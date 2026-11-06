<?php

namespace App\Actions;

use App\Models\Assets;
use Illuminate\Support\Facades\DB;

class CreateAssetAction
{
    public function execute(array $data): Assets {
        return DB::transaction(function () use ($data) {
            $asset = Assets::create([
                'asset_tag'              => $data['asset_tag'],
                'asset_type'             => $data['asset_type'],
                'brand'                  => $data['brand'],
                'model'                  => $data['model'],
                'serial_number'          => $data['serial_number'],
                'current_user'           => $data['current_user'] ?? 'IT',
                'department'             => $data['department'] ?? 'IT',
                'remark'                 => $data['remark'] ?? null,
                'status'                 => $data['status'],
                'date_of_asset_transfer' => $data['date_of_asset_transfer'] ?? null,
            ]);

            match ($data['asset_type']) {
                'Desktop', 'Laptop' => $asset->desktoplaptopDetails()->create([
                    'processor' => $data['processor'] ?? null,
                    'ram'       => $data['ram'] ?? null,
                    'storage'   => $data['storage'] ?? null,
                ]),

                default => null,
            };

            return $asset;
        });
    }
}
