<?php

namespace App\Actions;

use App\Models\Assets;
use Illuminate\Support\Facades\DB;

class UpdateAssetAction
{
    public function execute(Assets $asset, array $data): Assets {
        return DB::transaction(function () use ($asset, $data) {
            $asset->update([
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

            $mainChanged = $asset->wasChanged();

            $details = match ($data['asset_type']) {
                'Desktop', 'Laptop' => $asset->desktopLaptopDetails()->updateOrCreate(
                    ['asset_id' => $asset->id],
                    [
                        'processor' => $data['processor'] ?? null,
                        'ram'       => $data['ram'] ?? null,
                        'storage'   => $data['storage'] ?? null,
                    ]
                ),

                default => null,
            };

            $detailsChanged = $details ? $details->wasChanged() : false;

            $asset->has_changes = $mainChanged || $detailsChanged;

            return $asset;
        });
    }
}
