<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Phone extends Model
{
    use HasFactory;

    protected $table = 'ip_phone';

    protected $fillable = [
        'asset_tag',
        'brand',
        'model',
        'serial_number',
        'current_number',
        'status',
        'current_user',
        'department',
        'remarks',
        'date_of_asset_transfer',
    ];

    public static function statuses() {
        return ['Available', 'In Use', 'Defective', 'For Repair', 'For Disposal', 'Disposed', 'For Testing', 'Undefined'];
    }

    public function histories(): MorphMany
    {
        return $this->morphMany(History::class, 'asset');
    }
}
