<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Printer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrinterListController extends Controller
{
    public function showPrinter(Request $request) {
        if(!Auth::check()) {
            return redirect('/');
        }

        session(['printer_index_url' => $request->fullUrl()]);

        $search = $request->input('search');
        $perPage = $request->input('perPage', 10);
        $select = $request->input('select');

        $sort = in_array($request->input('sort'), ['id', 'asset_tag', 'serial_number', 'brand', 'model', 'ownership', 'status'])
                ? $request->input('sort')
                : 'id';
        $direction = $request->input('direction', 'desc');

        $data = Printer::when($search, function ($query, $search) {
            $query->where('asset_tag', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('ownership', 'like', "%{$search}%")
                  ->orWhere('current_user', 'like', "%{$search}%");
        })
        ->when($select, function ($query, $select) {
            $query->where('status', $select);
        })
        ->orderBy($sort, $direction)
        ->paginate($perPage)
        ->withQueryString();

        return Inertia::render('Inventory/Printer', [
            'printer' => $data,
            'pageTitle' => 'Printer',
            'filters' => $request->only(['search', 'perPage', 'sort', 'direction']),
        ]);
    }

    public function action($id = null) {
        $isView = request()->routeIs('printer.view');

        $printerData = $id ? Printer::find($id) : null;

        return Inertia::render('Action/ActionPrinter', [
            'selectedPrinter' => $printerData,
            'isView' => $isView,
            'pageTitle' => $isView ? 'View Printer Details' : ($id ? 'Edit Printer' : 'Add Printer'),
            'returnUrl' => session('printer_index_url', route('printer.index')),
        ]);
    }

    public function store(Request $request) {
        $redirectUrl = session('printer_index_url', route('printer.index'));

        $validated = $request->validate([
            'asset_tag' => ['nullable', 'string', 'max:255', 'unique:printer,asset_tag'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:printer,serial_number'],
            'printer_type' => ['required', 'string'],
            'supplier' => ['required_if:ownership,Leased,Borrowed', 'string'],
            'ownership' => ['required', 'string'],
            'current_user' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string'],
            'date_of_asset_transfer' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['current_user'] = $request->filled('current_user') ? $request->current_user : 'IT';
        $validated['department'] = $request->filled('department') ? $request->department : 'IT';

        Printer::create($validated);

        return redirect($redirectUrl)->with('success', 'Printer successfully added.');
    }

    public function update(Request $request, Printer $printer) {
        $redirectUrl = session('printer_index_url', route('printer.index'));

        $validated = $request->validate([
            'asset_tag' => ['nullable', 'string', 'max:255', 'unique:printer,asset_tag,' . $printer->id],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:printer,serial_number,' . $printer->id],
            'printer_type' => ['required', 'string'],
            'supplier' => ['required_if:ownership,Leased,Borrowed', 'string'],
            'ownership' => ['required', 'string'],
            'current_user' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string'],
            'date_of_asset_transfer' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['current_user'] = $request->filled('current_user') ? $request->current_user : 'IT';
        $validated['department'] = $request->filled('department') ? $request->department : 'IT';

        $printer->update($validated);
        return redirect($redirectUrl)->with('success', 'Printer successfully updated.');
    }

    public function destroy($id) {
        $redirectUrl = session('printer_index_url', route('printer.index'));

        $device = Printer::findOrFail($id);
        $device->delete();

        return redirect($redirectUrl)->with('success', 'Printer deleted successfully.');
    }
}
