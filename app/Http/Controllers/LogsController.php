<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogsController extends Controller
{
    public function showLogs(Request $request) {
        if(!Auth::check()) {
            return redirect('/');
        }

        $search = $request->input('search');
        $perPage = $request->input('perPage', 10);
        $selectAction = $request->input('selectAction');

        $logs = Logs::when($search, function ($query, $search) {
            $query->where('module', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('created_at', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%");
        })
        ->when($selectAction, function ($query, $selectAction) {
            $query->where('action', $selectAction);
        })
        ->orderBy('id', 'desc')
        ->paginate($perPage)
        ->withQueryString();

        return Inertia::render('Logs', [
            'logs' => $logs,
            'pageTitle' => 'Activity Logs',
            'filters' => $request->only(['search', 'selectAction', 'perPage']),
        ]);
    }
}
