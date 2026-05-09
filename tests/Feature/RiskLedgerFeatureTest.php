<?php

namespace Tests\Feature;

use App\Models\RiskSetting;
use App\Models\Trade;
use App\Models\TradingAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiskLedgerFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_risk_ledger_shows_warning_state_for_high_usage(): void
    {
        $user = User::factory()->create();
        $account = TradingAccount::factory()->create(['user_id' => $user->id]);

        RiskSetting::create([
            'user_id' => $user->id,
            'trading_account_id' => $account->id,
            'risk_per_trade_percent' => 1,
            'daily_drawdown_limit' => 100,
            'max_drawdown_limit' => 300,
            'warning_threshold_percent' => 75,
        ]);

        Trade::factory()->create([
            'user_id' => $user->id,
            'trading_account_id' => $account->id,
            'trade_date' => now()->toDateString(),
            'profit_loss' => -95,
        ]);

        $this->actingAs($user)
            ->get(route('risk-ledger.index', ['account_id' => $account->id]))
            ->assertOk()
            ->assertSee('AUTO LOCK WARNING ACTIVE');
    }

    public function test_risk_ledger_export_returns_csv(): void
    {
        $user = User::factory()->create();
        $account = TradingAccount::factory()->create(['user_id' => $user->id]);

        Trade::factory()->create([
            'user_id' => $user->id,
            'trading_account_id' => $account->id,
            'trade_date' => now()->toDateString(),
            'profit_loss' => -50,
        ]);

        $response = $this->actingAs($user)
            ->get(route('risk-ledger.export', ['account_id' => $account->id]));

        $response->assertOk();
        $response->assertStreamed();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Day P/L', $response->streamedContent());
    }
}
