<?php

namespace Database\Seeders;

use App\Models\PropChallenge;
use App\Models\RiskSetting;
use App\Models\Trade;
use App\Models\TradingAccount;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create(['name'=>'Demo Trader','email'=>'demo@hardriskledger.test']);
        $account = TradingAccount::create(['user_id'=>$user->id,'name'=>'XAU Scalper','broker'=>'IC Markets','account_number'=>'HRL-001','balance'=>10000,'equity'=>10000,'is_active'=>true]);
        RiskSetting::create(['user_id'=>$user->id,'trading_account_id'=>$account->id,'risk_per_trade_percent'=>1,'daily_drawdown_limit'=>500,'max_drawdown_limit'=>1000,'warning_threshold_percent'=>75]);
        Trade::factory()->count(20)->create(['user_id'=>$user->id,'trading_account_id'=>$account->id]);
        PropChallenge::create(['user_id'=>$user->id,'trading_account_id'=>$account->id,'prop_firm_name'=>'FTMO','account_type'=>'10K Challenge','initial_balance'=>10000,'profit_target'=>1000,'daily_drawdown'=>500,'max_drawdown'=>1000,'minimum_trading_days'=>5,'current_balance'=>10350,'challenge_status'=>'ongoing','progress_percentage'=>35,'remaining_drawdown'=>650]);
    }
}
