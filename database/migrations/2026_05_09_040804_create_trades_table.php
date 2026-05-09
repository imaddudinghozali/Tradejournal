<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trading_account_id')->constrained()->cascadeOnDelete();
            $table->date('trade_date');
            $table->string('pair')->default('XAU/USD');
            $table->string('session')->nullable();
            $table->string('setup_type')->nullable();
            $table->decimal('entry_price', 15, 5)->nullable();
            $table->decimal('stop_loss', 15, 5)->nullable();
            $table->decimal('take_profit', 15, 5)->nullable();
            $table->decimal('lot_size', 12, 2)->default(0.01);
            $table->decimal('risk_amount', 15, 2)->default(0);
            $table->decimal('risk_percent', 6, 2)->default(0);
            $table->decimal('reward_amount', 15, 2)->default(0);
            $table->decimal('rr_ratio', 8, 2)->default(0);
            $table->string('result')->default('loss');
            $table->decimal('profit_loss', 15, 2)->default(0);
            $table->string('amdx_phase')->nullable();
            $table->boolean('liquidity_sweep')->default(false);
            $table->boolean('inducement')->default(false);
            $table->boolean('fvg')->default(false);
            $table->boolean('order_block')->default(false);
            $table->boolean('bos')->default(false);
            $table->boolean('choch')->default(false);
            $table->text('notes')->nullable();
            $table->text('psychology_note')->nullable();
            $table->string('screenshot_path')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('trades'); }
};
