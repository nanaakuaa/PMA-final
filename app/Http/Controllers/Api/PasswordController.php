<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePasswordRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Password;
use App\Services\EncryptionService;
use App\Services\AuditService;
use App\Events\PasswordCreated;
use App\Events\PasswordUpdated;
use App\Events\PasswordDeleted;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PasswordController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private EncryptionService $encryptionService,
        private AuditService $auditService
    ) {}

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 25);
        $user = $request->user();

        $passwords = Password::query()
            ->with(['folder', 'creator', 'updater', 'department'])
            ->where(function($q) use ($user) {
                // Show company-wide passwords OR passwords in user's department
                $q->where('is_company_wide', true)
                  ->orWhere(function($subQ) use ($user) {
                      $subQ->where('is_company_wide', false)
                           ->where('department_id', $user->department_id);
                  });
            })
            ->when($request->folder_id, fn($q) => $q->where('folder_id', $request->folder_id))
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate($perPage);

        return response()->json($passwords);
    }

    public function store(StorePasswordRequest $request)
    {
        $password = Password::create([
            'user_id' => $request->user()->id,
            'folder_id' => $request->folder_id,
            'title' => $request->title,
            'username' => $request->username,
            'password' => $this->encryptionService->encrypt($request->password),
            'url' => $request->url,
            'notes' => $request->notes ? $this->encryptionService->encrypt($request->notes) : null,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
            'department_id' => $request->department_id,
            'is_company_wide' => $request->is_company_wide ?? true,
        ]);

        $password->load(['creator', 'updater', 'department']);

        $this->auditService->log('password_created', $password);

        // Dispatch event to notify other users
        PasswordCreated::dispatch($password, $request->user());

        return response()->json($password, 201);
    }

    public function show(Password $password)
    {
        $this->authorize('view', $password);

        $password->password = $this->encryptionService->decrypt($password->password);
        if ($password->notes) {
            $password->notes = $this->encryptionService->decrypt($password->notes);
        }

        // Expose sensitive fields only for this response after decryption
        $password->makeVisible(['password', 'notes']);

        $this->auditService->log('password_viewed', $password);

        return response()->json($password);
    }

    public function update(UpdatePasswordRequest $request, Password $password)
    {
        $this->authorize('update', $password);

        $password->update([
            'folder_id' => $request->folder_id,
            'title' => $request->title,
            'username' => $request->username,
            'password' => $request->filled('password')
                ? $this->encryptionService->encrypt($request->password)
                : $password->password,
            'url' => $request->url,
            'notes' => $request->notes ? $this->encryptionService->encrypt($request->notes) : null,
            'updated_by' => $request->user()->id,
        ]);

        $password->load(['creator', 'updater']);

        $this->auditService->log('password_updated', $password);

        // Dispatch event to notify other users
        PasswordUpdated::dispatch($password, $request->user());

        return response()->json($password);
    }

    public function destroy(Password $password)
    {
        $this->authorize('delete', $password);

        $this->auditService->log('password_deleted', $password);

        // Dispatch event to notify other users before deletion
        PasswordDeleted::dispatch($password, auth()->user());

        $password->delete();

        return response()->json(null, 204);
    }
}
