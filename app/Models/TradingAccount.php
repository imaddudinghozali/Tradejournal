<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingAccount extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','name','broker','account_number','balance','equity','is_active'];

    public function user() { return $this->belongsTo(User::class); }
    public function trades() { return $this->hasMany(Trade::class); }
    public function riskSettings() { return $this->hasMany(RiskSetting::class); }
    public function propChallenges() { return $this->hasMany(PropChallenge::class); }
}
