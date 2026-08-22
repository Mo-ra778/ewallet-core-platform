<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    /**
     * Get user balance and wallet summary
     */
    public function getBalance(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'تم استرجاع الرصيد بنجاح.',
            'data' => [
                'balance' => (float) $user->balance,
                'currency' => 'YER',
                'status' => $user->status,
                'is_active' => $user->isActive(),
            ],
        ]);
    }

    /**
     * Transfer money to another user (Requires active account, wrapped in DB::transaction)
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
        $currency = $request->input('currency', 'SAR');
        $description = $request->input('description') ?? 'تحويل رصيد';

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

        // Check sender balance
        if ((float) $sender->balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'رصيدك الحالي غير كافٍ لإتمام عملية التحويل.',
                'data' => [
                    'current_balance' => (float) $sender->balance,
                    'required_amount' => $amount,
                    'currency' => $currency,
                ],
            ], 422);
        }

        // Execute financial transaction inside DB::transaction for ACID compliance
        $transaction = DB::transaction(function () use ($sender, $receiver, $amount, $currency, $description) {
            // Deduct from sender
            $sender->decrement('balance', $amount);

            // Add to receiver
            $receiver->increment('balance', $amount);

            // Record transaction for sender
            $senderTx = Transaction::create([
                'user_id' => $sender->id,
                'type' => 'transfer',
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "تحويل إلى {$receiver->full_name} ({$receiver->phone}) - {$description}",
            ]);

            // Record transaction for receiver
            Transaction::create([
                'user_id' => $receiver->id,
                'type' => 'transfer',
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "استلام تحويل من {$sender->full_name} ({$sender->phone}) - {$description}",
            ]);

            // Notify Sender
            Notification::create([
                'recipient_id' => $sender->id,
                'recipient_type' => 'user',
                'title' => 'عملية تحويل صادرة',
                'message' => "تم تحويل {$amount} {$currency} بنجاح إلى {$receiver->full_name}.",
                'type' => 'transaction',
                'is_read' => false,
            ]);

            // Notify Receiver
            Notification::create([
                'recipient_id' => $receiver->id,
                'recipient_type' => 'user',
                'title' => 'عملية تحويل واردة',
                'message' => "وصلك مبلغ {$amount} {$currency} من {$sender->full_name}.",
                'type' => 'transaction',
                'is_read' => false,
            ]);

            return $senderTx;
        });

        // Refresh sender balance
        $sender->refresh();

        return response()->json([
            'success' => true,
            'message' => 'تم التحويل بنجاح.',
            'data' => [
                'transaction_id' => $transaction->id,
                'amount' => $amount,
                'currency' => $currency,
                'recipient_name' => $receiver->full_name,
                'recipient_phone' => $receiver->phone,
                'new_balance' => (float) $sender->balance,
                'created_at' => $transaction->created_at,
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

        $transactions = Transaction::where('user_id', $user->id)
            ->with(['agent:id,full_name,phone', 'admin:id,username'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'تم جلب سجل العمليات بنجاح.',
            'data' => $transactions,
        ]);
    }
}
