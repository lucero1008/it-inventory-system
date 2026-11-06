<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Assets;
use App\Actions\CreateAssetAction;
use App\Actions\UpdateAssetAction;
use App\Observers\AssetsObserver;

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

        $data = Assets::when($search, function ($query, $search) {
            $query->where('asset_tag', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('current_user', 'like', "%{$search}%");
        })
        ->when($select, function ($query, $select) {
            $query->where('status', $select);
        })
        ->with('desktopLaptopDetails')
        ->orderBy($sort, $direction)
        ->paginate($perPage)
        ->withQueryString();

        return Inertia::render('Inventory/Device', [
            'device' => $data,
            'pageTitle' => 'Desktop & Laptop',
            'filters' => $request->only(['search', 'perPage', 'select', 'sort', 'direction']),
        ]);
    }

    public function action(AssetsObserver $asset_tag, $id = null) {

        $assetTag = $asset_tag->generate();

        $deviceData = $id ? Assets::with('desktopLaptopDetails')->find($id) : null;

        return Inertia::render('Action/ActionDevice', [
            'selectedDevice' => $deviceData,
            'assetTag' => $assetTag,
            'pageTitle' => $id ? 'Edit Device' : 'Add Device',
            'returnUrl' => session('device_index_url', route('device.index')),
        ]);
    }

    public function view(Request $request, $id) {

        $deviceData = Assets::with('desktopLaptopDetails')->findOrFail($id);

        $search = $request->input('search');
        $perPage = $request->input('perPage', 10);

        $sort = in_array($request->input('sort'), ['id', 'created_at', 'user_id'])
                ? $request->input('sort')
                : 'id';

        $direction = $request->input('direction', 'desc');

        $histories = $deviceData->histories()
                    ->when($search, function ($query, $search) {
                        $query->where('id', 'like', "%{$search}%")
                              ->orWhere('details', 'like', "%{$search}%")
                              ->orWhereHas('user', function($userQuery) use ($search) {
                                    $userQuery->where('name', 'like', "%{$search}%")
                                              ->orWhere('username', 'like', "%{$search}%");
                              });
                    })
                    ->with('user')
                    ->orderBy($sort, $direction)
                    ->paginate($perPage)
                    ->withQueryString();

        return Inertia::render('View/ViewDevice', [
            'viewDevice' => $deviceData,
            'assetLogs' => $histories,
            'pageTitle' => 'View Device',
            'filters' => $request->only(['search', 'perPage', 'sort', 'direction']),
            'returnUrl' => session('device_index_url', route('device.index')),
        ]);
    }

    // Create
    public function store(Request $request, CreateAssetAction $createAssetAction) {

        $redirectUrl = session('device_index_url', route('device.index'));
        $currentUser = $request->input('current_user');

        if (!empty($currentUser) && $currentUser !== 'IT') {
            $request->merge(['status' => 'In Use']);
        }

        $validated = $request->validate([
            'asset_tag' => ['required', 'string', 'max:255', 'unique:assets,asset_tag'],
            'asset_type' => ['required', 'string'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:assets,serial_number'],
            'current_user' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'remark' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', Rule::in(Assets::statuses())],
            'date_of_asset_transfer' => ['nullable', 'string', 'max:255'],

            // Specific Detail fields
            'processor' => ['required', 'string', 'max:255'],
            'ram' => ['required', 'string'],
            'storage' => ['required', 'string'],
        ]);

        $validated['current_user'] = $request->filled('current_user') ? $request->current_user : 'IT';
        $validated['department'] = $request->filled('department') ? $request->department : 'IT';

        $createAssetAction->execute($validated);

        return redirect($redirectUrl)->with('success', 'Device successfully added.');
    }

    // Update
    public function update(Request $request, Assets $assets, UpdateAssetAction $updateAssetAction) {
        $redirectUrl = session('device_index_url', route('device.index'));
        $currentUser = $request->input('current_user');

        if (!empty($currentUser) && $currentUser !== 'IT') {
            $request->merge(['status' => 'In Use']);
        }

        $validated = $request->validate([
            'asset_tag' => ['required', 'string', 'max:255', 'unique:assets,asset_tag,' . $assets->id],
            'asset_type' => ['required', 'string'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:assets,serial_number,' . $assets->id],
            'current_user' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'remark' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', Rule::in(Assets::statuses())],
            'date_of_asset_transfer' => ['nullable', 'string', 'max:255'],

            // Specific Detail fields
            'processor' => ['required', 'string', 'max:255'],
            'ram' => ['required', 'string'],
            'storage' => ['required', 'string'],
        ]);

        $validated['current_user'] = $request->filled('current_user') ? $request->current_user : 'IT';
        $validated['department'] = $request->filled('department') ? $request->department : 'IT';

        $hasChanges = $updateAssetAction->execute($assets, $validated);

        $msgType = $hasChanges->has_changes ? 'success' : 'warning';
        $msgContent = $hasChanges->has_changes ? 'Device details updated successfully.' : 'No changes were made to the device details.';

        return redirect($redirectUrl)->with($msgType, $msgContent);
    }

    // Delete
    public function destroy($id) {
        $redirectUrl = session('device_index_url', route('device.index'));

        $device = Assets::findOrFail($id);
        $device->delete();
        return redirect($redirectUrl)->with('success', 'Device deleted successfully.');
    }
}
