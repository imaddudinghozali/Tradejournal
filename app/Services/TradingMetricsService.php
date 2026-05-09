<?php

namespace App\Services;

use App\Models\Trade;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TradingMetricsService
{
    public function resolvePeriodDates(string $period, ?string $startDate, ?string $endDate): array
    {
        $today = Carbon::today();

        return match ($period) {
            'today' => [$today->toDateString(), $today->toDateString()],
            'weekly' => [$today->copy()->startOfWeek()->toDateString(), $today->toDateString()],
            'month' => [$today->copy()->startOfMonth()->toDateString(), $today->toDateString()],
            '3months' => [$today->copy()->subMonthsNoOverflow(3)->toDateString(), $today->toDateString()],
            '1year' => [$today->copy()->subYear()->toDateString(), $today->toDateString()],
            'custom' => [$startDate, $endDate],
            default => [null, null],
        };
    }

    public function buildTradeQuery(User $user, ?string $selectedAccountId = null, ?string $startDate = null, ?string $endDate = null): Builder
    {
        return Trade::query()
            ->where('user_id', $user->id)
            ->when($selectedAccountId, fn (Builder $q) => $q->where('trading_account_id', $selectedAccountId))
            ->when($startDate, fn (Builder $q) => $q->whereDate('trade_date', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('trade_date', '<=', $endDate));
    }

    public function summarize(Builder $baseTrades): array
    {
        $total = (clone $baseTrades)->count();
        $wins = (clone $baseTrades)->where('profit_loss', '>', 0)->count();
        $losses = (clone $baseTrades)->where('profit_loss', '<', 0)->count();
        $periodPL = (float) (clone $baseTrades)->sum('profit_loss');
        $winrate = $total > 0 ? round(($wins / $total) * 100, 2) : 0.0;

        return compact('total', 'wins', 'losses', 'periodPL', 'winrate');
    }

    public function buildCurve(Builder $baseTrades): array
    {
        $rows = (clone $baseTrades)->orderBy('trade_date')->get(['trade_date', 'profit_loss']);

        $running = 0.0;
        $peak = 0.0;
        $maxDrawdown = 0.0;
        $labels = [];
        $data = [];

        foreach ($rows as $row) {
            $running += (float) $row->profit_loss;
            $peak = max($peak, $running);
            $maxDrawdown = max($maxDrawdown, $peak - $running);
            $labels[] = $row->trade_date->format('d M');
            $data[] = round($running, 2);
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'maxDrawdown' => round($maxDrawdown, 2),
        ];
    }

    public function maxDrawdownFromRows(Collection $rows): float
    {
        $running = 0.0;
        $peak = 0.0;
        $maxDrawdown = 0.0;

        foreach ($rows as $row) {
            $running += (float) $row->profit_loss;
            $peak = max($peak, $running);
            $maxDrawdown = max($maxDrawdown, $peak - $running);
        }

        return round($maxDrawdown, 2);
    }
}

