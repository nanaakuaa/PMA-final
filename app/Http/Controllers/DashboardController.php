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
            'total_passwords' => Password::where('user_id', $user->id)->count(),
            'total_folders' => Folder::where('user_id', $user->id)->count(),
            'recent_activities' => AuditLog::with('user')
                ->where('user_id', $user->id)
                ->latest()
                ->take(10)
                ->get(),
        ];

        $recentPasswords = Password::with(['folder', 'creator', 'updater'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentPasswords' => $recentPasswords,
        ]);
    }
}
