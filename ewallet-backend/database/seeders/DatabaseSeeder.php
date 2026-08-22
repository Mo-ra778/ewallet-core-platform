<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Agent;
use App\Models\Notification;
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

        // 2. Create Test Agent
        $agent = Agent::create([
            'full_name' => 'وكيل صنعاء الرئيسي - محمد',
            'phone' => '777000111',
            'password_hash' => Hash::make('agent123'),
            'balance' => 500000.00,
            'status' => 'active',
        ]);

        // 3. Create Active User
        $user1 = User::create([
            'full_name' => 'أحمد علي حسن',
            'phone' => '777111222',
            'email' => 'ahmed@example.com',
            'password_hash' => Hash::make('user123'),
            'balance' => 25000.00,
            'status' => 'active',
        ]);

        // 4. Create Another Active User (for transfer testing)
        $user2 = User::create([
            'full_name' => 'خالد عبد الله',
            'phone' => '777222333',
            'email' => 'khaled@example.com',
            'password_hash' => Hash::make('user123'),
            'balance' => 15000.00,
            'status' => 'active',
        ]);

        // 5. Create Pending User (waiting for admin approval)
        $userPending = User::create([
            'full_name' => 'سالم محمد ناصر',
            'phone' => '777333444',
            'email' => 'salem@example.com',
            'password_hash' => Hash::make('user123'),
            'balance' => 0.00,
            'status' => 'pending',
        ]);

        // 6. Initial Seed Transactions
        Transaction::create([
            'user_id' => $user1->id,
            'agent_id' => $agent->id,
            'type' => 'deposit',
            'amount' => 25000.00,
            'currency' => 'YER',
            'status' => 'completed',
            'description' => 'إيداع نقدي أولي عبر الوكيل',
        ]);

        // 7. Initial Seed Notifications
        Notification::create([
            'recipient_id' => $user1->id,
            'recipient_type' => 'user',
            'title' => 'مرحباً بك في المحفظة الإلكترونية',
            'message' => 'تم تفعيل حسابك بنجاح وجاهز للاستخدام.',
            'type' => 'alert',
            'is_read' => false,
        ]);
    }
}
