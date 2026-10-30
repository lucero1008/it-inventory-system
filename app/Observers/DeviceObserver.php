<?php

namespace App\Observers;

use App\Models\Logs;
use App\Models\Device;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class DeviceObserver
{
    public function created(Device $device) : void
    {
        History::write(
            $device,
            "Asset added to inventory."
        );

        Logs::write(
            'create',
            'Device',
            Auth::user()->name . " added a new asset record (Asset Tag: {$device->asset_tag})."
        );
    }

    public function updated(Device $device) : void
    {
        $dirtyFields = $device->getDirty();
        unset($dirtyFields['updated_at']);

        $historyEntries = [];

        $specKeys = ['ram', 'storage', 'processor'];
        $changedKeys = array_keys($dirtyFields);
        $totalChanges = count($dirtyFields);

        if($totalChanges === 1) {
            $field = array_key_first($dirtyFields);

            switch ($field) {
               case 'serial_number':
                    $historyEntries[] = "Changed serial number from {$device->getOriginal('serial_number')} to {$dirtyFields['serial_number']}";
                    break;
                case 'current_user':
                    $newUser = $dirtyFields['current_user'] ?: 'Unassigned';
                    $historyEntries[] = "Reassigned asset to {$newUser}";
                    break;
                case 'status':
                    $historyEntries[] = "Changed status from {$device->getOriginal('status')} to {$dirtyFields['status']}";
                    break;
                default:
                    $oldDetails = $device->getOriginal($field) ? $device->getOriginal($field) : "N/A";
                    $newDetails = $dirtyFields[$field] ? $dirtyFields[$field] : "N/A";
                    $historyEntries[] = "Updated {$field} from {$oldDetails} to {$newDetails}";
                    break;
                }
        } else {

            $hasOtherField = false;
            foreach ($changedKeys as $key) {
                if (!in_array($key, $specKeys)) {
                    $hasOtherField = true;
                    break;
                }
            }

            if (!$hasOtherField) {
                $specChanges = array_intersect($changedKeys, $specKeys);
                $updatedValues = [];
                foreach ($specChanges as $field) {
                    $updatedValues[] = $device->$field;
                }
                $historyEntries[] = "Updated hardware specifications (" . implode(', ', $updatedValues) . ")";
            } else {
                $allFieldChanges = $changedKeys;
                if (!empty($allFieldChanges)) {
                    $updatedAllValues = [];
                    foreach ($allFieldChanges as $field) {
                        $val = $device->$field;
                        if (!is_null($val) && $val !== '') {
                            $label = ucwords(str_replace('_', ' ', $field));

                            if ($field === 'asset_tag') { $label = 'Tag'; }
                            if ($field === 'serial_number') { $label = 'SN'; }
                            if ($field === 'device_type') { $label = 'Type'; }
                            if ($field === 'current_user') { $label = 'User'; }
                            if ($field === 'date_of_asset_transfer') { $label = 'Date Transfer'; }

                            $updatedAllValues[] = "{$label}: {$val}";
                        }
                    }
                    $historyEntries[] = "Updated asset details (" . implode(', ', $updatedAllValues) . ")";
                }
            }

        }

        if (!empty($historyEntries)) {
            foreach ($historyEntries as $entry) {
                History::write($device, $entry);
            }
        }

        // Audit Log
        Logs::write(
            'edit',
            'Device',
            Auth::user()->name . " update asset details for {$device->asset_tag}."
        );
    }

    public function deleted(Device $device) : void
    {
        Logs::write(
            'delete',
            'Device',
            Auth::user()->name . " deleted asset record (Asset Tag: {$device->asset_tag})."
        );
    }
}
