<?php

namespace App\Observers;

use App\Models\Assets;

class AssetTagObserver
{
    public function generate(): string {
       $totalCount = Assets::count();

       $nextNumber = $totalCount + 1;

       return 'IT' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

}
