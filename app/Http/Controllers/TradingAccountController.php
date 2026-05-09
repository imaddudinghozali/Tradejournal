<?php
namespace App\Http\Controllers;

use App\Models\TradingAccount;
use Illuminate\Http\Request;

class TradingAccountController extends Controller
{
    public function index() { $accounts = auth()->user()->tradingAccounts()->latest()->paginate(10); return view('trading-accounts.index', compact('accounts')); }
    public function create() { return view('trading-accounts.form', ['account' => new TradingAccount()]); }
    public function store(Request $request) { $data = $request->validate(['name'=>'required|string|max:255','broker'=>'nullable|string|max:255','account_number'=>'nullable|string|max:255','balance'=>'required|numeric','equity'=>'required|numeric']); auth()->user()->tradingAccounts()->create($data); return redirect()->route('trading-accounts.index'); }
    public function edit(TradingAccount $tradingAccount) { $this->authorizeOwner($tradingAccount->user_id); return view('trading-accounts.form', ['account'=>$tradingAccount]); }
    public function update(Request $request, TradingAccount $tradingAccount) { $this->authorizeOwner($tradingAccount->user_id); $data = $request->validate(['name'=>'required','broker'=>'nullable','account_number'=>'nullable','balance'=>'required|numeric','equity'=>'required|numeric']); $tradingAccount->update($data); return redirect()->route('trading-accounts.index'); }
    public function destroy(TradingAccount $tradingAccount) { $this->authorizeOwner($tradingAccount->user_id); $tradingAccount->delete(); return back(); }
    private function authorizeOwner($ownerId): void { abort_if(auth()->id() !== $ownerId, 403); }
}
