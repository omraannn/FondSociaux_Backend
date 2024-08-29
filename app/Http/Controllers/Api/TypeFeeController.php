<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTypeFeeRequest;
use App\Http\Requests\UpdateTypeFeeRequest;
use App\Models\TypeFee;
use Illuminate\Http\Request;

class TypeFeeController extends Controller
{
    /*|--------------------------------------------------------------------------
    | Fetch TypeFees without permission
    |-------------------------------------------------------------------------- */
    public function fetchTypeFeesAll()
    {
        try {
            $typeFees = TypeFee::with('category')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'TypeFees fetched successfully',
                'typeFees' => $typeFees,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch typeFees',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /*|--------------------------------------------------------------------------
    | Fetch TypeFees
    |-------------------------------------------------------------------------- */
    public function fetchTypeFees(Request $request)
    {
        try {

            if (!auth()->user()->can('voir les types de frais')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de créer une politique.',
                ], 403);
            }

            $query = TypeFee::with('category');

            if ($request->has('title') && $request->input('title') !== null) {
                $query->where('title', 'like', '%' . $request->input('title') . '%');
            }

            if ($request->has('category_id') && $request->input('category_id') !== null) {
                $query->where('category_id', $request->input('category_id'));
            }

            $perPage = $request->query('per_page', 10);
            $typeFees = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'TypeFees fetched successfully',
                'typeFees' => $typeFees->items(),
                'pagination' => [
                    'total' => $typeFees->total(),
                    'per_page' => $typeFees->perPage(),
                    'current_page' => $typeFees->currentPage(),
                    'last_page' => $typeFees->lastPage(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch typeFees',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /*|--------------------------------------------------------------------------
    | Store TypeFee
    |-------------------------------------------------------------------------- */
    public function storeTypeFee(StoreTypeFeeRequest $request)
    {
        try {

            if (!auth()->user()->can('créer un type de frais')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de créer une politique.',
                ], 403);
            }

            $typeFee = TypeFee::create([
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'percentage' => $request->percentage,
                'unit_price' => $request->unit_price,
                'ceiling' => $request->ceiling,
                'ceiling_type' => $request->ceiling_type,
                'refund_type' => $request->refund_type,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'TypeFee created successfully',
                'typeFee' => $typeFee,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create typeFee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /*|--------------------------------------------------------------------------
    | Fetch TypeFee with $id
    |-------------------------------------------------------------------------- */
    public function fetchTypeFee($id)
    {
        try {
            $typeFee = TypeFee::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'TypeFee fetched successfully',
                'typeFee' => $typeFee,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch typeFee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /*|--------------------------------------------------------------------------
    | Update TypeFee
    |-------------------------------------------------------------------------- */
    public function updateTypeFee(UpdateTypeFeeRequest $request, $id)
    {
        try {

            if (!auth()->user()->can('mettre à jour un type de frais')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de créer une politique.',
                ], 403);
            }

            $typeFee = TypeFee::findOrFail($id);

            $typeFee->category_id = $request->category_id ?? $typeFee->category_id;
            $typeFee->title = $request->title ?? $typeFee->title;
            $typeFee->description = $request->description ?? $typeFee->description;
            $typeFee->ceiling_type = $request->ceiling_type ?? $typeFee->ceiling_type;
            $typeFee->refund_type = $request->refund_type ?? $typeFee->refund_type;

            if($typeFee->ceiling_type === 'none') {
                $typeFee->ceiling = null;
            } else {
                $typeFee->ceiling = $request->ceiling ?? $typeFee->ceiling;
            }

            if($request->refund_type === 'percentage'){
                $typeFee->percentage = $request->percentage;
                $typeFee->unit_price = null;
            } else {
                $typeFee->percentage = null;
                $typeFee->unit_price = $request->unit_price;
            }

            $typeFee->save();

            return response()->json([
                'status' => 'success',
                'message' => 'TypeFee updated successfully',
                'typeFee' => $typeFee,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update typeFee',
                'error' => $e->getMessage(),
            ], 500);

        }
    }



    /*|--------------------------------------------------------------------------
    | Delete TypeFee
    |-------------------------------------------------------------------------- */
    public function deleteTypeFee($id)
    {
        try {

            if (!auth()->user()->can('supprimer un type de frais')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de créer une politique.',
                ], 403);
            }

            $typeFee = TypeFee::findOrFail($id);

            $typeFee->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'TypeFee deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete typeFee',
                'error' => $e->getMessage()
            ], 500);

        }
    }
}
