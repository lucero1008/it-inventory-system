<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesktopLaptopDetails extends Model
{
    protected $table = 'desktop_laptop_details';

    protected $fillable = [
        'asset_id',
        'processor',
        'ram',
        'storage',
    ];

    public function assets(): BelongsTo
    {
        return $this->belongsTo(Assets::class);
    }
}
