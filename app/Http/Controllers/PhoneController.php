<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Phone;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class PhoneController extends Controller
{
    public function showPhone(Request $request) {
        if(!Auth::check()) {
            return redirect('/');
        }

        session(['phone_index_url' => $request->fullUrl()]);

        $search = $request->input('search');
        $perPage = $request->input('perPage', 10);
        $select = $request->input('select');

        $sort = in_array($request->input('sort'), ['id', 'asset_tag', 'serial_number', 'brand', 'model', 'status'])
                ? $request->input('sort')
                : 'id';
        $direction = $request->input('direction', 'desc');

        $data = Phone::when($search, function ($query, $search) {
            $query->where('asset_tag', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('current_number', 'like', "%{$search}%")
                  ->orWhere('current_user', 'like', "%{$search}%");
        })
        ->when($select, function ($query, $select) {
            $query->where('status', $select);
        })
        ->orderBy($sort, $direction)
        ->paginate($perPage)
        ->withQueryString();

        return Inertia::render('Inventory/Phone', [
            'phone' => $data,
            'pageTitle' => 'Phone',
            'filters' => $request->only(['search', 'perPage', 'sort', 'direction']),
        ]);
    }

    public function action($id = null) {
        $phoneData = $id ? Phone::find($id) : null;

        return Inertia::render('Action/ActionPhone', [
            'selectedPhone' => $phoneData,
            'pageTitle' => $id ? 'Edit Phone' : 'Add Phone',
            'returnUrl' => session('phone_index_url', route('phone.index')),
        ]);
    }


    public function view(Request $request, $id) {
        $phoneData = Phone::findOrFail($id);

        $search = $request->input('search');
        $perPage = $request->input('perPage', 10);

        $sort = in_array($request->input('sort'), ['id', 'created_at', 'user_id'])
                ? $request->input('sort')
                : 'id';

        $direction = $request->input('direction', 'desc');

        $histories = $phoneData->histories()
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

        return Inertia::render('View/ViewPhone', [
            'viewPhone' => $phoneData,
            'assetLogs' => $histories,
            'pageTitle' => 'View Phone',
            'filters' => $request->only(['search', 'perPage', 'sort', 'direction']),
            'returnUrl' => session('phone_index_url', route('phone.index')),
        ]);
    }

    public function store(Request $request) {
        $redirectUrl = session('phone_index_url', route('phone.index'));

        $currentUser = $request->input('current_user');

        if (!empty($currentUser) && $currentUser !== 'IT') {
            $request->merge(['status' => 'In Use']);
        }

        $validated = $request->validate([
            'asset_tag' => ['required', 'string', 'max:255', 'unique:ip_phone,asset_tag'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:ip_phone,serial_number'],
            'current_number' => ['required', 'string', 'unique:ip_phone,current_number'],
            'current_user' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', Rule::in(Phone::statuses())],
            'date_of_asset_transfer' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['current_user'] = $request->filled('current_user') ? $request->current_user : 'IT';
        $validated['department'] = $request->filled('department') ? $request->department : 'IT';

        Phone::create($validated);
        return redirect($redirectUrl)->with('success', 'Phone successfully added.');
    }

    public function update(Request $request, Phone $phone) {

        $redirectUrl = session('phone_index_url', route('phone.index'));

        $currentUser = $request->input('current_user');
        if (!empty($currentUser) && $currentUser !== 'IT') {
            $request->merge(['status' => 'In Use']);
        }

        $validated = $request->validate([
            'asset_tag' => ['required', 'string', 'max:255', 'unique:ip_phone,asset_tag,' . $phone->id],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:ip_phone,serial_number,' . $phone->id],
            'current_number' => ['required', 'string', 'unique:ip_phone,current_number,' . $phone->id],
            'current_user' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', Rule::in(Phone::statuses())],
            'date_of_asset_transfer' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['current_user'] = $request->filled('current_user') ? $request->current_user : 'IT';
        $validated['department'] = $request->filled('department') ? $request->department : 'IT';

        $phone->update($validated);

        $hasChanges = $phone->wasChanged(['asset_tag', 'device_type', 'brand', 'model', 'serial_number', 'current_number', 'current_user', 'department', 'remarks', 'status', 'date_of_asset_transfer']);

        $msgType = $hasChanges ? 'success' : 'warning';
        $msgContent = $hasChanges ? 'Phone details updated successfully.' : 'No changes were made to the phone details.';

        return redirect($redirectUrl)->with($msgType, $msgContent);
    }

    public function destroy($id) {
        $redirectUrl = session('phone_index_url', route('phone.index'));

        $phone = Phone::findOrFail($id);
        $phone->delete();
        return redirect($redirectUrl)->with('success', 'Phone deleted successfully.');
    }
}
