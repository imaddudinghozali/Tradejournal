<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskSetting extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','trading_account_id','risk_per_trade_percent','daily_drawdown_limit','max_drawdown_limit','warning_threshold_percent'];
    public function user() { return $this->belongsTo(User::class); }
    public function tradingAccount() { return $this->belongsTo(TradingAccount::class); }
}
