<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('prop_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trading_account_id')->nullable()->constrained()->nullOnDelete();
            $table->string('prop_firm_name');
            $table->string('account_type');
            $table->decimal('initial_balance', 15, 2);
            $table->decimal('profit_target', 15, 2);
            $table->decimal('daily_drawdown', 15, 2);
            $table->decimal('max_drawdown', 15, 2);
            $table->unsignedInteger('minimum_trading_days')->default(5);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->string('challenge_status')->default('ongoing');
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->decimal('remaining_drawdown', 15, 2)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('prop_challenges'); }
};
