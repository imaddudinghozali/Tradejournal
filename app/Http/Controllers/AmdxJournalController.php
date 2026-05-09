<?php
namespace App\Http\Controllers;

use App\Models\Trade;

class AmdxJournalController extends Controller
{
    public function index() {
        $trades = Trade::where('user_id',auth()->id())->whereNotNull('amdx_phase')->latest('trade_date')->paginate(15);
        return view('amdx.index', compact('trades'));
    }
}
