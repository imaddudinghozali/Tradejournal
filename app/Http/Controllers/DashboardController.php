<?php

namespace App\Http\Controllers;

use App\Models\RiskSetting;
use App\Services\TradingMetricsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, TradingMetricsService $metrics)
    {
        $user = auth()->user();
        $accounts = $user->tradingAccounts()->orderByDesc('is_active')->orderBy('name')->get();

        $selectedAccountId = $request->input('account_id');
        if (! $selectedAccountId && $accounts->isNotEmpty()) {
            $selectedAccountId = (string) optional($accounts->firstWhere('is_active', true))->id ?: (string) $accounts->first()->id;
        }

        if ($selectedAccountId && ! $accounts->pluck('id')->contains((int) $selectedAccountId)) {
            $selectedAccountId = null;
        }

        $period = $request->input('period', 'month');
        [$startDate, $endDate] = $metrics->resolvePeriodDates($period, $request->input('start_date'), $request->input('end_date'));

        $baseTrades = $metrics->buildTradeQuery($user, $selectedAccountId, $startDate, $endDate);

        ['total' => $total, 'wins' => $wins, 'losses' => $losses, 'periodPL' => $periodPL, 'winrate' => $winrate] = $metrics->summarize($baseTrades);
        ['labels' => $curveLabels, 'data' => $curveData, 'maxDrawdown' => $maxDrawdown] = $metrics->buildCurve($baseTrades);

        $setting = RiskSetting::where('user_id', $user->id)->latest()->first();
        $remainingDaily = $setting ? max(0, $setting->daily_drawdown_limit + min(0, $periodPL)) : 0;
        $remainingMax = $setting ? max(0, $setting->max_drawdown_limit - $maxDrawdown) : 0;
        $status = $remainingMax <= 0 ? 'danger' : ($setting && $remainingMax < ($setting->max_drawdown_limit * 0.25) ? 'warning' : 'safe');

        $recentTrades = (clone $baseTrades)->latest('trade_date')->limit(8)->get();
        $selectedAccountName = $selectedAccountId ? optional($accounts->firstWhere('id', (int) $selectedAccountId))->name : 'All Accounts';

        return view('dashboard', compact(
            'total', 'wins', 'losses', 'winrate', 'periodPL',
            'maxDrawdown', 'remainingDaily', 'remainingMax', 'status', 'curveLabels', 'curveData',
            'recentTrades', 'startDate', 'endDate', 'period', 'accounts', 'selectedAccountId', 'selectedAccountName'
        ));
    }
}



