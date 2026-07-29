<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        $sessions = $request->user()
            ->sessions()
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'device_type' => $session->device_type,
                    'browser' => $session->browser,
                    'last_activity' => $session->last_activity,
                    'created_at' => $session->created_at,
                    'is_current' => $session->isCurrentSession(),
                ];
            });

        return response()->json($sessions);
    }

    public function createSession(Request $request): UserSession
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());

        return $request->user()->sessions()->create([
            'session_id' => session()->getId(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $agent->deviceType(),
            'browser' => $agent->browser(),
            'last_activity' => now(),
        ]);
    }

    public function updateLastActivity(Request $request)
    {
        $sessionId = session()->getId();
        $request->user()->sessions()
            ->where('session_id', $sessionId)
            ->update(['last_activity' => now()]);
    }

    public function revoke(Request $request, UserSession $session)
    {
        if ($session->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $session->delete();

        return response()->json(['message' => 'Session revoked']);
    }

    public function revokeAll(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required|string',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($validated['password'], $request->user()->password)) {
            return response()->json(['message' => 'Invalid password'], 422);
        }

        $currentSessionId = session()->getId();
        $request->user()->sessions()
            ->where('session_id', '!=', $currentSessionId)
            ->delete();

        return response()->json(['message' => 'All other sessions revoked']);
    }
}
