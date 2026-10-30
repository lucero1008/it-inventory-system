<?php

namespace App\Observers;

use App\Models\Logs;
use App\Models\Monitor;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class MonitorObserver
{
    public function created(Monitor $monitor) : void {
        History::write(
            $monitor,
            "Asset added to inventory."
        );

        Logs::write(
            'create',
            'Monitor',
            Auth::user()->name . " added a new asset record (Asset Tag: {$monitor->asset_tag})."
        );
    }

    public function updated(Monitor $monitor) : void {
        $dirtyFields = $monitor->getDirty();
        unset($dirtyFields['updated_at']);

        $historyEntries = [];

        $changedKeys = array_keys($dirtyFields);
        $totalChanges = count($dirtyFields);

        if($totalChanges === 1) {
            $field = array_key_first($dirtyFields);

            switch ($field) {
               case 'serial_number':
                    $historyEntries[] = "Changed serial number from {$monitor->getOriginal('serial_number')} to {$dirtyFields['serial_number']}";
                    break;
                case 'current_user':
                    $newUser = $dirtyFields['current_user'] ?: 'Unassigned';
                    $historyEntries[] = "Reassigned asset to {$newUser}";
                    break;
                case 'status':
                    $historyEntries[] = "Changed status from {$monitor->getOriginal('status')} to {$dirtyFields['status']}";
                    break;
                default:
                    $oldDetails = $monitor->getOriginal($field) ? $monitor->getOriginal($field) : "N/A";
                    $newDetails = $dirtyFields[$field] ? $dirtyFields[$field] : "N/A";
                    $historyEntries[] = "Updated {$field} from {$oldDetails} to {$newDetails}";
                    break;
                }
        } else {
            $allFieldChanges = $changedKeys;
            if (!empty($allFieldChanges)) {
                    $updatedAllValues = [];
                    foreach ($allFieldChanges as $field) {
                        $val = $monitor->$field;
                        if (!is_null($val) && $val !== '') {

                            $label = ucwords(str_replace('_', ' ', $field));

                            if ($field === 'asset_tag') { $label = 'Tag'; }
                            if ($field === 'serial_number') { $label = 'SN'; }
                            if ($field === 'current_user') { $label = 'User'; }
                            if ($field === 'date_of_asset_transfer') { $label = 'Date Transfer'; }

                            $updatedAllValues[] = "{$label}: {$val}";
                        }
                    }
                    $historyEntries[] = "Updated asset details (" . implode(', ', $updatedAllValues) . ")";
            }
        }

        if (!empty($historyEntries)) {
            foreach ($historyEntries as $entry) {
                History::write($monitor, $entry);
            }
        }

        // Audit Log
        Logs::write(
            'edit',
            'Monitor',
            Auth::user()->name . " update asset details for {$monitor->asset_tag}."
        );
    }

    public function deleted(Monitor $monitor) : void {
        Logs::write(
            'delete',
            'Monitor',
            Auth::user()->name . " deleted asset record (Asset Tag: {$monitor->asset_tag})."
        );
    }
}
