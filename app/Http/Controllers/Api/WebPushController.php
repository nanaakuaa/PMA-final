<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebPushController extends Controller
{
    public function vapidPublicKey()
    {
        return response(config('webpush.vapid.public_key'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $user = $request->user();
        $user->updatePushSubscription(
            $request->input('endpoint'),
            $request->input('keys.p256dh'),
            $request->input('keys.auth')
        );

        return response()->json(['success' => true]);
    }

    public function unsubscribe(Request $request)
    {
        $request->validate(['endpoint' => 'required|string']);
        $request->user()->deletePushSubscription($request->input('endpoint'));

        return response()->json(['success' => true]);
    }

    public function setEnabled(Request $request)
    {
        $request->validate(['enabled' => 'required|boolean']);
        $user = $request->user();
        $user->web_push_enabled = $request->boolean('enabled');
        $user->save();
        return response()->json(['success' => true, 'enabled' => $user->web_push_enabled]);
    }
}
