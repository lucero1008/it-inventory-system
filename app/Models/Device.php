<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model
{
    use HasFactory;

    protected $table = 'desktop_laptop';

    protected $fillable = [
        'asset_tag',
        'device_type',
        'brand',
        'model',
        'serial_number',
        'processor',
        'ram',
        'storage',
        'status',
        'current_user',
        'department',
        'remarks',
        'date_of_asset_transfer',
    ];

    public static function statuses() {
        return ['Available', 'In Use', 'For Repair', 'For Disposal', 'Disposed', 'For Testing', 'Undefined'];
    }
}
