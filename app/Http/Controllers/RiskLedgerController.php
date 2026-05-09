<?php
namespace App\Http\Controllers;

use App\Models\RiskSetting;
use App\Services\TradingMetricsService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;

class RiskLedgerController extends Controller
{
    public function index(Request $request, TradingMetricsService $metrics)
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

        $baseQuery = $metrics->buildTradeQuery($user, $selectedAccountId);

        $daily = (clone $baseQuery)->whereDate('trade_date', now())->sum('profit_loss');
        $weekly = (clone $baseQuery)->whereBetween('trade_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('profit_loss');
        $monthly = (clone $baseQuery)->whereMonth('trade_date', now()->month)->whereYear('trade_date', now()->year)->sum('profit_loss');

        $settings = RiskSetting::where('user_id', $user->id)
            ->when($selectedAccountId, fn ($q) => $q->where('trading_account_id', $selectedAccountId))
            ->latest()
            ->first();

        $equityRows = (clone $baseQuery)->orderBy('trade_date')->get(['profit_loss']);
        $maxDrawdownUsed = $metrics->maxDrawdownFromRows($equityRows);

        $dailyLossUsed = max(0, -1 * (float) $daily);
        $dailyLimit = (float) ($settings->daily_drawdown_limit ?? 0);
        $maxLimit = (float) ($settings->max_drawdown_limit ?? 0);

        $dailyUsagePct = $dailyLimit > 0 ? min(100, round(($dailyLossUsed / $dailyLimit) * 100, 2)) : 0;
        $maxUsagePct = $maxLimit > 0 ? min(100, round(($maxDrawdownUsed / $maxLimit) * 100, 2)) : 0;

        $remainingDaily = max(0, $dailyLimit - $dailyLossUsed);
        $remainingMax = max(0, $maxLimit - $maxDrawdownUsed);

        $status = 'safe';
        if ($dailyUsagePct >= 100 || $maxUsagePct >= 100) {
            $status = 'danger';
        } elseif ($dailyUsagePct >= 75 || $maxUsagePct >= 75) {
            $status = 'warning';
        }

        $lockMode = $dailyUsagePct > 90 || $maxUsagePct > 90;

        $dailyLogRows = (clone $baseQuery)
            ->selectRaw('trade_date, SUM(profit_loss) as day_pl, COUNT(*) as trade_count')
            ->groupBy('trade_date')
            ->orderByDesc('trade_date')
            ->limit(30)
            ->get()
            ->map(function ($row) use ($dailyLimit) {
                $dayLoss = max(0, -1 * (float) $row->day_pl);
                $usage = $dailyLimit > 0 ? round(($dayLoss / $dailyLimit) * 100, 2) : 0;

                $level = 'normal';
                if ($usage >= 100) {
                    $level = 'breach';
                } elseif ($usage >= 75) {
                    $level = 'warning';
                }

                return [
                    'date' => $row->trade_date,
                    'day_pl' => (float) $row->day_pl,
                    'trade_count' => (int) $row->trade_count,
                    'usage' => $usage,
                    'level' => $level,
                ];
            })
            ->filter(fn ($r) => $r['level'] !== 'normal')
            ->values();

        return view('risk-ledger.index', compact(
            'daily', 'weekly', 'monthly', 'settings',
            'dailyLossUsed', 'maxDrawdownUsed',
            'dailyLimit', 'maxLimit',
            'dailyUsagePct', 'maxUsagePct',
            'remainingDaily', 'remainingMax', 'status',
            'accounts', 'selectedAccountId', 'lockMode', 'dailyLogRows'
        ));
    }

    public function export(Request $request, TradingMetricsService $metrics): StreamedResponse
    {
        $user = auth()->user();
        $accounts = $user->tradingAccounts()->pluck('id');
        $selectedAccountId = $request->input('account_id');
        if ($selectedAccountId && ! $accounts->contains((int) $selectedAccountId)) {
            $selectedAccountId = null;
        }

        $baseQuery = $metrics->buildTradeQuery($user, $selectedAccountId);
        $dailyLimit = (float) (RiskSetting::where('user_id', $user->id)
            ->when($selectedAccountId, fn ($q) => $q->where('trading_account_id', $selectedAccountId))
            ->latest()
            ->value('daily_drawdown_limit') ?? 0);

        $dailyLogRows = (clone $baseQuery)
            ->selectRaw('trade_date, SUM(profit_loss) as day_pl, COUNT(*) as trade_count')
            ->groupBy('trade_date')
            ->orderByDesc('trade_date')
            ->limit(90)
            ->get();

        $filename = 'risk-ledger-' . now()->format('Ymd-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        return response()->stream(function () use ($dailyLogRows, $dailyLimit) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['Date', 'Day P/L', 'Trades', 'Daily Loss Used', 'Daily Limit', 'Usage %', 'Level']);

            foreach ($dailyLogRows as $row) {
                $dayPl = (float) $row->day_pl;
                $dayLoss = max(0, -1 * $dayPl);
                $usage = $dailyLimit > 0 ? round(($dayLoss / $dailyLimit) * 100, 2) : 0;
                $level = $usage >= 100 ? 'breach' : ($usage >= 75 ? 'warning' : 'normal');

                fputcsv($out, [
                    $row->trade_date,
                    $dayPl,
                    (int) $row->trade_count,
                    $dayLoss,
                    $dailyLimit,
                    $usage,
                    $level,
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }
}
