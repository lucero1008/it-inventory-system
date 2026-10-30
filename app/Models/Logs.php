<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Logs extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'details',
    ];

    public static function write($action, $module, $details) {
        self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'details' => $details,
        ]);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
