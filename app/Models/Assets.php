<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Assets extends Model
{
    protected $table = 'assets';

    protected $fillable = [
        'asset_tag',
        'asset_type',
        'brand',
        'model',
        'serial_number',
        'status',
        'current_user',
        'department',
        'remark',
        'date_of_asset_transfer',
    ];

    public static function statuses() {
        return ['Available', 'In Use', 'Defective', 'For Repair', 'For Disposal', 'Disposed', 'For Testing', 'Undefined'];
    }

    // asset history
    public function histories(): MorphMany
    {
        return $this->morphMany(History::class, 'asset');
    }

    public function desktoplaptopDetails(): HasOne
    {
        return $this->hasOne(DesktopLaptopDetails::class, 'asset_id');
    }
}
