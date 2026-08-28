<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use App\Models\Notification;
use App\Models\SystemSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FeeService;
use App\Services\PushNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    /**
     * Get user balance across all supported currencies
     */
    public function getBalance(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'تم استرجاع الأرصدة بنجاح.',
            'data' => [
                'primary_balance' => (float) $user->getCurrencyBalance('YER'),
                'primary_currency' => 'YER',
                'balances' => $user->getAllBalances(),
                'status' => $user->status,
                'is_active' => $user->isActive(),
            ],
        ]);
    }

    /**
     * Transfer money to another user in specified currency with automated fee calculation
     */
    public function transfer(Request $request): JsonResponse
    {
        /** @var User $sender */
        $sender = $request->user();

        $validator = Validator::make($request->all(), [
            'receiver_phone' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string|in:SAR,YER,USD,EUR',
            'description' => 'nullable|string|max:255',
        ], [
            'receiver_phone.required' => 'رقم هاتف المستلم مطلوب.',
            'amount.required' => 'المبلغ مطلوب.',
            'amount.numeric' => 'المبلغ يجب أن يكون رقماً صحيحاً.',
            'amount.min' => 'أقل مبلغ للتحويل هو 1.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors(),
            ], 422);
        }

        $receiverPhone = $request->input('receiver_phone');
        $amount = (float) $request->input('amount');
        $currency = strtoupper($request->input('currency', 'YER'));
        $description = $request->input('description') ?? 'تحويل رصيد';

        // Check operational limits
        $minLimit = SystemSetting::getFloat('min_transfer_amount', 1.0);
        $maxLimit = SystemSetting::getFloat('max_transfer_amount', 10000000.0);

        if ($amount < $minLimit) {
            return response()->json([
                'success' => false,
                'message' => "أقل مبلغ مسموح بتحويله هو " . number_format($minLimit, 2) . " {$currency}.",
            ], 422);
        }

        if ($amount > $maxLimit) {
            return response()->json([
                'success' => false,
                'message' => "الحد الأقصى للتحويل في العملية الواحدة هو " . number_format($maxLimit, 2) . " {$currency}.",
            ], 422);
        }

        // Calculate transfer fee
        $fee = FeeService::calculateTransferFee($amount);
        $totalDebit = $amount + $fee;

        // Cannot transfer to self
        if ($sender->phone === $receiverPhone) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك تحويل الأموال إلى حسابك الشخصي.',
                'data' => null,
            ], 422);
        }

        // Find receiver
        $receiver = User::where('phone', $receiverPhone)->first();

        if (!$receiver) {
            return response()->json([
                'success' => false,
                'message' => 'المستلم غير مسجل في النظام.',
                'data' => null,
            ], 404);
        }

        if ($receiver->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'حساب المستلم غير نشط حالياً ولا يمكنه استقبال الأموال.',
                'data' => null,
            ], 422);
        }

        // Check sender balance (must cover amount + fee)
        if (!$sender->hasSufficientBalance($totalDebit, $currency)) {
            return response()->json([
                'success' => false,
                'message' => "رصيدك الحالي بعملة {$currency} لا يكفي لتغطية المبلغ المطلوب مع رسوم التحويل ({$totalDebit} {$currency}).",
                'data' => [
                    'current_balance' => $sender->getCurrencyBalance($currency),
                    'transfer_amount' => $amount,
                    'fee' => $fee,
                    'total_required' => $totalDebit,
                    'currency' => $currency,
                ],
            ], 422);
        }

        // Execute financial transaction inside DB::transaction for ACID compliance
        $transaction = DB::transaction(function () use ($sender, $receiver, $amount, $fee, $totalDebit, $currency, $description) {
            // Deduct from sender currency (amount + fee)
            $sender->decrementCurrency($currency, $totalDebit);

            // Add amount to receiver currency
            $receiver->incrementCurrency($currency, $amount);

            // Record transaction for sender (includes fee)
            $senderTx = Transaction::create([
                'user_id' => $sender->id,
                'type' => 'transfer',
                'amount' => $amount,
                'fee' => $fee,
                'commission' => 0.00,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "تحويل إلى {$receiver->full_name} ({$receiver->phone}) - {$description}" . ($fee > 0 ? " (رسوم: {$fee} {$currency})" : ''),
            ]);

            // Record transaction for receiver
            Transaction::create([
                'user_id' => $receiver->id,
                'type' => 'transfer',
                'amount' => $amount,
                'fee' => 0.00,
                'commission' => 0.00,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "استلام تحويل من {$sender->full_name} ({$sender->phone}) - {$description}",
            ]);

            // Notify Sender
            PushNotificationService::sendToUser(
                user: $sender,
                title: '📤 عملية تحويل صادرة',
                message: "تم تحويل " . number_format($amount, 2) . " {$currency} بنجاح إلى {$receiver->full_name}" . ($fee > 0 ? " (رسوم: " . number_format($fee, 2) . " {$currency})" : '') . ".",
                data: ['type' => 'transfer', 'direction' => 'outgoing', 'transaction_id' => $senderTx->id],
                type: 'transaction'
            );

            // Notify Receiver
            PushNotificationService::sendToUser(
                user: $receiver,
                title: '📥 عملية تحويل واردة',
                message: "وصلك مبلغ " . number_format($amount, 2) . " {$currency} من {$sender->full_name}.",
                data: ['type' => 'transfer', 'direction' => 'incoming', 'amount' => $amount, 'currency' => $currency],
                type: 'transaction'
            );

            return $senderTx;
        });

        $sender->refresh();

        return response()->json([
            'success' => true,
            'message' => 'تم التحويل بنجاح.',
            'data' => [
                'transaction_id' => $transaction->id,
                'amount' => $amount,
                'fee' => $fee,
                'currency' => $currency,
                'recipient_name' => $receiver->full_name,
                'recipient_phone' => $receiver->phone,
                'new_balance' => $sender->getCurrencyBalance($currency),
                'all_balances' => $sender->getAllBalances(),
                'created_at' => $transaction->created_at,
            ],
        ]);
    }

    /**
     * Get All Active Exchange Rates
     */
    public function getExchangeRates(): JsonResponse
    {
        $rates = ExchangeRate::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'message' => 'تم استرجاع أسعار الصرف الحالية بنجاح.',
            'data' => [
                'rates' => $rates,
                'exchange_fee_percent' => SystemSetting::getFloat('exchange_fee_percent', 0.25),
            ],
        ]);
    }

    /**
     * Preview Currency Exchange (Calculate rate, fee, and net received before confirming)
     */
    public function previewExchange(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'from_currency' => 'required|string|max:10',
            'to_currency' => 'required|string|max:10|different:from_currency',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors(),
            ], 422);
        }

        $from = strtoupper(trim($request->input('from_currency')));
        $to = strtoupper(trim($request->input('to_currency')));
        $amount = (float) $request->input('amount');

        $pair = ExchangeRate::getPair($from, $to);
        $rate = ExchangeRate::getRate($from, $to);

        if ($rate === null) {
            return response()->json([
                'success' => false,
                'message' => "سعر الصرف بين {$from} و {$to} غير متوفر أو غير مفعّل حالياً.",
            ], 422);
        }

        // Check custom min/max limits if defined for this pair
        if ($pair && $pair->min_exchange_amount && $amount < (float) $pair->min_exchange_amount) {
            return response()->json([
                'success' => false,
                'message' => "الحد الأدنى لمصارفة هذا الزوج هو " . number_format($pair->min_exchange_amount, 2) . " {$from}.",
            ], 422);
        }

        if ($pair && $pair->max_exchange_amount && $amount > (float) $pair->max_exchange_amount) {
            return response()->json([
                'success' => false,
                'message' => "الحد الأقصى لمصارفة هذا الزوج في العملية هو " . number_format($pair->max_exchange_amount, 2) . " {$from}.",
            ], 422);
        }

        $convertedGross = round($amount * $rate, 2);
        $fee = FeeService::calculateExchangeFee($convertedGross, $from, $to);
        $netReceived = round($convertedGross - $fee, 2);
        $feePercent = ExchangeRate::getFeePercent($from, $to);

        return response()->json([
            'success' => true,
            'message' => 'معاينة الصرف جاهزة.',
            'data' => [
                'from_currency' => $from,
                'to_currency' => $to,
                'sell_amount' => $amount,
                'exchange_rate' => $rate,
                'gross_converted' => $convertedGross,
                'fee' => $fee,
                'fee_percent' => $feePercent,
                'fee_currency' => $to,
                'net_received' => $netReceived,
            ],
        ]);
    }

    /**
     * Execute Currency Exchange (Swap balances between wallets inside user account)
     */
    public function exchange(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'from_currency' => 'required|string|max:10',
            'to_currency' => 'required|string|max:10|different:from_currency',
            'amount' => 'required|numeric|min:0.01',
        ], [
            'from_currency.required' => 'يرجى تحديد العملة المراد تحويلها.',
            'to_currency.required' => 'يرجى تحديد العملة المستهدفة.',
            'to_currency.different' => 'يجب أن تكون العملة المستهدفة مختلفة عن العملة المصدر.',
            'amount.required' => 'المبلغ مطلوب.',
            'amount.min' => 'يجب أن يكون المبلغ أكبر من 0.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors(),
            ], 422);
        }

        $from = strtoupper(trim($request->input('from_currency')));
        $to = strtoupper(trim($request->input('to_currency')));
        $amount = (float) $request->input('amount');

        // Check user balance in source currency
        if (!$user->hasSufficientBalance($amount, $from)) {
            return response()->json([
                'success' => false,
                'message' => "رصيدك الحالي بعملة {$from} (" . number_format($user->getCurrencyBalance($from), 2) . ") غير كافٍ لإتمام عملية المصارفة.",
            ], 422);
        }

        // Get Exchange Rate and Pair Rules
        $pair = ExchangeRate::getPair($from, $to);
        $rate = ExchangeRate::getRate($from, $to);

        if ($rate === null) {
            return response()->json([
                'success' => false,
                'message' => "سعر الصرف بين {$from} و {$to} غير متاح في الوقت الحالي.",
            ], 422);
        }

        // Check Pair Limits
        if ($pair && $pair->min_exchange_amount && $amount < (float) $pair->min_exchange_amount) {
            return response()->json([
                'success' => false,
                'message' => "الحد الأدنى لمصارفة هذا الزوج هو " . number_format($pair->min_exchange_amount, 2) . " {$from}.",
            ], 422);
        }

        if ($pair && $pair->max_exchange_amount && $amount > (float) $pair->max_exchange_amount) {
            return response()->json([
                'success' => false,
                'message' => "الحد الأقصى لمصارفة هذا الزوج في العملية هو " . number_format($pair->max_exchange_amount, 2) . " {$from}.",
            ], 422);
        }

        $convertedGross = round($amount * $rate, 2);
        $fee = FeeService::calculateExchangeFee($convertedGross, $from, $to);
        $netReceived = round($convertedGross - $fee, 2);
        $feePercent = ExchangeRate::getFeePercent($from, $to);

        // Execute currency swap atomically inside DB::transaction
        $transaction = DB::transaction(function () use ($user, $from, $to, $amount, $rate, $fee, $feePercent, $netReceived) {
            // Deduct source currency
            $user->decrementCurrency($from, $amount);

            // Add target currency (net received after fee)
            $user->incrementCurrency($to, $netReceived);

            // Record Exchange Transaction
            $tx = Transaction::create([
                'user_id' => $user->id,
                'type' => 'exchange',
                'amount' => $netReceived,
                'fee' => $fee,
                'commission' => 0.00,
                'currency' => $to,
                'status' => 'completed',
                'description' => "مصارفة داخلية: تحويل " . number_format($amount, 2) . " {$from} إلى " . number_format($netReceived, 2) . " {$to} (سعر الصرف: {$rate})" . ($fee > 0 ? " - رسوم ({$feePercent}%): {$fee} {$to}" : ''),
            ]);

            // Notify user
            PushNotificationService::sendToUser(
                user: $user,
                title: '💱 عملية مصارفة عملات ناجحة',
                message: "تم صرف " . number_format($amount, 2) . " {$from} واستلام " . number_format($netReceived, 2) . " {$to} في محفظتك بنجاح.",
                data: ['type' => 'exchange', 'from' => $from, 'to' => $to, 'amount' => $amount, 'received' => $netReceived],
                type: 'transaction'
            );

            return $tx;
        });

        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => 'تم صرف وتحويل العملات بنجاح.',
            'data' => [
                'transaction_id' => $transaction->id,
                'sold_amount' => $amount,
                'sold_currency' => $from,
                'received_amount' => $netReceived,
                'received_currency' => $to,
                'exchange_rate' => $rate,
                'fee' => $fee,
                'fee_percent' => $feePercent,
                'balances' => $user->getAllBalances(),
            ],
        ]);
    }

    /**
     * Get transaction history for current user
     */
    public function transactions(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $query = Transaction::where('user_id', $user->id)
            ->with(['agent:id,full_name,phone', 'admin:id,username']);

        if ($currency = $request->query('currency')) {
            $query->where('currency', strtoupper($currency));
        }

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'تم جلب سجل العمليات بنجاح.',
            'data' => $transactions,
        ]);
    }

    /**
     * Preview Cash Remittance (Calculate fees and total required before sending)
     */
    public function previewRemittance(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string|in:SAR,YER,USD,EUR',
        ], [
            'amount.required' => 'يرجى إدخال مبلغ الحوالة.',
            'amount.min' => 'أقل مبلغ للحوالة هو 1.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors(),
            ], 422);
        }

        $amount = (float) $request->input('amount');
        $currency = strtoupper($request->input('currency', 'YER'));

        $feeData = FeeService::calculateRemittanceFee($amount);
        $totalRequired = $amount + $feeData['fee'];

        return response()->json([
            'success' => true,
            'message' => 'معاينة رسوم الحوالة النقدية جاهزة.',
            'data' => [
                'amount' => $amount,
                'currency' => $currency,
                'fee' => $feeData['fee'],
                'fee_percent' => $feeData['fee_percent'],
                'total_debit' => $totalRequired,
            ],
        ]);
    }

    /**
     * Send Cash Remittance to an Unregistered Person
     */
    public function sendRemittance(Request $request): JsonResponse
    {
        /** @var User $sender */
        $sender = $request->user();

        $validator = Validator::make($request->all(), [
            'recipient_name' => 'required|string|min:6|max:150',
            'recipient_phone' => 'required|string|min:7|max:30',
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string|in:SAR,YER,USD,EUR',
            'notes' => 'nullable|string|max:255',
        ], [
            'recipient_name.required' => 'اسم المستلم الرباعي مطلوب لإصدار الحوالة.',
            'recipient_name.min' => 'يرجى إدخال اسم المستلم كاملاً (الاسم واللقب).',
            'recipient_phone.required' => 'رقم هاتف المستلم مطلوب.',
            'amount.required' => 'مبلغ الحوالة مطلوب.',
            'amount.min' => 'أقل مبلغ للحوالة هو 1.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors(),
            ], 422);
        }

        $recipientName = trim($request->input('recipient_name'));
        $recipientPhone = trim($request->input('recipient_phone'));
        $amount = (float) $request->input('amount');
        $currency = strtoupper($request->input('currency', 'YER'));
        $notes = $request->input('notes') ?? 'حوالة نقدية كاش';

        // Check fee
        $feeData = FeeService::calculateRemittanceFee($amount);
        $fee = $feeData['fee'];
        $agentCommission = $feeData['agent_commission'];
        $totalDebit = $amount + $fee;

        // Check sender balance
        if (!$sender->hasSufficientBalance($totalDebit, $currency)) {
            return response()->json([
                'success' => false,
                'message' => "رصيدك الحالي بعملة {$currency} لا يكفي لتغطية مبلغ الحوالة والرسوم الإجمالية ({$totalDebit} {$currency}).",
                'data' => [
                    'current_balance' => $sender->getCurrencyBalance($currency),
                    'remittance_amount' => $amount,
                    'fee' => $fee,
                    'total_required' => $totalDebit,
                    'currency' => $currency,
                ],
            ], 422);
        }

        // Execute inside DB::transaction
        $remittance = DB::transaction(function () use ($sender, $recipientName, $recipientPhone, $amount, $fee, $agentCommission, $totalDebit, $currency, $notes) {
            // Deduct from sender balance
            $sender->decrementCurrency($currency, $totalDebit);

            // Generate Codes
            $remittanceCode = \App\Models\Remittance::generateUniqueCode();
            $pinCode = \App\Models\Remittance::generatePinCode();

            // Create Remittance Record
            $rem = \App\Models\Remittance::create([
                'remittance_code' => $remittanceCode,
                'pin_code' => $pinCode,
                'sender_id' => $sender->id,
                'sender_type' => 'user',
                'sender_name' => $sender->full_name,
                'sender_phone' => $sender->phone,
                'recipient_name' => $recipientName,
                'recipient_phone' => $recipientPhone,
                'amount' => $amount,
                'fee' => $fee,
                'agent_commission' => $agentCommission,
                'currency' => $currency,
                'status' => 'pending',
                'notes' => $notes,
            ]);

            // Record Transaction in Ledger
            Transaction::create([
                'user_id' => $sender->id,
                'type' => 'transfer',
                'amount' => $amount,
                'fee' => $fee,
                'commission' => 0.00,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "إصدار حوالة نقدية إلى {$recipientName} ({$recipientPhone}) - رقم الحوالة: {$remittanceCode}" . ($fee > 0 ? " (رسوم: {$fee} {$currency})" : ''),
            ]);

            // Create In-App Notification & Push Notification with the Remittance details
            PushNotificationService::sendToUser(
                user: $sender,
                title: '📄 تم إصدار الحوالة النقدية بنجاح',
                message: "تم إصدار حوالة بمبلغ " . number_format($amount, 2) . " {$currency} إلى {$recipientName}. رقم الحوالة: [ {$remittanceCode} ]، الكود السري: [ {$pinCode} ].",
                data: ['type' => 'remittance', 'remittance_code' => $remittanceCode, 'amount' => $amount, 'currency' => $currency],
                type: 'transaction'
            );

            return $rem;
        });

        $sender->refresh();

        return response()->json([
            'success' => true,
            'message' => 'تم إصدار الحوالة النقدية بنجاح. يرجى تزويد المستلم برقم الحوالة والكود السري.',
            'data' => [
                'remittance_id' => $remittance->id,
                'remittance_code' => $remittance->remittance_code,
                'pin_code' => $remittance->pin_code,
                'amount' => $remittance->amount,
                'fee' => $remittance->fee,
                'currency' => $remittance->currency,
                'recipient_name' => $remittance->recipient_name,
                'recipient_phone' => $remittance->recipient_phone,
                'status' => $remittance->status,
                'new_balance' => $sender->getCurrencyBalance($currency),
                'created_at' => $remittance->created_at,
            ],
        ], 201);
    }

    /**
     * Get All Remittances Sent By Current User
     */
    public function myRemittances(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $query = \App\Models\Remittance::where('sender_id', $user->id)
            ->with('payingAgent:id,full_name,phone');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $remittances = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'تم استرجاع الحوالات النقدية بنجاح.',
            'data' => $remittances,
        ]);
    }

    /**
     * Cancel an Unclaimed/Pending Remittance and Refund Amount to Sender
     */
    public function cancelRemittance(string $id, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $remittance = \App\Models\Remittance::where('id', $id)
            ->where('sender_id', $user->id)
            ->first();

        if (!$remittance) {
            return response()->json([
                'success' => false,
                'message' => 'الحوالة غير موجودة.',
                'data' => null,
            ], 404);
        }

        if ($remittance->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => "لا يمكن إلغاء هذه الحوالة لأنها في حالة ({$remittance->status}). الحوالات المعلقة فقط يمكن إلغاؤها.",
                'data' => null,
            ], 422);
        }

        DB::transaction(function () use ($remittance, $user) {
            // Update remittance status
            $remittance->update([
                'status' => 'cancelled',
            ]);

            // Refund the principal amount back to user's currency vault
            $user->incrementCurrency($remittance->currency, (float) $remittance->amount);

            // Record transaction for refund
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'transfer',
                'amount' => $remittance->amount,
                'fee' => 0.00,
                'commission' => 0.00,
                'currency' => $remittance->currency,
                'status' => 'completed',
                'description' => "استرجاع مبلغ حوالة ملغاة (رقم: {$remittance->remittance_code}) للمستلم {$remittance->recipient_name}",
            ]);

            // In-app Notification & Push
            PushNotificationService::sendToUser(
                user: $user,
                title: '↩️ تم إلغاء الحوالة واسترجاع المبلغ',
                message: "تم إلغاء الحوالة رقم {$remittance->remittance_code} واسترجاع مبلغ " . number_format($remittance->amount, 2) . " {$remittance->currency} إلى محفظتك بنجاح.",
                data: ['type' => 'remittance_cancelled', 'remittance_code' => $remittance->remittance_code],
                type: 'transaction'
            );
        });

        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الحوالة واسترجاع المبلغ إلى محفظتك بنجاح.',
            'data' => [
                'remittance_id' => $remittance->id,
                'status' => 'cancelled',
                'refunded_amount' => $remittance->amount,
                'currency' => $remittance->currency,
                'current_balance' => $user->getCurrencyBalance($remittance->currency),
            ],
        ]);
    }
}

