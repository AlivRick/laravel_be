<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $roles = Role::all();
        return $this->createSuccessResponse($roles);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:50|unique:role,role_name',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first());
        }

        $role = Role::create($request->all());
        return $this->createSuccessResponse($role, 201, 'Role created successfully');
    }

    public function show($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return $this->createErrorResponse('Role not found', 404);
        }
        return $this->createSuccessResponse($role);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:50|unique:role,role_name,' . $id . ',role_id',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first());
        }

        $role = Role::find($id);
        if (!$role) {
            return $this->createErrorResponse('Role not found', 404);
        }

        $role->update($request->all());
        return $this->createSuccessResponse($role, 200, 'Role updated successfully');
    }

    public function destroy($id)
    {
        $role = Role::where('is_active', true)->find($id);
        if (!$role) {
            return $this->createErrorResponse('Role not found', 404);
        }

        $role->update(['is_active' => false]);
        return $this->createSuccessResponse(null, 200, 'Role deleted successfully');
    }
} 