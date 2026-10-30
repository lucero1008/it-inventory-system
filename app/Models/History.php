<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class History extends Model
{
    use HasFactory;

    protected $table = 'asset_histories';

    protected $fillable = [
        'asset_type',
        'asset_id',
        'user_id',
        'details',
    ];

    public static function write($asset, $details) {
        self::create([
            'asset_type' => get_class($asset), // $asset->getMorphClass()
            'asset_id'   => $asset->getKey(),
            'user_id' => Auth::id(),
            'details' => $details,
        ]);
    }

    public function asset(): MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
