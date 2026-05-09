<?php

use App\Http\Controllers\AmdxJournalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropChallengeController;
use App\Http\Controllers\RiskLedgerController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\TradingAccountController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('trading-accounts', TradingAccountController::class)->except(['show']);
    Route::resource('trades', TradeController::class)->except(['show']);
    Route::get('trades-export', [TradeController::class, 'export'])->name('trades.export');
    Route::get('/risk-ledger', [RiskLedgerController::class, 'index'])->name('risk-ledger.index');
    Route::get('/risk-ledger-export', [RiskLedgerController::class, 'export'])->name('risk-ledger.export');
    Route::resource('prop-challenges', PropChallengeController::class)->except(['show']);
    Route::get('/amdx-journal', [AmdxJournalController::class, 'index'])->name('amdx.index');
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
    Route::get('/statistics-export', [StatisticsController::class, 'export'])->name('statistics.export');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
