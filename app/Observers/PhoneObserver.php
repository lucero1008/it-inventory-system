<?php

namespace App\Observers;

use App\Models\Logs;
use App\Models\Phone;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class PhoneObserver
{
    public function created(Phone $phone) : void {
        History::write(
            $phone,
            "Asset added to inventory."
        );

        Logs::write(
            'create',
            'Phone',
            Auth::user()->name . " added a new asset record (Asset Tag: {$phone->asset_tag})."
        );
    }


    public function updated(Phone $phone) : void {
        $dirtyFields = $phone->getDirty();
        unset($dirtyFields['updated_at']);

        $historyEntries = [];

        $changedKeys = array_keys($dirtyFields);
        $totalChanges = count($dirtyFields);

        if($totalChanges === 1) {
            $field = array_key_first($dirtyFields);

            switch ($field) {
               case 'serial_number':
                    $historyEntries[] = "Changed serial number from {$phone->getOriginal('serial_number')} to {$dirtyFields['serial_number']}";
                    break;
                case 'current_user':
                    $newUser = $dirtyFields['current_user'] ?: 'Unassigned';
                    $historyEntries[] = "Reassigned asset to {$newUser}";
                    break;
                case 'current_number':
                    $historyEntries[] = "Changed number from {$phone->getOriginal('current_number')} to {$dirtyFields['current_number']}";
                    break;
                case 'status':
                    $historyEntries[] = "Changed status from {$phone->getOriginal('status')} to {$dirtyFields['status']}";
                    break;
                case 'remarks':
                    $newRemark = $dirtyFields['remarks'] ? $dirtyFields['remarks'] : "empty";
                    $historyEntries[] = "Updated remark to {$newRemark}";
                    break;
                default:
                    $oldDetails = $phone->getOriginal($field) ? $phone->getOriginal($field) : "N/A";
                    $newDetails = $dirtyFields[$field] ? $dirtyFields[$field] : "N/A";
                    $label = str_replace('_', ' ', $field);
                    $historyEntries[] = "Updated {$label} from {$oldDetails} to {$newDetails}";
                    break;
                }
        } else {
            $allFieldChanges = $changedKeys;
            if (!empty($allFieldChanges)) {
                    $updatedAllValues = [];
                    foreach ($allFieldChanges as $field) {
                        $val = $phone->$field;
                        if (!is_null($val) && $val !== '') {

                            $label = ucwords(str_replace('_', ' ', $field));

                            if ($field === 'asset_tag') { $label = 'Tag'; }
                            if ($field === 'serial_number') { $label = 'SN'; }
                            if ($field === 'current_user') { $label = 'User'; }
                            if ($field === 'remarks') { $label = 'Remark'; }
                            if ($field === 'date_of_asset_transfer') { $label = 'Date Transfer'; }

                            $updatedAllValues[] = "{$label}: {$val}";
                        }
                    }
                    $historyEntries[] = "Updated asset details (" . implode(', ', $updatedAllValues) . ")";
            }
        }

        if (!empty($historyEntries)) {
            foreach ($historyEntries as $entry) {
                History::write($phone, $entry);
            }
        }

        // Audit Log
        Logs::write(
            'edit',
            'Phone',
            Auth::user()->name . " update asset details for {$phone->asset_tag}."
        );
    }

    public function deleted(Phone $phone) : void {
        Logs::write(
            'delete',
            'Phone',
            Auth::user()->name . " deleted asset record (Asset Tag: {$phone->asset_tag})."
        );
    }
}
