<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Models\DailyReport;
use PHPUnit\Framework\TestCase;

class DailyReportCommissionTest extends TestCase
{
    public function test_zero_client_commission_does_not_fall_back_to_imported_agent_fee(): void
    {
        $client = new Client(['commission_percentage' => 0]);
        $report = new DailyReport([
            'agent_fee_usd' => 5,
            'weekly_reward_base_usd_hosts' => 288,
        ]);
        $report->setRelation('client', $client);

        $this->assertSame(0.0, (float) $report->agent_fee_usd);
    }

    public function test_client_commission_is_calculated_from_weekly_reward_base(): void
    {
        $client = new Client(['commission_percentage' => 5]);
        $report = new DailyReport([
            'agent_fee_usd' => 5,
            'weekly_reward_base_usd_hosts' => 288,
        ]);
        $report->setRelation('client', $client);

        $this->assertSame(14.4, (float) $report->agent_fee_usd);
    }
}
