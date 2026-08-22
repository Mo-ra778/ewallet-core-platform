<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Agent;
use App\Models\ExchangeRate;
use App\Models\Notification;
use App\Models\SystemSetting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Super Admin
        $admin = Admin::create([
            'username' => 'admin',
            'password_hash' => Hash::make('admin123'),
            'role' => 'super_admin',
        ]);

        // 2. Create Test Agent with Multi-Currency Balances
        $agent = Agent::create([
            'full_name' => 'وكيل صنعاء الرئيسي - محمد الإدريسي',
            'phone' => '777000111',
            'password_hash' => Hash::make('agent123'),
            'balance' => 2500000.00,
            'balance_yer' => 2500000.00,
            'balance_sar' => 15000.00,
            'balance_usd' => 5000.00,
            'balance_eur' => 2000.00,
            'status' => 'active',
        ]);

        // 3. Create Active User with Multi-Currency Balances
        $user1 = User::create([
            'full_name' => 'أحمد علي حسن',
            'phone' => '777111222',
            'email' => 'ahmed@example.com',
            'password_hash' => Hash::make('user123'),
            'balance' => 250000.00,
            'balance_yer' => 250000.00,
            'balance_sar' => 2500.00,
            'balance_usd' => 800.00,
            'balance_eur' => 300.00,
            'status' => 'active',
        ]);

        // 4. Create Another Active User (for transfer testing)
        $user2 = User::create([
            'full_name' => 'خالد عبد الله المنصوري',
            'phone' => '777222333',
            'email' => 'khaled@example.com',
            'password_hash' => Hash::make('user123'),
            'balance' => 100000.00,
            'balance_yer' => 100000.00,
            'balance_sar' => 1200.00,
            'balance_usd' => 450.00,
            'balance_eur' => 0.00,
            'status' => 'active',
        ]);

        // 5. Create Pending User (waiting for admin approval)
        $userPending = User::create([
            'full_name' => 'سالم محمد ناصر',
            'phone' => '777333444',
            'email' => 'salem@example.com',
            'password_hash' => Hash::make('user123'),
            'balance' => 0.00,
            'balance_yer' => 0.00,
            'balance_sar' => 0.00,
            'balance_usd' => 0.00,
            'balance_eur' => 0.00,
            'status' => 'pending',
        ]);

        // 6. Seed Exchange Rates Matrix
        $rates = [
            ['from' => 'SAR', 'to' => 'YER', 'rate' => 425.000000, 'buy' => 424.500000, 'sell' => 426.000000],
            ['from' => 'USD', 'to' => 'YER', 'rate' => 1600.000000, 'buy' => 1595.000000, 'sell' => 1605.000000],
            ['from' => 'EUR', 'to' => 'YER', 'rate' => 1750.000000, 'buy' => 1740.000000, 'sell' => 1760.000000],
            ['from' => 'USD', 'to' => 'SAR', 'rate' => 3.750000, 'buy' => 3.745000, 'sell' => 3.755000],
            ['from' => 'EUR', 'to' => 'USD', 'rate' => 1.090000, 'buy' => 1.085000, 'sell' => 1.095000],
            ['from' => 'EUR', 'to' => 'SAR', 'rate' => 4.100000, 'buy' => 4.080000, 'sell' => 4.120000],
        ];

        foreach ($rates as $r) {
            ExchangeRate::updateOrCreate(
                ['from_currency' => $r['from'], 'to_currency' => $r['to']],
                [
                    'rate' => $r['rate'],
                    'buy_rate' => $r['buy'],
                    'sell_rate' => $r['sell'],
                    'is_active' => true,
                ]
            );
        }

        // 7. Seed System Settings & Fees Configuration
        $settings = [
            // Fees
            ['key' => 'transfer_fee_percent', 'value' => '0.5', 'group' => 'fees', 'label' => 'نسبة رسوم التحويل (%)', 'description' => 'نسبة الخصم المئوية المفروضة على التحويلات المالية بين العملاء.'],
            ['key' => 'transfer_fee_fixed', 'value' => '0.00', 'group' => 'fees', 'label' => 'مبلغ رسم التحويل الثابت', 'description' => 'مبلغ ثابت إضافي يخصم مع كل عملية تحويل.'],
            ['key' => 'withdrawal_fee_percent', 'value' => '1.0', 'group' => 'fees', 'label' => 'نسبة رسوم السحب النقدي (%)', 'description' => 'نسبة الرسوم المفروضة عند سحب العميل للنقد من الوكيل.'],
            ['key' => 'agent_commission_percent', 'value' => '60.0', 'group' => 'fees', 'label' => 'حصة الوكيل من رسوم السحب (%)', 'description' => 'النسبة المئوية التي تذهب للوكيل كعمولة ربح من إجمالي رسوم السحب.'],
            ['key' => 'exchange_fee_percent', 'value' => '0.25', 'group' => 'fees', 'label' => 'عمولة صرف وتحويل العملات (%)', 'description' => 'النسبة المئوية التي تقتطع كرسوم خدمة عند تحويل العملات داخل المحفظة.'],
            
            // Operational Limits
            ['key' => 'min_transfer_amount', 'value' => '10.00', 'group' => 'limits', 'label' => 'الحد الأدنى للتحويل', 'description' => 'أقل مبلغ مسموح بتحويله في العملية الواحدة.'],
            ['key' => 'max_transfer_amount', 'value' => '5000000.00', 'group' => 'limits', 'label' => 'الحد الأقصى للتحويل', 'description' => 'أقصى مبلغ مسموح بتحويله في العملية الواحدة.'],
            ['key' => 'daily_transfer_limit', 'value' => '20000000.00', 'group' => 'limits', 'label' => 'السقف اليومي للتحويلات', 'description' => 'الحد الأقصى لمجموع تحويلات العميل خلال 24 ساعة.'],
            
            // General
            ['key' => 'app_name', 'value' => 'محفظتي الإلكترونية', 'group' => 'general', 'label' => 'اسم المنصة', 'description' => 'الاسم التجاري للمنظومة المالية.'],
            ['key' => 'maintenance_mode', 'value' => '0', 'group' => 'general', 'label' => 'وضع الصيانة', 'description' => 'إيقاف العمليات مؤقتاً لأغراض الترقية أو الجرد.'],
        ];

        foreach ($settings as $s) {
            SystemSetting::set($s['key'], $s['value'], $s['group'], $s['label'], $s['description']);
        }

        // 8. Initial Seed Transactions
        Transaction::create([
            'user_id' => $user1->id,
            'agent_id' => $agent->id,
            'type' => 'deposit',
            'amount' => 250000.00,
            'fee' => 0.00,
            'commission' => 0.00,
            'currency' => 'YER',
            'status' => 'completed',
            'description' => 'إيداع نقدي يمني عبر الوكيل',
        ]);

        Transaction::create([
            'user_id' => $user1->id,
            'agent_id' => $agent->id,
            'type' => 'deposit',
            'amount' => 2500.00,
            'fee' => 0.00,
            'commission' => 0.00,
            'currency' => 'SAR',
            'status' => 'completed',
            'description' => 'إيداع نقدي بالريال السعودي',
        ]);

        Transaction::create([
            'user_id' => $user1->id,
            'agent_id' => $agent->id,
            'type' => 'deposit',
            'amount' => 800.00,
            'fee' => 0.00,
            'commission' => 0.00,
            'currency' => 'USD',
            'status' => 'completed',
            'description' => 'إيداع نقدي بالدولار الأمريكي',
        ]);

        // 9. Initial Seed Notifications
        Notification::create([
            'recipient_id' => $user1->id,
            'recipient_type' => 'user',
            'title' => 'مرحباً بك في المحفظة الإلكترونية',
            'message' => 'تم تفعيل حسابك بنجاح وجاهز للاستخدام وتحويل وصرف العملات.',
            'type' => 'alert',
            'is_read' => false,
        ]);
    }
}
