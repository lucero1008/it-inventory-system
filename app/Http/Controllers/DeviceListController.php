<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Logs;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class DeviceListController extends Controller
{
    public function showDevice(Request $request) {
        if(!Auth::check()) {
            return redirect('/');
        }

        session(['device_index_url' => $request->fullUrl()]);

        $search = $request->input('search');
        $perPage = $request->input('perPage', 10);
        $select = $request->input('select');

        $sort = in_array($request->input('sort'), ['id', 'device_type', 'asset_tag', 'serial_number', 'brand', 'model', 'status'])
                ? $request->input('sort')
                : 'id';
        $direction = $request->input('direction', 'desc');

        $data = Device::when($search, function ($query, $search) {
            $query->where('asset_tag', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('current_device', 'like', "%{$search}%");
        })
        ->when($select, function ($query, $select) {
            $query->where('status', $select);
        })
        ->orderBy($sort, $direction)
        ->paginate($perPage)
        ->withQueryString();

        return Inertia::render('Inventory/Device', [
            'device' => $data,
            'pageTitle' => 'Desktop & Laptop',
            'filters' => $request->only(['search', 'perPage', 'sort', 'direction']),
        ]);
    }

    public function action($id = null) {

        $isView = request()->routeIs('device.view');
        $deviceData = $id ? Device::find($id) : null;

        return Inertia::render('Action/ActionDevice', [
            'selectedDevice' => $deviceData,
            'isView' => $isView,
            'pageTitle' => $isView ? 'View Device Details' : ($id ? 'Edit Device' : 'Add Device'),
            'returnUrl' => session('device_index_url', route('device.index')),
        ]);
    }

    public function store(Request $request) {

        $redirectUrl = session('device_index_url', route('device.index'));
        $currentUser = $request->input('current_user');

        if (!empty($currentUser) && $currentUser !== 'IT') {
            $request->merge(['status' => 'In Use']);
        }

        $validated = $request->validate([
            'asset_tag' => ['required', 'string', 'max:255', 'unique:desktop_laptop,asset_tag'],
            'device_type' => ['required', 'string'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:desktop_laptop,serial_number'],
            'processor' => ['required', 'string', 'max:255'],
            'ram' => ['required', 'string'],
            'storage' => ['required', 'string'],
            'current_user' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', Rule::in(Device::statuses())],
            'date_of_asset_transfer' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['current_user'] = $request->filled('current_user') ? $request->current_user : 'IT';
        $validated['department'] = $request->filled('department') ? $request->department : 'IT';

        Device::create($validated);

        return redirect($redirectUrl)->with('success', 'Device successfully added.');
    }

    public function update(Request $request, Device $device) {

        $redirectUrl = session('device_index_url', route('device.index'));
        $currentUser = $request->input('current_user');

        if (!empty($currentUser) && $currentUser !== 'IT') {
            $request->merge(['status' => 'In Use']);
        }

        $validated = $request->validate([
            'asset_tag' => ['required', 'string', 'max:255', 'unique:desktop_laptop,asset_tag,' . $device->id],
            'device_type' => ['required', 'string'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:desktop_laptop,serial_number,' . $device->id],
            'processor' => ['required', 'string', 'max:255'],
            'ram' => ['required', 'string'],
            'storage' => ['required', 'string'],
            'current_user' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', Rule::in(Device::statuses())],
            'date_of_asset_transfer' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['current_user'] = $request->filled('current_user') ? $request->current_user : 'IT';
        $validated['department'] = $request->filled('department') ? $request->department : 'IT';

        $device->update($validated);

        $hasChanges = $device->wasChanged(['asset_tag', 'device_type', 'brand', 'model', 'serial_number', 'processor', 'ram', 'storage', 'current_user', 'department', 'remarks', 'status', 'date_of_asset_transfer']);

        $msgType = $hasChanges ? 'success' : 'warning';
        $msgContent = $hasChanges ? 'Device details updated successfully.' : 'No changes were made to the device details.';

        return redirect($redirectUrl)->with($msgType, $msgContent);
    }

    public function destroy($id) {
        $redirectUrl = session('device_index_url', route('device.index'));

        $device = Device::findOrFail($id);
        $device->delete();
        return redirect($redirectUrl)->with('success', 'Device deleted successfully.');
    }
}
