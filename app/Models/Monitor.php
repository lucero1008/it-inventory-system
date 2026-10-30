<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Monitor extends Model
{
    use HasFactory;

    protected $table = 'monitor';

    protected $fillable = [
        'asset_tag',
        'brand',
        'model',
        'serial_number',
        'size',
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
