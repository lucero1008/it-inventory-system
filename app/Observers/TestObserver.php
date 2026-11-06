<?php

namespace App\Observers;

use App\Models\DesktopLaptopDetails;

class TestObserver
{
    public function updated(DesktopLaptopDetails $value) : void {
        $test = $value->getChanges();


        dd($test);
    }
}
