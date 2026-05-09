<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeScreenshot extends Model
{
    use HasFactory;
    protected $fillable = ['trade_id','path'];
    public function trade() { return $this->belongsTo(Trade::class); }
}
