<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropChallenge extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','trading_account_id','prop_firm_name','account_type','initial_balance','profit_target','daily_drawdown','max_drawdown','minimum_trading_days','current_balance','challenge_status','progress_percentage','remaining_drawdown'];
    public function user() { return $this->belongsTo(User::class); }
    public function tradingAccount() { return $this->belongsTo(TradingAccount::class); }
}
