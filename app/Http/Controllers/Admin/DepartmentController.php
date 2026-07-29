<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 25);

        $departments = Department::withCount('users')
            ->orderBy('name')
            ->paginate($perPage);

        return inertia('Admin/Departments', [
            'departments' => $departments
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments'],
            'description' => ['nullable', 'string'],
        ]);

        $department = Department::create($validated);

        return redirect()->back()->with('success', 'Department created successfully');
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name,' . $department->id],
            'description' => ['nullable', 'string'],
        ]);

        $department->update($validated);

        return redirect()->back()->with('success', 'Department updated successfully');
    }

    public function destroy(Department $department)
    {
        if ($department->users()->count() > 0) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete department with users']);
        }

        $department->delete();

        return redirect()->back()->with('success', 'Department deleted successfully');
    }
}
