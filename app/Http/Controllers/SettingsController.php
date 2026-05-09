<?php
namespace App\Http\Controllers;

use App\Models\RiskSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index() { $setting = RiskSetting::where('user_id',auth()->id())->latest()->first(); return view('settings.index', compact('setting')); }
    public function update(Request $request) { $data = $request->validate(['risk_per_trade_percent'=>'required|numeric','daily_drawdown_limit'=>'required|numeric','max_drawdown_limit'=>'required|numeric','warning_threshold_percent'=>'required|numeric']); RiskSetting::updateOrCreate(['user_id'=>auth()->id()],$data); return back(); }
}
