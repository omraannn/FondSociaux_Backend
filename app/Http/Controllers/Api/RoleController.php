<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /*|--------------------------------------------------------------------------
    | Fetch roles without permissions
    |-------------------------------------------------------------------------- */
    public function fetchRolesAll()
    {
        try {
            $roles = Role::with('permissions')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Roles fetched successfully',
                'roles' => $roles,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch roles',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /*|--------------------------------------------------------------------------
   | Fetch roles
   |-------------------------------------------------------------------------- */
    public function fetchRoles(Request $request) : JsonResponse
    {
        try {
            // Vérifier si l'utilisateur a la permission de voir les rôles
            if (!auth()->user()->can('voir les rôles')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de voir les rôles.',
                ], 403);
            }

            $query = Role::with('permissions');

            if ($request->has('name') && $request->input('name') !== null) {
                $query->where('name', 'like', '%' . $request->input('name') . '%');
            }

            $perPage = $request->query('per_page', 10);
            $roles = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Roles fetched successfully',
                'roles' => $roles->items(),
                'pagination' => [
                    'total' => $roles->total(),
                    'per_page' => $roles->perPage(),
                    'current_page' => $roles->currentPage(),
                    'last_page' => $roles->lastPage(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch roles',
                'error' => $e->getMessage(),
            ], 500);

        }
    }




    /*|--------------------------------------------------------------------------
    | Fetch permissions
    |-------------------------------------------------------------------------- */
    public function fetchPermissions()
    {
        try {
            $permissions = Permission::all();

            return response()->json([
                'status' => 'success',
                'message' => 'Permissions fetched successfully',
                'permissions' => $permissions,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch permissions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /*|--------------------------------------------------------------------------
    | Store role
    |-------------------------------------------------------------------------- */
    public function store(StoreRoleRequest $request)
    {
        try {

            if (!auth()->user()->can('créer des rôles')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de voir les roles.',
                ], 403);
            }

            $role = Role::create(['name' => $request->name]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Role created successfully',
                'role' => $role,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create role',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /*|--------------------------------------------------------------------------
    | Fetch role by ID
    |-------------------------------------------------------------------------- */
    public function show($id)
    {
        try {

            if (!auth()->user()->can('voir les rôles')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de voir les roles.',
                ], 403);
            }

            $role = Role::with('permissions')->find($id);

            if (!$role) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Role not found',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Role fetched successfully',
                'role' => $role,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch role',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /*|--------------------------------------------------------------------------
    | Update role
    |-------------------------------------------------------------------------- */
    public function update(UpdateRoleRequest $request, $id)
    {
        try {

            if (!auth()->user()->can('modifier des rôles')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de voir les roles.',
                ], 403);
            }

            $role = Role::find($id);

            if (!$role) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Role not found',
                ], 404);
            }

            $role->update(['name' => $request->name]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Role updated successfully',
                'role' => $role,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update role',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /*|--------------------------------------------------------------------------
    | Delete role
    |-------------------------------------------------------------------------- */
    public function destroy($id)
    {
        try {

            if (!auth()->user()->can('supprimer des rôles')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de voir les roles.',
                ], 403);
            }

            $role = Role::find($id);

            if (!$role) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Role not found',
                ], 404);
            }

            $role->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Role deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete role',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
