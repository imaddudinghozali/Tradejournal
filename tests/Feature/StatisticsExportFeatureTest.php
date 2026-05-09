<?php

namespace Tests\Feature;

use App\Models\Trade;
use App\Models\TradingAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticsExportFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistics_export_returns_filtered_csv(): void
    {
        $user = User::factory()->create();
        $account = TradingAccount::factory()->create(['user_id' => $user->id]);

        Trade::factory()->create([
            'user_id' => $user->id,
            'trading_account_id' => $account->id,
            'trade_date' => now()->toDateString(),
            'profit_loss' => 120,
        ]);

        $response = $this->actingAs($user)
            ->get(route('statistics.export', ['account_id' => $account->id, 'period' => '1year']));

        $response->assertOk();
        $response->assertStreamed();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Date,P/L,Cumulative', $response->streamedContent());
    }
}
