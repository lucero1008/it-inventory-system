<?php

namespace App\Http\Controllers;


use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function showDashboard() {
        // sleep(2);
        if(!Auth::check()) {
            return Inertia::location('/');
        }

        return Inertia::render('Dashboard', [
            'pageTitle' => 'Dashboard'
        ]);
    }
}
