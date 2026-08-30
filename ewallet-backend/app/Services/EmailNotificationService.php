<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService
{
    /**
     * Send email verification OTP to a newly registered user
     */
    public static function sendEmailVerificationOtp(User $user, string $otpCode): bool
    {
        if (empty($user->email)) {
            return false;
        }

        $subject = '🔐 رمز تفعيل حسابك في المحفظة الإلكترونية';
        $htmlContent = self::buildEmailTemplate(
            title: 'تأكيد وتفعيل البريد الإلكتروني',
            greeting: "مرحباً بك يا {$user->full_name}",
            leadText: 'شكراً لتسجيلك في منصة المحفظة الإلكترونية. يرجى استخدام رمز التحقق أدناه لتأكيد بريدك الإلكتروني وإكمال إنشاء الحساب:',
            otpCode: $otpCode,
            validityMinutes: 10,
            details: [
                'الاسم الكامل' => $user->full_name,
                'رقم الهاتف' => $user->phone,
                'البريد الإلكتروني' => $user->email,
                'وقت الطلب' => now()->format('Y-m-d H:i:s'),
            ],
            securityNotice: 'إذا لم تكن قد طلبت إنشاء هذا الحساب، يرجى تجاهل هذه الرسالة أو التواصل مع الدعم الفني فوراً.'
        );

        return self::sendRawEmail($user->email, $subject, $htmlContent);
    }

    /**
     * Send Password Reset OTP to the user via email
     */
    public static function sendPasswordResetOtp(User $user, string $otpCode): bool
    {
        if (empty($user->email)) {
            return false;
        }

        $subject = '🔐 رمز استعادة كلمة المرور - محفظتي';
        $htmlContent = self::buildEmailTemplate(
            title: 'طلب استعادة كلمة المرور',
            greeting: "عزيزي العميل {$user->full_name}",
            leadText: 'تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك في المحفظة الإلكترونية. استخدم رمز الأمان أدناه لإتمام تعيين كلمة المرور الجديدة:',
            otpCode: $otpCode,
            validityMinutes: 10,
            details: [
                'اسم الحساب' => $user->full_name,
                'رقم الهاتف' => $user->phone,
                'البريد الإلكتروني' => $user->email,
                'وقت الطلب' => now()->format('Y-m-d H:i:s'),
            ],
            securityNotice: '⚠️ تنبيه أمني: لا تشارك هذا الرمز مع أي شخص كائناً من كان. إذا لم تقم بطلب إعادة تعيين كلمة المرور، يرجى تغيير كلمة مرورك فوراً أو التواصل مع الدعم الفني.'
        );

        return self::sendRawEmail($user->email, $subject, $htmlContent);
    }

    /**
     * Send Cash-Out (Withdrawal) OTP to the user via email
     */
    public static function sendWithdrawalOtp(
        User $user,
        string $otpCode,
        float $amount,
        string $currency = 'YER',
        ?string $agentName = null
    ): bool {
        if (empty($user->email)) {
            return false;
        }

        $formattedAmount = number_format($amount, 2);
        $subject = "⚠️ رمز تأكيد السحب النقدي ({$formattedAmount} {$currency})";

        $htmlContent = self::buildEmailTemplate(
            title: 'طلب سحب نقدي (Cash-Out OTP)',
            greeting: "عزيزي العميل {$user->full_name}",
            leadText: "تم تقديم طلب سحب نقدي من محفظتك بقيمة <strong style=\"color: #0F766E;\">{$formattedAmount} {$currency}</strong>. استخدم رمز الأمان أدناه لتأكيد العملية مع الوكيل المعتمد:",
            otpCode: $otpCode,
            validityMinutes: 5,
            details: [
                'المبلغ المراد سحبه' => "{$formattedAmount} {$currency}",
                'الوكيل المنفّذ' => $agentName ?? 'مركز وكيل معتمد',
                'رقم هاتف العميل' => $user->phone,
                'وقت الطلب' => now()->format('Y-m-d H:i:s'),
            ],
            securityNotice: '⚠️ تنبيه أمني مشدد: لا تشارك هذا الرمز مع أي شخص إلا الوكيل المعتمد وجهاً لوجه بعد استلام كامل المبلغ نقداً.'
        );

        return self::sendRawEmail($user->email, $subject, $htmlContent);
    }

    /**
     * Send email via Laravel Mail with fail-safe error handling
     */
    private static function sendRawEmail(string $toEmail, string $subject, string $htmlContent): bool
    {
        try {
            Mail::html($htmlContent, function ($message) use ($toEmail, $subject) {
                $message->to($toEmail)
                    ->subject($subject);
            });

            Log::info("FinTech Email dispatched successfully to: {$toEmail} with subject: [{$subject}]");
            return true;
        } catch (\Throwable $e) {
            Log::warning("FinTech Email dispatch fallback/error to {$toEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Build modern responsive HTML FinTech email template
     */
    private static function buildEmailTemplate(
        string $title,
        string $greeting,
        string $leadText,
        string $otpCode,
        int $validityMinutes,
        array $details = [],
        string $securityNotice = ''
    ): string {
        $detailsRows = '';
        foreach ($details as $label => $value) {
            $detailsRows .= "
                <tr>
                    <td style=\"padding: 8px 12px; color: #64748B; font-size: 13px; border-bottom: 1px solid #F1F5F9;\">{$label}</td>
                    <td style=\"padding: 8px 12px; color: #0F172A; font-weight: bold; font-size: 13px; text-align: left; border-bottom: 1px solid #F1F5F9; direction: ltr;\">{$value}</td>
                </tr>
            ";
        }

        return "
        <!DOCTYPE html>
        <html lang=\"ar\" dir=\"rtl\">
        <head>
            <meta charset=\"UTF-8\">
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
            <title>{$title}</title>
        </head>
        <body style=\"margin: 0; padding: 20px; background-color: #F8FAFC; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #0F172A;\">
            <table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"max-width: 560px; background-color: #FFFFFF; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #E2E8F0;\">
                <!-- Header -->
                <tr>
                    <td style=\"padding: 24px 30px; background: linear-gradient(135deg, #0F766E 0%, #115E59 100%); text-align: center; color: #FFFFFF;\">
                        <div style=\"font-size: 20px; font-weight: bold; letter-spacing: 0.5px;\">🏦 محفظتي للخدمات النقدية والمصرفية</div>
                        <div style=\"font-size: 12px; color: #CCFBF1; margin-top: 4px;\">E-Wallet Financial Platform</div>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style=\"padding: 30px;\">
                        <h2 style=\"margin: 0 0 12px 0; color: #0F172A; font-size: 18px; font-weight: 700;\">{$greeting}</h2>
                        <p style=\"margin: 0 0 20px 0; color: #475569; font-size: 14px; line-height: 1.6;\">{$leadText}</p>

                        <!-- OTP Box -->
                        <div style=\"background-color: #F0FDFA; border: 2px dashed #0D9488; border-radius: 12px; padding: 20px; text-align: center; margin: 24px 0;\">
                            <div style=\"font-size: 12px; font-weight: 600; color: #0F766E; margin-bottom: 6px; text-transform: uppercase;\">رمز الأمان والتحقق (OTP)</div>
                            <div style=\"font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #0F766E; font-family: monospace;\">{$otpCode}</div>
                            <div style=\"font-size: 11px; color: #64748B; margin-top: 8px;\">⏳ الرمز صالح لمدة {$validityMinutes} دقائق فقط</div>
                        </div>

                        <!-- Details Table -->
                        " . (!empty($detailsRows) ? "
                        <div style=\"margin: 20px 0;\">
                            <div style=\"font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px;\">تفاصيل الطلب:</div>
                            <table width=\"100%\" style=\"border-collapse: collapse; background-color: #F8FAFC; border-radius: 8px; overflow: hidden;\">
                                {$detailsRows}
                            </table>
                        </div>
                        " : "") . "

                        <!-- Security Notice -->
                        " . (!empty($securityNotice) ? "
                        <div style=\"background-color: #FFFBEB; border-right: 4px solid #F59E0B; padding: 12px 14px; border-radius: 6px; margin-top: 20px;\">
                            <p style=\"margin: 0; font-size: 12px; color: #92400E; line-height: 1.5;\">{$securityNotice}</p>
                        </div>
                        " : "") . "
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style=\"padding: 16px 30px; background-color: #F8FAFC; border-top: 1px solid #E2E8F0; text-align: center; color: #94A3B8; font-size: 11px;\">
                        هذه رسالة آلية تم إرسالها من منصة المحفظة الإلكترونية المشفرة. يرجى عدم الرد على هذه الرسالة.<br>
                        جميع الحقوق محفوظة &copy; " . date('Y') . "
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";
    }
}
