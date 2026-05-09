<?php

namespace Tests\Feature;

use App\Models\Trade;
use App\Models\TradingAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TradeJournalFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_update_and_delete_trade(): void
    {
        $user = User::factory()->create();
        $account = TradingAccount::factory()->create(['user_id' => $user->id, 'is_active' => true]);

        $this->actingAs($user)
            ->post(route('trades.store'), $this->payload($account->id, [
                'pair' => 'XAU/USD',
                'profit_loss' => -50,
            ]))
            ->assertRedirect(route('trades.index', absolute: false));

        $trade = Trade::query()->firstOrFail();
        $this->assertSame('loss', $trade->result);

        $this->actingAs($user)
            ->put(route('trades.update', $trade), $this->payload($account->id, [
                'pair' => 'EUR/USD',
                'profit_loss' => 80,
            ]))
            ->assertRedirect(route('trades.index', absolute: false));

        $trade->refresh();
        $this->assertSame('win', $trade->result);
        $this->assertSame('EUR/USD', $trade->pair);

        $this->actingAs($user)
            ->delete(route('trades.destroy', $trade))
            ->assertRedirect();

        $this->assertDatabaseCount('trades', 0);
    }

    public function test_trade_filter_and_export_follow_account_selection(): void
    {
        $user = User::factory()->create();
        $accountA = TradingAccount::factory()->create(['user_id' => $user->id, 'name' => 'A']);
        $accountB = TradingAccount::factory()->create(['user_id' => $user->id, 'name' => 'B']);

        Trade::factory()->create(array_merge($this->payload($accountA->id), ['user_id' => $user->id, 'profit_loss' => 10]));
        Trade::factory()->create(array_merge($this->payload($accountB->id), ['user_id' => $user->id, 'profit_loss' => -20]));

        $this->actingAs($user)
            ->get(route('trades.index', ['account_id' => $accountA->id, 'period' => '1year']))
            ->assertOk()
            ->assertSee('Total P/L:');

        $response = $this->actingAs($user)
            ->get(route('trades.export', ['account_id' => $accountA->id, 'period' => '1year']));

        $response->assertOk();
        $response->assertStreamed();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('XAU/USD', $response->streamedContent());
    }

    private function payload(int $accountId, array $overrides = []): array
    {
        return array_merge([
            'trading_account_id' => $accountId,
            'trade_date' => now()->toDateString(),
            'pair' => 'XAU/USD',
            'session' => 'London',
            'setup_type' => 'Breakout',
            'entry_price' => 2400,
            'stop_loss' => 2390,
            'take_profit' => 2420,
            'lot_size' => 0.1,
            'risk_amount' => 50,
            'risk_percent' => 1,
            'reward_amount' => 100,
            'rr_ratio' => 2,
            'profit_loss' => 25,
            'amdx_phase' => 'Accumulation',
        ], $overrides);
    }
}
