<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use Inertia\Inertia;
use App\Models\Monitor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class MonitorListController extends Controller
{
    public function showMonitor(Request $request) {
        if(!Auth::check()) {
            return redirect('/');
        }

        session(['monitor_index_url' => $request->fullUrl()]);

        $search = $request->input('search');
        $perPage = $request->input('perPage', 10);
        $select = $request->input('select');

        $sort = in_array($request->input('sort'), ['id', 'asset_tag', 'serial_number', 'brand', 'model', 'size', 'status'])
                ? $request->input('sort')
                : 'id';
        $direction = $request->input('direction', 'desc');

        $data = Monitor::when($search, function ($query, $search) {
            $query->where('asset_tag', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('size', 'like', "%{$search}%")
                  ->orWhere('current_user', 'like', "%{$search}%");
        })
        ->when($select, function ($query, $select) {
            $query->where('status', $select);
        })
        ->orderBy($sort, $direction)
        ->paginate($perPage)
        ->withQueryString();

        return Inertia::render('Inventory/Monitor', [
            'monitor' => $data,
            'pageTitle' => 'Monitor',
            'filters' => $request->only(['search', 'perPage', 'sort', 'direction']),
        ]);
    }

    public function action($id = null) {

        $isView = request()->routeIs('monitor.view');
        $monitorData = $id ? Monitor::find($id) : null;

        return Inertia::render('Action/ActionMonitor', [
            'selectedMonitor' => $monitorData,
            'isView' => $isView,
            'pageTitle' => $isView ? 'View Monitor Details' : ($id ? 'Edit Monitor' : 'Add Monitor'),
            'returnUrl' => session('monitor_index_url', route('monitor.index')),
        ]);
    }

    public function store(Request $request) {

        $redirectUrl = session('monitor_index_url', route('monitor.index'));

        $currentUser = $request->input('current_user');

        if (!empty($currentUser) && $currentUser !== 'IT') {
            $request->merge(['status' => 'In Use']);
        }

        $validated = $request->validate([
            'asset_tag' => ['required', 'string', 'max:255', 'unique:monitor,asset_tag'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:monitor,serial_number'],
            'size' => ['required', 'string'],
            'current_user' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', Rule::in(Monitor::statuses())],
            'date_of_asset_transfer' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['current_user'] = $request->filled('current_user') ? $request->current_user : 'IT';
        $validated['department'] = $request->filled('department') ? $request->department : 'IT';

        Monitor::create($validated);
        return redirect($redirectUrl)->with('success', 'Monitor successfully added.');
    }

    public function update(Request $request, Monitor $monitor) {

        $redirectUrl = session('monitor_index_url', route('monitor.index'));

        $currentUser = $request->input('current_user');
        if (!empty($currentUser) && $currentUser !== 'IT') {
            $request->merge(['status' => 'In Use']);
        }

        $validated = $request->validate([
            'asset_tag' => ['required', 'string', 'max:255', 'unique:monitor,asset_tag,' . $monitor->id],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:monitor,serial_number,' . $monitor->id],
            'size' => ['required', 'string'],
            'current_user' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', Rule::in(Monitor::statuses())],
            'date_of_asset_transfer' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['current_user'] = $request->filled('current_user') ? $request->current_user : 'IT';
        $validated['department'] = $request->filled('department') ? $request->department : 'IT';

        $monitor->update($validated);

        return redirect($redirectUrl)->with('success', 'Monitor details updated successfully.');
    }

    public function destroy($id) {
        $redirectUrl = session('monitor_index_url', route('monitor.index'));

        $monitor = Monitor::findOrFail($id);
        $monitor->delete();
        return redirect($redirectUrl)->with('success', 'Monitor deleted successfully.');
    }
}
