<?php

namespace App\Observers;

use App\Models\Logs;
use App\Models\Printer;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class PrinterObserver
{
    public function created(Printer $printer) : void {
        History::write(
            $printer,
            "Asset added to inventory."
        );

        $checkDetails = $printer->asset_tag ? "(Asset Tag: {$printer->asset_tag})" : "(Brand: {$printer->brand} Model: {$printer->model} SN: {$printer->model})";
        Logs::write(
            'create',
            'Printer',
            Auth::user()->name . " added a new printer record {$checkDetails}."
        );
    }

    public function updated(Printer $printer) : void {
        $dirtyFields = $printer->getDirty();
        unset($dirtyFields['updated_at']);

        $historyEntries = [];

        $changedKeys = array_keys($dirtyFields);
        $totalChanges = count($dirtyFields);

        if($totalChanges === 1) {
            $field = array_key_first($dirtyFields);

            switch ($field) {
               case 'serial_number':
                    $historyEntries[] = "Changed serial number from {$printer->getOriginal('serial_number')} to {$dirtyFields['serial_number']}";
                    break;
                case 'current_user':
                    $newUser = $dirtyFields['current_user'] ?: 'Unassigned';
                    $historyEntries[] = "Reassigned asset to {$newUser}";
                    break;
                case 'status':
                    $historyEntries[] = "Changed status from {$printer->getOriginal('status')} to {$dirtyFields['status']}";
                    break;
                default:
                    $oldDetails = $printer->getOriginal($field) ? $printer->getOriginal($field) : "N/A";
                    $newDetails = $dirtyFields[$field] ? $dirtyFields[$field] : "N/A";
                    $historyEntries[] = "Updated {$field} from {$oldDetails} to {$newDetails}";
                    break;
                }
        } else {
            $allFieldChanges = $changedKeys;
            if (!empty($allFieldChanges)) {
                    $updatedAllValues = [];
                    foreach ($allFieldChanges as $field) {
                        $val = $printer->$field;
                        if (!is_null($val) && $val !== '') {

                            $label = ucwords(str_replace('_', ' ', $field));

                            if ($field === 'asset_tag') { $label = 'Tag'; }
                            if ($field === 'printer_type') { $label = 'Type'; }
                            if ($field === 'serial_number') { $label = 'SN'; }
                            if ($field === 'current_user') { $label = 'User'; }
                            if ($field === 'date_of_asset_transfer') { $label = 'Date Transfer'; }

                            $updatedAllValues[] = "{$label}: {$val}";
                        }
                    }
                    $historyEntries[] = "Updated printer details (" . implode(', ', $updatedAllValues) . ")";
            }
        }

        if (!empty($historyEntries)) {
            foreach ($historyEntries as $entry) {
                History::write($printer, $entry);
            }
        }

        $checkDetails = $printer->asset_tag ? "(Asset Tag: {$printer->asset_tag})" : "(Brand: {$printer->brand} Model: {$printer->model} SN: {$printer->model})";
        Logs::write(
            'edit',
            'Printer',
            Auth::user()->name . " update printer details for {$checkDetails}."
        );
    }

    public function deleted(Printer $printer) : void {
        $checkDetails = $printer->asset_tag ? "(Asset Tag: {$printer->asset_tag})" : "(Brand: {$printer->brand} Model: {$printer->model} SN: {$printer->model})";
        Logs::write(
            'delete',
            'Printer',
            Auth::user()->name . " deleted printer record {$checkDetails}."
        );
    }
}
