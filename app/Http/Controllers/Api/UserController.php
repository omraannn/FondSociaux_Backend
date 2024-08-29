<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /*|--------------------------------------------------------------------------
    | User create account
    |-------------------------------------------------------------------------- */
    public function register(RegisterRequest $request)
    {
        try {
            $frontImageName = $this->handleImageUpload($request, 'front_image');
            $backImageName = $this->handleImageUpload($request, 'back_image');

            $user = new User();
            $this->fillUserData($user, $request, $frontImageName, $backImageName);

            $user->status = 0;
            $user->save();

            $user->assignRole('GUEST');

            return response()->json([
                "status" => "success",
                "message" => "Employee registered successfully",
                "user" => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Registration failed",
                "error" => $e->getMessage()
            ], 422);
        }
    }


    /*|--------------------------------------------------------------------------
    |  Store employee
    |-------------------------------------------------------------------------- */
    public function storeEmployee(StoreEmployeeRequest $request)
    {

        if (!auth()->user()->can('créer un employé')) {
            return response()->json([
                'status' => '403',
                'message' => 'Vous n\'avez pas la permission de créer un employé.',
            ], 403);
        }

        try {
            $frontImageName = $this->handleImageUpload($request, 'front_image');
            $backImageName = $this->handleImageUpload($request, 'back_image');

            $user = new User();
            $this->fillUserDataWithoutPassword($user, $request, $frontImageName, $backImageName);

            $user->password = Hash::make('password');
            $user->status = 1;

            $user->save();

            $roles = json_decode($request->input('roles', '[]'), true);
            if (!is_array($roles)) {
                $roles = [];
            }

            // Assigner les rôles à l'utilisateur
            $user->syncRoles($roles);

            return response()->json([
                "status" => "success",
                "message" => "Employee registered successfully",
                "user" => $user
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                "status" => "error",
                "message" => "Validation failed",
                "errors" => $e->errors()
            ], 422);
        }
    }


    /*|--------------------------------------------------------------------------
    |  Login user
    |-------------------------------------------------------------------------- */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        $user = Auth::user();

        $roles = $user->getRoleNames();
        $permissions = $user->getAllPermissions()->pluck('name');

        $customClaims = [
            'roles' => $roles,
            'permissions' => $permissions,
            'id' => $user->id,
        ];
        $token = JWTAuth::claims($customClaims)->attempt($credentials);
        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }


    /*|--------------------------------------------------------------------------
    | Logout user
    |-------------------------------------------------------------------------- */
    public function logout()
    {
        try {
            if (Auth::check()) {
                auth()->logout();
                return response()->json([
                    "status" => "success",
                    "message" => "Logged out successfully"
                ]);
            } else {
                return response()->json([
                    "status" => "error",
                    "message" => "No active session found"
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to log out",
                "error" => $e->getMessage()
            ], 500);
        }
    }


    /*|--------------------------------------------------------------------------
    | Refresh JWT token
    |-------------------------------------------------------------------------- */
    public function refreshToken()
    {
        $newToken = auth()->refresh();
        return response()->json([
            "status" => "success",
            "message" => "access token refreshed",
            "token" => $newToken
        ]);
    }


    /*|--------------------------------------------------------------------------
   | Fetch pending employees
   |-------------------------------------------------------------------------- */
    public function fetchPendingEmployeeAll()
    {
        try {
            $pendingUsers = User::where('status', 0)
                ->orWhereHas('roles', function ($query) {
                    $query->where('name', 'GUEST');
                })
                ->with('roles')
                ->get();

            return response()->json([
                "status" => "success",
                "message" => "Pending users fetched successfully",
                "pendingUsers" => $pendingUsers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to fetch pending employees",
                "error" => $e->getMessage()
            ]);
        }
    }


    /*|--------------------------------------------------------------------------
    | Fetch pending employees
    |-------------------------------------------------------------------------- */
    public function fetchPendingEmployee()
    {
        try {
            if (!auth()->user()->can('voir les employés')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de voir les employés.',
                ], 403);
            }

            $pendingUsers = User::where('status', 0)
                ->orWhereHas('roles', function ($query) {
                    $query->where('name', 'GUEST');
                })
                ->with('roles')
                ->get();

            return response()->json([
                "status" => "success",
                "message" => "Pending users fetched successfully",
                "pendingUsers" => $pendingUsers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to fetch pending employees",
                "error" => $e->getMessage()
            ]);
        }
    }


    /*|--------------------------------------------------------------------------
    | Fetch confirmed employees without permission
    |-------------------------------------------------------------------------- */
    public function fetchConfirmedEmployeeAll()
    {
        try {
            $confirmedUsers = User::where('status', 1)
                ->where(function ($query) {
                    $query->whereDoesntHave('roles', function ($query) {
                        $query->where('name', 'RH');
                    })->orWhereHas('roles', function ($query) {
                            $query->havingRaw('COUNT(roles.id) > 1');
                    });
                })
                ->with('roles')
                ->get();

            return response()->json([
                "status" => "success",
                "message" => "Confirmed users fetched successfully",
                "confirmedUsers" => $confirmedUsers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to fetch confirmed employees",
                "error" => $e->getMessage()
            ]);
        }
    }


    /*|--------------------------------------------------------------------------
    | Fetch confirmed employees
    |-------------------------------------------------------------------------- */
   /* public function fetchConfirmedEmployee()
    {
        try {
            if (!auth()->user()->can('voir les employés')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de voir les employés.',
                ], 403);
            }

            $confirmedUsers = User::where('status', 1)
                ->where(function ($query) {
                    $query->whereDoesntHave('roles', function ($query) {
                        $query->where('name', 'RH');
                    })->orWhereHas('roles', function ($query) {
                        $query->havingRaw('COUNT(roles.id) > 1');
                    });
                })
                ->with('roles')
                ->get();

            return response()->json([
                "status" => "success",
                "message" => "Confirmed users fetched successfully",
                "confirmedUsers" => $confirmedUsers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to fetch confirmed employees",
                "error" => $e->getMessage()
            ]);
        }
    }*/

    public function fetchConfirmedEmployee(Request $request): JsonResponse
    {
        try {

            if (!auth()->user()->can('voir les employés')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de voir les employés.',
                ], 403);
            }

            $query = User::where('status', 1)
                ->where(function ($query) {
                    $query->whereDoesntHave('roles', function ($query) {
                        $query->where('name', 'RH');
                    })->orWhereHas('roles', function ($query) {
                        $query->havingRaw('COUNT(roles.id) > 1');
                    });
                })
                ->with('roles');

            if ($request->has('firstname') && $request->input('firstname') !== null) {
                $query->where('firstname', 'like', '%' . $request->input('firstname') . '%');
            }

            if ($request->has('lastname') && $request->input('lastname') !== null) {
                $query->where('lastname', 'like', '%' . $request->input('lastname') . '%');
            }

            if ($request->has('cin') && $request->input('cin') !== null) {
                $query->where('cin', 'like', '%' . $request->input('cin') . '%');
            }

            if ($request->has('address') && $request->input('address') !== null) {
                $query->where('address', 'like', '%' . $request->input('address') . '%');
            }

            if ($request->has('email') && $request->input('email') !== null) {
                $query->where('email', 'like', '%' . $request->input('email') . '%');
            }

            if ($request->has('tel') && $request->input('tel') !== null) {
                $query->where('tel', 'like', '%' . $request->input('tel') . '%');
            }

            $perPage = $request->query('per_page', 10);
            $confirmedUsers = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Confirmed employees fetched successfully',
                'confirmedUsers' => $confirmedUsers->items(),
                'pagination' => [
                    'total' => $confirmedUsers->total(),
                    'per_page' => $confirmedUsers->perPage(),
                    'current_page' => $confirmedUsers->currentPage(),
                    'last_page' => $confirmedUsers->lastPage(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch confirmed employees',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /*|--------------------------------------------------------------------------
    | Confirm employee request
    |-------------------------------------------------------------------------- */
    public function confirmEmployee($user_id)
    {
        try {
            if (!auth()->user()->can('confirmer un employé')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de confirmer un employé.',
                ], 403);
            }

            $user = User::findOrFail($user_id);
            $user->status = 1;
            $user->syncRoles('EMPLOYEE');

            $user->save();

            return response()->json([
                "status" => "success",
                "message" => "Employee activated successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to confirm employee",
                'error' => $e->getMessage()
            ], 500);
        }
    }


   /*|--------------------------------------------------------------------------
   | Delete employee
   |-------------------------------------------------------------------------- */
    public function deleteEmployee($user_id)
    {
        try {
            if (!auth()->user()->can('supprimer un employé')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de supprimer un employé.',
                ], 403);
            }

            $user = User::findOrFail($user_id);
            $user->delete();
            return response()->json([
                "status" => "success",
                "message" => "Employee deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to delete employee",
                'error' => $e->getMessage()
            ],500);
        }
    }


    /*|--------------------------------------------------------------------------
     | Update employee infos
     |-------------------------------------------------------------------------- */
    public function updateEmployeeInfos(UpdateEmployeeRequest $request, $user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            $user->firstname = $request->firstname ?? $user->firstname;
            $user->lastname = $request->lastname ?? $user->lastname;
            $user->email = $request->email ?? $user->email;
            $user->age = $request->age ?? $user->age;
            $user->cin = $request->cin ?? $user->cin;
            $user->address = $request->address ?? $user->address;
            $user->tel = $request->tel ?? $user->tel;


            $roles = json_decode($request->input('roles', '[]'), true);
            if (!is_array($roles)) {
                $roles = [];
            }
            $user->syncRoles($roles);

            if (count($roles) === 1 && in_array('GUEST', $roles)) {
                $user->status = 0;
            } else {
                $user->status = 1;
            }

            if ($request->hasFile('front_image')) {
                $frontImageName = $this->handleImageUpdate($request, $user, 'front_image');
                $user->front_image = $frontImageName;
            }
            if ($request->hasFile('back_image')) {
                $backImageName = $this->handleImageUpdate($request, $user, 'back_image');
                $user->back_image = $backImageName;
            }
            $user->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Employee updated successfully',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /*|--------------------------------------------------------------------------
    | Get profile -test-
    |-------------------------------------------------------------------------- */
    public function profile()
    {
        $userData = Auth()->user();

        return response()->json([
            "status" => "success",
            "profile" => $userData,
            "message" => "Profile success"
        ]);
    }


    /*|--------------------------------------------------------------------------
    | Update password
    |-------------------------------------------------------------------------- */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6',
        ]);

        try {
            $user = Auth::user();
            if (!Hash::check($request->current_password, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['The provided current password is incorrect.'],
                ]);
            }
            $user->password = Hash::make($request->new_password);
            $user->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Password updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update password',
                'error' => $e->getMessage()
            ], 500);
        }
    }


   /*|--------------------------------------------------------------------------
   | Private methods
   |-------------------------------------------------------------------------- */

    private function fillUserData(User $user, Request $request, $frontImageName, $backImageName)
    {
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->age = $request->age;
        $user->cin = $request->cin;
        $user->front_image = $frontImageName;
        $user->back_image = $backImageName;
        $user->address = $request->address;
        $user->tel = $request->tel;
    }

    private function fillUserDataWithoutPassword(User $user, Request $request, $frontImageName, $backImageName)
    {
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->age = $request->age;
        $user->cin = $request->cin;
        $user->front_image = $frontImageName;
        $user->back_image = $backImageName;
        $user->address = $request->address;
        $user->tel = $request->tel;
    }

    private function handleImageUpload(Request $request, $imageName)
    {
        if ($request->hasFile($imageName)) {
            $originalName = Str::random(20) . '.' . $request->file($imageName)->getClientOriginalExtension();
            $request->file($imageName)->storeAs('public/images/CIN', $originalName);
            return $originalName;
        }
        return null;
    }

    private function handleImageUpdate(Request $request, User $user, $imageName)
    {
        if ($request->hasFile($imageName)) {
            $this->deleteImageIfExists($user->$imageName);
            $originalName = Str::random(20) . '.' . $request->file($imageName)->getClientOriginalExtension();
            $request->file($imageName)->storeAs('public/images/CIN', $originalName);
            return $originalName;
        }
        return $user->$imageName;
    }

    private function deleteImageIfExists($imageName)
    {
        if ($imageName && Storage::exists('public/images/CIN/' . $imageName)) {
            Storage::delete('public/images/CIN/' . $imageName);
        }
    }
}
