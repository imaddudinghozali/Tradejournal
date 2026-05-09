<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password'];
    protected $hidden = ['password','remember_token'];
    protected function casts(): array { return ['email_verified_at' => 'datetime','password' => 'hashed']; }

    public function tradingAccounts() { return $this->hasMany(TradingAccount::class); }
    public function trades() { return $this->hasMany(Trade::class); }
    public function riskSettings() { return $this->hasMany(RiskSetting::class); }
    public function propChallenges() { return $this->hasMany(PropChallenge::class); }
    public function psychologyNotes() { return $this->hasMany(PsychologyNote::class); }
}
