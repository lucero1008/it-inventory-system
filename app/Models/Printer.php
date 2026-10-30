<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Printer extends Model
{
    use HasFactory;

    protected $table = 'printer';

    protected $fillable = [
        'asset_tag',
        'brand',
        'model',
        'serial_number',
        'printer_type',
        'ownership',
        'supplier',
        'status',
        'current_user',
        'department',
        'remarks',
        'date_of_asset_transfer',
    ];
}
