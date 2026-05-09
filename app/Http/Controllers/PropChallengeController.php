<?php
namespace App\Http\Controllers;

use App\Models\PropChallenge;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PropChallengeController extends Controller
{
    public function index() { $challenges = auth()->user()->propChallenges()->latest()->paginate(10); return view('prop-challenges.index', compact('challenges')); }
    public function create() { return view('prop-challenges.form', ['challenge'=>new PropChallenge(), 'accounts' => auth()->user()->tradingAccounts()->orderBy('name')->get()]); }
    public function store(Request $request) { $data = $this->validated($request); $data['user_id']=auth()->id(); $data['remaining_drawdown']=$data['max_drawdown']; PropChallenge::create($data); return redirect()->route('prop-challenges.index'); }
    public function edit(PropChallenge $propChallenge) { abort_if($propChallenge->user_id!==auth()->id(),403); return view('prop-challenges.form',['challenge'=>$propChallenge, 'accounts' => auth()->user()->tradingAccounts()->orderBy('name')->get()]); }
    public function update(Request $request, PropChallenge $propChallenge) { abort_if($propChallenge->user_id!==auth()->id(),403); $data=$this->validated($request); $data['progress_percentage']=round((($data['current_balance']-$data['initial_balance'])/max($data['profit_target'],1))*100,2); $data['remaining_drawdown']=max(0,$data['max_drawdown']-max(0,$data['initial_balance']-$data['current_balance'])); $propChallenge->update($data); return redirect()->route('prop-challenges.index'); }
    public function destroy(PropChallenge $propChallenge) { abort_if($propChallenge->user_id!==auth()->id(),403); $propChallenge->delete(); return back(); }
    private function validated(Request $request): array { return $request->validate(['trading_account_id'=>['nullable', Rule::exists('trading_accounts', 'id')->where(fn ($q) => $q->where('user_id', auth()->id()))],'prop_firm_name'=>'required|string|max:255','account_type'=>'required|string|max:255','initial_balance'=>'required|numeric|min:0','profit_target'=>'required|numeric|min:0','daily_drawdown'=>'required|numeric|min:0','max_drawdown'=>'required|numeric|min:0','minimum_trading_days'=>'required|integer|min:1','current_balance'=>'required|numeric|min:0','challenge_status'=>'required|in:ongoing,passed,failed']); }
}
