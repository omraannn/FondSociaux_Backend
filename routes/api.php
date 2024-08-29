<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PolicyController;
use App\Http\Controllers\Api\TypeFeeController;
use App\Http\Controllers\Api\RefundController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\RoleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*|--------------------------------------------------------------------------
| Routes pour les utilisateurs non authentifiés
|-------------------------------------------------------------------------- */
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('policies/all', [PolicyController::class, 'fetchPoliciesAll']);


/*|--------------------------------------------------------------------------
| Routes pour les utilisateurs authentifiés
|-------------------------------------------------------------------------- */
Route::group(['middleware' => ['auth:api']], function () {

    /*|--------------------------------------------------------------------------
    | Routes communes à tous les utilisateurs authentifiés
    |-------------------------------------------------------------------------- */
    Route::get('/logout', [UserController::class, 'logout']);
    Route::post('/refresh-token', [UserController::class, 'refreshToken']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/update-password', [UserController::class, 'updatePassword']);

    Route::group(['prefix' => 'stats',], function () {

        Route::get('/refund-statistics/{period?}/{employeeId?}/{typeFeeId?}', [StatsController::class, 'calculateRefundStatistics']);
        Route::get('/refund-monthly-statistics-auth', [StatsController::class, 'calculateMonthlyRefundStatisticsAuth']);

    });

    /*|--------------------------------------------------------------------------
    | Routes pour les administrateurs RH
    |-------------------------------------------------------------------------- */

    // Utilisateurs
    Route::group(['prefix' => 'users',], function () {

        Route::post('store-employee', [UserController::class, 'storeEmployee']);
        Route::post('confirm-employee/{user_id}', [UserController::class, 'confirmEmployee']);
        Route::delete('delete-employee/{user_id}', [UserController::class, 'deleteEmployee']);
        Route::get('pending-employee-all', [UserController::class, 'fetchPendingEmployeeAll']);
        Route::get('pending-employee', [UserController::class, 'fetchPendingEmployee']);
        Route::get('confirmed-employee-all', [UserController::class, 'fetchConfirmedEmployeeAll']);
        Route::get('confirmed-employee', [UserController::class, 'fetchConfirmedEmployee']);
        Route::post('update-employee/{user_id}', [UserController::class, 'updateEmployeeInfos']);

    });

    // Catégories
    Route::group(['prefix' => 'categories',], function () {

        Route::get('', [CategoryController::class, 'fetchCategories']);
        Route::get('all', [CategoryController::class, 'fetchCategoriesAll']);
        Route::get('fetch-category/{id}', [CategoryController::class, 'fetchCategory']);
        Route::post('store-category', [CategoryController::class, 'storeCategory']);
        Route::post('update-category/{id}', [CategoryController::class, 'updateCategory']);
        Route::delete('delete-category/{id}', [CategoryController::class, 'deleteCategory']);

    });

    // Politiques
    Route::group(['prefix' => 'policies',], function () {

        Route::get('policies', [PolicyController::class, 'fetchPolicies']);
        Route::post('store-policy', [PolicyController::class, 'storePolicy']);
        Route::post('update-policy/{id}', [PolicyController::class, 'updatePolicy']);
        Route::get('fetch-policy/{id}', [PolicyController::class, 'fetchPolicy']);
        Route::delete('delete-policy/{id}', [PolicyController::class, 'deletePolicy']);

    });

    // Types de frais
    Route::group(['prefix' => 'type-fees',], function () {

        Route::get('', [TypeFeeController::class, 'fetchTypeFees']);
        Route::get('all', [TypeFeeController::class, 'fetchTypeFeesAll']);
        Route::post('store-type-fee', [TypeFeeController::class, 'storeTypeFee']);
        Route::get('fetch-type-fee/{id}', [TypeFeeController::class, 'fetchTypeFee']);
        Route::post('update-type-fee/{id}', [TypeFeeController::class, 'updateTypeFee']);
        Route::delete('delete-type-fee/{id}', [TypeFeeController::class, 'deleteTypeFee']);

    });

    // Demandes de remboursement
    Route::group(['prefix' => 'refunds',], function () {

        Route::get('refund-demands', [RefundController::class, 'fetchRefundDemands']);
        Route::get("refunds-pending-all", [RefundController::class, 'fetchPendingRefundDemandsAll']);
        Route::get("refunds-pending", [RefundController::class, 'fetchPendingRefundDemands']);
        Route::post('refund/{id}/reject', [RefundController::class, 'rejectRefundDemand']);
        Route::post('refund/{id}/accept', [RefundController::class, 'acceptRefundDemand']);
        Route::post('refunds-store-by-RH', [RefundController::class, 'storeRefundForUser']);
        Route::post('/refund-delete/{id}', [RefundController::class, 'deleteRefundDemand']);
        Route::post('/refund-update-by-rh/{id}/{user_id}', [RefundController::class, 'updateRefundByRh']);
        Route::post('/refunds-update-payed', [RefundController::class, 'updatePayedStatus']);

    });

    // GESTIONS DES ROLES
    Route::group(['prefix' => 'roles',], function () {

        Route::get('', [RoleController::class, 'fetchRoles']);
        Route::get('all', [RoleController::class, 'fetchRolesAll']);
        Route::get('permissions', [RoleController::class, 'fetchPermissions']);
        Route::post('store-role', [RoleController::class, 'store']);
        Route::post('roles/{id}', [RoleController::class, 'update']);
        Route::delete('roles/{id}', [RoleController::class, 'destroy']);

    });


    /*|--------------------------------------------------------------------------
    | Routes pour les employés
    |-------------------------------------------------------------------------- */
    // Remboursements
    Route::group(['prefix' => 'refunds-employee',], function () {

        Route::post('/refund-store', [RefundController::class, 'storeRefundByUser']);
        Route::get('/refunds-last-5', [RefundController::class, 'fetchLast8RefundDemands']);
        Route::get('/refunds-auth', [RefundController::class, 'fetchAuthRefunds']);
        Route::post('/refund-cancel/{id}', [RefundController::class, 'cancelRefundDemand']);
        Route::post('/refund-update/{id}', [RefundController::class, 'updateRefundByUser']);

    });
});
