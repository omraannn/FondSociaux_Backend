<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePolicyRequest;
use App\Http\Requests\UpdatePolicyRequest;
use App\Models\Policy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    /*|--------------------------------------------------------------------------
    |   Fetch policies without permissions
    |-------------------------------------------------------------------------- */
    public function fetchPoliciesAll()
    {
        try {
            $policies = Policy::all();

            return response()->json([
                'status' => 'success',
                'message' => 'Policies fetched successfully',
                'policies' => $policies,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch policies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /*|--------------------------------------------------------------------------
    |   Fetch policies with permissions
    |-------------------------------------------------------------------------- */
    public function fetchPolicies(Request $request)
    {
        try {

            if (!auth()->user()->can('voir les politiques')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de créer une politique.',
                ], 403);
            }

            $query = Policy::query();

            if ($request->has('title') && $request->input('title') !== null) {
                $query->where('title', 'like', '%' . $request->input('title') . '%');
            }

            $perPage = $request->query('per_page', 10);
            $policies = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Policies fetched successfully',
                'policies' => $policies->items(),
                'pagination' => [
                    'total' => $policies->total(),
                    'per_page' => $policies->perPage(),
                    'current_page' => $policies->currentPage(),
                    'last_page' => $policies->lastPage(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch policies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /*|--------------------------------------------------------------------------
    |  Store policy
    |-------------------------------------------------------------------------- */
    public function storePolicy(StorePolicyRequest $request)
    {
        try {
            if (!auth()->user()->can('créer une politique')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de créer une politique.',
                ], 403);
            }

            $policy = Policy::create([
                'title' => $request->title,
                'description' => $request->description,
                'sub_description' => $request->sub_description,
                'active' => $request->active,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Policy created successfully',
                'policy' => $policy,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create policy',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /*|--------------------------------------------------------------------------
    |   Fetch policy with $id
    |-------------------------------------------------------------------------- */
    public function fetchPolicy($id)
    {
        try {
            if (!auth()->user()->can('voir les politiques')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de voir les politiques.',
                ], 403);
            }

            $policy = Policy::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Policy fetched successfully',
                'policy' => $policy,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch policy',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /*|--------------------------------------------------------------------------
    |   Update policy
    |-------------------------------------------------------------------------- */
    public function updatePolicy(UpdatePolicyRequest $request, $id)
    {
        try {
            if (!auth()->user()->can('mettre à jour une politique')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de mettre à jour une politique.',
                ], 403);
            }

            $policy = Policy::findOrFail($id);

            $policy->title = $request->title ?? $policy->title;
            $policy->description = $request->description ?? $policy->description;
            $policy->active = $request->active ?? $policy->active;
            $policy->sub_description = $request->sub_description ?? $policy->sub_description;

            $policy->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Policy updated successfully',
                'policy' => $policy,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update policy',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /*|--------------------------------------------------------------------------
    | Delete policy
    |-------------------------------------------------------------------------- */
    public function deletePolicy($id)
    {
        try {
            if (!auth()->user()->can('supprimer une politique')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de supprimer une politique.',
                ], 403);
            }

            $policy = Policy::findOrFail($id);

            $policy->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Policy deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete policy',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
