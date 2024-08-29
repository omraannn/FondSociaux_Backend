<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{


    /*|--------------------------------------------------------------------------
      |  Fetch all categories without permissions
      |-------------------------------------------------------------------------- */
    public function fetchCategoriesAll() : JsonResponse
    {
        try {

            $categories = Category::all();
            return response()->json([
                'status' => 'success',
                'message' => 'Categories fetched successfully',
                'categories' => $categories,
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage(),
            ], 500);

        }
    }



    /*|--------------------------------------------------------------------------
    |   Fetch Categories with permissions
    |-------------------------------------------------------------------------- */
    public function fetchCategories(Request $request) : JsonResponse
    {
        try {

            if (!auth()->user()->can('voir les catégories')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de voir les catégories.',
                ], 403);
            }

            $query = Category::query();

            if ($request->has('title') && $request->input('title') !== null) {
                $query->where('title', 'like', '%' . $request->input('title') . '%');
            }

            $perPage = $request->query('per_page', 10);
            $categories = $query->paginate($perPage);


            return response()->json([
                'status' => 'success',
                'message' => 'Categories fetched successfully',
                'categories' => $categories->items(),
                'pagination' => [
                    'total' => $categories->total(),
                    'per_page' => $categories->perPage(),
                    'current_page' => $categories->currentPage(),
                    'last_page' => $categories->lastPage(),
                ],
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /*|--------------------------------------------------------------------------
    |   Store category
    |-------------------------------------------------------------------------- */
    public function storeCategory(StoreCategoryRequest $request) : JsonResponse
    {
        try {
            if (!auth()->user()->can('créer une catégorie')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de créer une catégorie.',
                ], 403);
            }

            $category = Category::create([
                'title' => $request->title,
                'description' => $request->description,
                'active' => $request->active,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Category created successfully',
                'category' => $category,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /*|--------------------------------------------------------------------------
    | Fetch category with $id
    |-------------------------------------------------------------------------- */
    public function fetchCategory($id) : JsonResponse
    {

        try {
            if (!auth()->user()->can('voir les catégories')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de voir les catégories.',
                ], 403);
            }

            $category = Category::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Category fetched successfully',
                'category' => $category,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /*|--------------------------------------------------------------------------
    | Update category
    |-------------------------------------------------------------------------- */
    public function updateCategory(UpdateCategoryRequest $request, $id) : JsonResponse
    {
        try {
            if (!auth()->user()->can('mettre à jour une catégorie')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de mettre à jour une catégorie.',
                ], 403);
            }

            $category = Category::findOrFail($id);

            $category->title = $request->title ?? $category->title;
            $category->description = $request->description ?? $category->description;
            $category->active = $request->active ?? $category->active;

            $category->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Category updated successfully',
                'category' => $category,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /*|--------------------------------------------------------------------------
    | Delete category
    |-------------------------------------------------------------------------- */
    public function deleteCategory($id) : JsonResponse
    {
        try {
            if (!auth()->user()->can('supprimer une catégorie')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de supprimer une catégorie.',
                ], 403);
            }

            $category = Category::findOrFail($id);

            $category->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
