<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Services\EncryptionService;
use App\Models\Password;
use App\Models\Folder;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\PasswordShareController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\WebPushController;

Route::middleware('auth')->group(function () {

    // Password endpoints
    Route::apiResource('passwords', PasswordController::class);
    Route::post('passwords/{password}/share', [PasswordShareController::class, 'share']);
    Route::delete('passwords/{password}/shares/{share}', [PasswordShareController::class, 'revoke']);

    // Folder endpoints
    Route::apiResource('folders', FolderController::class);

    // Department endpoints
    Route::get('departments', [\App\Http\Controllers\Api\DepartmentController::class, 'index']);

    // Audit log endpoints (Admin Only)
    Route::middleware('admin')->group(function () {
        Route::get('audit-logs', [AuditLogController::class, 'index']);
        Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show']);
    });

    // User profile endpoints
    Route::get('profile', function (Request $request) {
        return $request->user();
    });

    Route::put('profile', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
        ]);

        $request->user()->update($validated);

        return $request->user();
    });

    // Password change endpoint
    Route::put('password', function (Request $request) {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $request->user()->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 422);
        }

        $request->user()->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    });

    // Export data endpoint
    Route::get('export', function (Request $request) {
        $format = $request->query('format', 'json');
        $exportService = app(\App\Services\ExportService::class);

        match ($format) {
            'csv' => $content = $exportService->exportAsCsv(),
            'xml' => $content = $exportService->exportAsXml(),
            'pdf' => $content = $exportService->exportAsPdf(),
            default => $content = $exportService->exportAsJson(),
        };

        $mimeTypes = [
            'json' => 'application/json',
            'csv' => 'text/csv',
            'xml' => 'application/xml',
            'pdf' => 'application/pdf',
        ];

        $extensions = [
            'json' => 'json',
            'csv' => 'csv',
            'xml' => 'xml',
            'pdf' => 'pdf',
        ];

        return response($content)
            ->header('Content-Type', $mimeTypes[$format] ?? 'application/json')
            ->header('Content-Disposition', 'attachment; filename="mvault-export.' . ($extensions[$format] ?? 'json') . '"');
    });

    // Delete account endpoint
    Route::delete('account', function (Request $request) {
        $user = $request->user();
        $user->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    });

    // Session Management
    Route::get('sessions', [\App\Http\Controllers\Api\SessionController::class, 'index']);
    Route::post('sessions/track', [\App\Http\Controllers\Api\SessionController::class, 'updateLastActivity']);
    Route::delete('sessions/{session}', [\App\Http\Controllers\Api\SessionController::class, 'revoke']);
    Route::post('sessions/revoke-all', [\App\Http\Controllers\Api\SessionController::class, 'revokeAll']);

    // Notification endpoints
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread', [NotificationController::class, 'getUnread']);
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('notifications/{notification}', [NotificationController::class, 'delete']);

    // WebPush endpoints
    Route::get('webpush/vapid-public-key', [WebPushController::class, 'vapidPublicKey']);
    Route::post('webpush/subscribe', [WebPushController::class, 'subscribe']);
    Route::post('webpush/unsubscribe', [WebPushController::class, 'unsubscribe']);
    Route::put('webpush/enabled', [WebPushController::class, 'setEnabled']);
});
