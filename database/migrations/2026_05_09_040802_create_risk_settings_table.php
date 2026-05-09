<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('risk_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trading_account_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('risk_per_trade_percent', 5, 2)->default(1);
            $table->decimal('daily_drawdown_limit', 15, 2)->default(0);
            $table->decimal('max_drawdown_limit', 15, 2)->default(0);
            $table->decimal('warning_threshold_percent', 5, 2)->default(75);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('risk_settings'); }
};
