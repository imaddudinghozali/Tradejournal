<?php
namespace App\Http\Controllers;

use App\Models\Trade;
use App\Services\TradingMetricsService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class TradeController extends Controller
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

        $query = $metrics->buildTradeQuery($user, $selectedAccountId, $startDate, $endDate)
            ->with('tradingAccount')
            ;

        $summaryTotalPL = (clone $query)->sum('profit_loss');
        $summaryWins = (clone $query)->where('profit_loss', '>', 0)->count();
        $summaryLosses = (clone $query)->where('profit_loss', '<', 0)->count();

        $trades = $query->latest('trade_date')->paginate(15)->withQueryString();

        return view('trades.index', compact('trades', 'summaryTotalPL', 'summaryWins', 'summaryLosses', 'startDate', 'endDate', 'period', 'accounts', 'selectedAccountId'));
    }

    public function create() { $accounts = auth()->user()->tradingAccounts; return view('trades.form', ['trade'=>new Trade(),'accounts'=>$accounts]); }

    public function store(Request $request) {
        $data = $this->validated($request);
        $data['user_id']=auth()->id();
        $data['result'] = $this->resolveResult((float) $data['profit_loss']);
        if ($request->hasFile('screenshot')) {
            $data['screenshot_path'] = $request->file('screenshot')->store('trade-screenshots', 'public');
        }
        Trade::create($data);
        return redirect()->route('trades.index');
    }

    public function edit(Trade $trade) { abort_if($trade->user_id!==auth()->id(),403); $accounts = auth()->user()->tradingAccounts; return view('trades.form', compact('trade','accounts')); }

    public function update(Request $request, Trade $trade) {
        abort_if($trade->user_id!==auth()->id(),403);
        $data = $this->validated($request);
        $data['result'] = $this->resolveResult((float) $data['profit_loss']);
        if ($request->hasFile('screenshot')) {
            if ($trade->screenshot_path) { Storage::disk('public')->delete($trade->screenshot_path); }
            $data['screenshot_path'] = $request->file('screenshot')->store('trade-screenshots', 'public');
        }
        $trade->update($data);
        return redirect()->route('trades.index');
    }

    public function destroy(Trade $trade) {
        abort_if($trade->user_id!==auth()->id(),403);
        if ($trade->screenshot_path) { Storage::disk('public')->delete($trade->screenshot_path); }
        $trade->delete();
        return back();
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
            ->with('tradingAccount:id,name')
            ->orderByDesc('trade_date')
            ->get();

        $filename = 'trade-journal-' . now()->format('Ymd-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        return response()->stream(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['Date', 'Account', 'Pair', 'Session', 'Setup', 'Lot', 'Risk Amount', 'Risk %', 'Reward Amount', 'RR', 'Result', 'P/L', 'AMDX', 'Notes']);

            foreach ($rows as $r) {
                fputcsv($out, [
                    optional($r->trade_date)->format('Y-m-d'),
                    optional($r->tradingAccount)->name,
                    $r->pair,
                    $r->session,
                    $r->setup_type,
                    $r->lot_size,
                    $r->risk_amount,
                    $r->risk_percent,
                    $r->reward_amount,
                    $r->rr_ratio,
                    $r->result,
                    $r->profit_loss,
                    $r->amdx_phase,
                    $r->notes,
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }

    private function resolveResult(float $profitLoss): string
    {
        if ($profitLoss > 0) return 'win';
        if ($profitLoss < 0) return 'loss';
        return 'breakeven';
    }

    private function validated(Request $request): array {
        return $request->validate([
            'trading_account_id' => [
                'required',
                Rule::exists('trading_accounts', 'id')->where(fn ($q) => $q->where('user_id', auth()->id())),
            ],
            'trade_date'=>'required|date',
            'pair'=>'required|string|max:20',
            'session'=>'nullable|string|max:50',
            'setup_type'=>'nullable|string|max:100',
            'entry_price'=>'nullable|numeric|min:0',
            'stop_loss'=>'nullable|numeric|min:0|required_with:entry_price',
            'take_profit'=>'nullable|numeric|min:0',
            'lot_size'=>'required|numeric|min:0.01',
            'risk_amount'=>'required|numeric|min:0',
            'risk_percent'=>'required|numeric|min:0',
            'reward_amount'=>'required|numeric|min:0',
            'rr_ratio'=>'required|numeric|min:0',
            'result'=>'nullable|in:win,loss,breakeven',
            'profit_loss'=>'required|numeric',
            'amdx_phase'=>'nullable|string|max:50',
            'liquidity_sweep'=>'nullable|boolean',
            'inducement'=>'nullable|boolean',
            'fvg'=>'nullable|boolean',
            'order_block'=>'nullable|boolean',
            'bos'=>'nullable|boolean',
            'choch'=>'nullable|boolean',
            'notes'=>'nullable|string',
            'psychology_note'=>'nullable|string',
            'screenshot' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072|dimensions:min_width=300,min_height=200,max_width=6000,max_height=6000'
        ]);
    }
}



