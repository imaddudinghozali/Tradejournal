<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','trading_account_id','trade_date','pair','session','setup_type','entry_price','stop_loss','take_profit','lot_size','risk_amount','risk_percent','reward_amount','rr_ratio','result','profit_loss','amdx_phase','liquidity_sweep','inducement','fvg','order_block','bos','choch','notes','psychology_note','screenshot_path'];
    protected $casts = ['trade_date' => 'date','liquidity_sweep' => 'boolean','inducement' => 'boolean','fvg' => 'boolean','order_block' => 'boolean','bos' => 'boolean','choch' => 'boolean'];
    public function user() { return $this->belongsTo(User::class); }
    public function tradingAccount() { return $this->belongsTo(TradingAccount::class); }
}
