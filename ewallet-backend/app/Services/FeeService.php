<?php

namespace App\Services;

use App\Models\ExchangeRate;
use App\Models\SystemSetting;

class FeeService
{
    /**
     * Calculate transfer fee for a given amount
     */
    public static function calculateTransferFee(float $amount): float
    {
        $percent = SystemSetting::getFloat('transfer_fee_percent', 0.0);
        $fixed = SystemSetting::getFloat('transfer_fee_fixed', 0.0);

        $fee = ($amount * ($percent / 100)) + $fixed;
        return round($fee, 2);
    }

    /**
     * Calculate withdrawal fee and agent commission share
     * Returns: ['fee' => total_fee, 'agent_commission' => agent_share, 'platform_net' => platform_share]
     */
    public static function calculateWithdrawalFee(float $amount): array
    {
        $percent = SystemSetting::getFloat('withdrawal_fee_percent', 0.0);
        $agentSharePercent = SystemSetting::getFloat('agent_commission_percent', 0.0);

        $totalFee = round($amount * ($percent / 100), 2);
        $agentCommission = round($totalFee * ($agentSharePercent / 100), 2);
        $platformNet = round($totalFee - $agentCommission, 2);

        return [
            'fee' => $totalFee,
            'agent_commission' => $agentCommission,
            'platform_net' => $platformNet,
        ];
    }

    /**
     * Calculate exchange fee dynamically based on specific pair custom commission or global setting
     */
    public static function calculateExchangeFee(float $amount, ?string $from = null, ?string $to = null): float
    {
        if ($from && $to) {
            $percent = ExchangeRate::getFeePercent($from, $to);
        } else {
            $percent = SystemSetting::getFloat('exchange_fee_percent', 0.25);
        }

        return round($amount * ($percent / 100), 2);
    }

    /**
     * Calculate cash remittance fee and agent payout commission
     */
    public static function calculateRemittanceFee(float $amount): array
    {
        $percent = SystemSetting::getFloat('remittance_fee_percent', 1.0); // default 1% for cash remittance
        $fixed = SystemSetting::getFloat('remittance_fee_fixed', 0.0);
        $agentSharePercent = SystemSetting::getFloat('remittance_agent_commission_percent', 50.0); // 50% of fee goes to paying agent

        $totalFee = round(($amount * ($percent / 100)) + $fixed, 2);
        $agentCommission = round($totalFee * ($agentSharePercent / 100), 2);
        $platformNet = round($totalFee - $agentCommission, 2);

        return [
            'fee' => $totalFee,
            'agent_commission' => $agentCommission,
            'platform_net' => $platformNet,
            'fee_percent' => $percent,
        ];
    }
}


