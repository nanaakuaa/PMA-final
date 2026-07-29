<?php

namespace App\Http\Controllers;

use App\Models\Password;
use App\Models\Folder;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $stats = [
            'total_passwords' => Password::count(),
            'total_folders' => Folder::count(),
            'total_employees' => \App\Models\User::count(),
            'recent_activities' => AuditLog::with('user')
                ->latest()
                ->take(10)
                ->get(),
        ];

        $recentPasswords = Password::with(['folder', 'creator', 'updater'])
            ->latest()
            ->take(10)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentPasswords' => $recentPasswords,
        ]);
    }
}
