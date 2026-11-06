<?php

namespace App\Observers;

use App\Models\Logs;
use App\Models\Assets;
use App\Models\DesktopLaptopDetails;
use App\Models\History;
use App\Services\AssetHistoryService;
use Illuminate\Support\Facades\Auth;

class AssetsObserver
{
    // generate unique asset tag
    public function generate(): string {
       $totalCount = Assets::count();

       $nextNumber = $totalCount + 1;

       return 'IT' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    // create logs
    public function created(Assets $assets) : void
    {
        History::write(
            $assets,
            "Asset added to inventory."
        );

        Logs::write(
            'create',
            $assets->asset_type,
            Auth::user()->name . " added a new asset record (Asset Tag: {$assets->asset_tag})."
        );
    }

    // update logs
    public function updated(Assets $assets) : void {
        $historyEntries = app(AssetHistoryService::class)->generateEntries();

        dd($historyEntries);

        Logs::write(
            'edit',
            $assets->asset_type,
            Auth::user()->name . " update asset details for {$assets->asset_tag}."
        );
    }

    // delete logs
    public function deleted(Assets $assets) : void
    {
        Logs::write(
            'delete',
            $assets->asset_type,
            Auth::user()->name . " deleted asset record (Asset Tag: {$assets->asset_tag})."
        );
    }
}
