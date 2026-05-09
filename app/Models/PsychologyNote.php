<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PsychologyNote extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','trade_id','note_date','mood','content'];
    protected $casts = ['note_date' => 'date'];
    public function user() { return $this->belongsTo(User::class); }
    public function trade() { return $this->belongsTo(Trade::class); }
}
