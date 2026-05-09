<?php
namespace App\Http\Controllers;

use App\Services\TradingMetricsService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
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

        $period = $request->input('period', 'month');
        [$startDate, $endDate] = $metrics->resolvePeriodDates($period, $request->input('start_date'), $request->input('end_date'));

        $rows = $metrics->buildTradeQuery($user, $selectedAccountId, $startDate, $endDate)
            ->orderBy('trade_date')
            ->get();

        $labels = $rows->map(fn ($r) => $r->trade_date->format('Y-m-d'))->values()->all();
        $pl = $rows->pluck('profit_loss')->map(fn ($v) => (float) $v)->values()->all();
        $colors = array_map(fn ($v) => $v >= 0 ? 'rgba(16,185,129,.7)' : 'rgba(244,63,94,.7)', $pl);

        return view('statistics.index', compact('labels', 'pl', 'colors', 'startDate', 'endDate', 'period', 'accounts', 'selectedAccountId'));
    }

    public function export(Request $request, TradingMetricsService $metrics): StreamedResponse
    {
        $user = auth()->user();
        $accounts = $user->tradingAccounts()->pluck('id');
        $selectedAccountId = $request->input('account_id');
        if ($selectedAccountId && ! $accounts->contains((int) $selectedAccountId)) {
            $selectedAccountId = null;
        }

        $period = $request->input('period', 'month');
        [$startDate, $endDate] = $metrics->resolvePeriodDates($period, $request->input('start_date'), $request->input('end_date'));

        $rows = $metrics->buildTradeQuery($user, $selectedAccountId, $startDate, $endDate)
            ->orderBy('trade_date')
            ->get(['trade_date', 'profit_loss']);

        $running = 0.0;
        $filename = 'statistics-' . now()->format('Ymd-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        return response()->stream(function () use ($rows, &$running) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['Date', 'P/L', 'Cumulative']);

            foreach ($rows as $row) {
                $running += (float) $row->profit_loss;
                fputcsv($out, [
                    optional($row->trade_date)->format('Y-m-d'),
                    (float) $row->profit_loss,
                    round($running, 2),
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }
}



