<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFolderRequest;
use App\Models\Folder;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FolderController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private AuditService $auditService
    ) {}

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 25);

        $folders = Folder::query()
            ->where('user_id', $request->user()->id)
            ->withCount('passwords')
            ->latest()
            ->paginate($perPage);

        return response()->json($folders);
    }

    public function store(StoreFolderRequest $request)
    {
        $folder = Folder::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $this->auditService->log('folder_created', $folder);

        return response()->json($folder, 201);
    }

    public function show(Folder $folder)
    {
        $this->authorize('view', $folder);

        $folder->load('passwords');

        return response()->json($folder);
    }

    public function update(StoreFolderRequest $request, Folder $folder)
    {
        $this->authorize('update', $folder);

        $folder->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $this->auditService->log('folder_updated', $folder);

        return response()->json($folder);
    }

    public function destroy(Folder $folder)
    {
        $this->authorize('delete', $folder);

        $this->auditService->log('folder_deleted', $folder);

        $folder->delete();

        return response()->json(null, 204);
    }
}
