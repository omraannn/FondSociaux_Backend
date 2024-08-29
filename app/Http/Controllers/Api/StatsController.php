<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatsController extends Controller
{
   /*|--------------------------------------------------------------------------
   | calculate Monthly & annual Refund Statistics with filter
   |--------------------------------------------------------------------------
   */
    public function calculateRefundStatistics(Request $request) : JsonResponse
    {
        try {
            if (!auth()->user()->can('voir les statistiques des utilisateurs')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de voir les statistiques des utilisateurs.',
                ], 403);
            }

            $period = $request->query('period', 'monthly');
            $employeeId = $request->query('employeeId');
            $typeFeeId = $request->query('feeTypeId');
            $year = $request->query('year', now()->year);

            $currentYear = now()->year;
            $totalAcceptedOverall = 0;
            $totalRejectedOverall = 0;
            $totalPendingOverall = 0;
            if ($period === 'monthly') {
                $monthlyStatistics = [];
                for ($month = 1; $month <= 12; $month++) {
                    $baseQuery = Refund::whereYear('created_at', $year)
                        ->whereMonth('created_at', $month);
                    if ($employeeId) {
                        $baseQuery->where('user_id', $employeeId);
                    }
                    if ($typeFeeId) {
                        $baseQuery->where('type_fee_id', $typeFeeId);
                    }
                    $totalAcceptedAmount = (clone $baseQuery)->where('status', 'accepted')->sum('reimbursement_amount');
                    $totalAcceptedOverall += $totalAcceptedAmount;

                    $totalRejectedAmount = (clone $baseQuery)->where('status', 'rejected')->sum('reimbursement_amount');
                    $totalRejectedOverall += $totalRejectedAmount;

                    $totalPendingAmount = (clone $baseQuery)->where('status', 'pending')->sum('reimbursement_amount');
                    $totalPendingOverall += $totalPendingAmount;
                    $monthlyStatistics[] = [
                        'month' => $month,
                        'total_accepted_amount' => $totalAcceptedAmount,
                        'total_rejected_amount' => $totalRejectedAmount,
                        'total_pending_amount' => $totalPendingAmount,
                    ];
                }
                return response()->json([
                    'status' => 'success',
                    'message' => 'Monthly refund statistics calculated successfully',
                    'statistics' => $monthlyStatistics,
                    'totals' => [
                        'total_accepted_overall' => $totalAcceptedOverall,
                        'total_rejected_overall' => $totalRejectedOverall,
                        'total_pending_overall' => $totalPendingOverall,
                    ]
                ]);
            } elseif ($period === 'annual') {
                $annualStatistics = [];
                for ($year = $currentYear - 12; $year <= $currentYear; $year++) {
                    $baseQuery = Refund::whereYear('created_at', $year);
                    if ($employeeId) {
                        $baseQuery->where('user_id', $employeeId);
                    }
                    if ($typeFeeId) {
                        $baseQuery->where('type_fee_id', $typeFeeId);
                    }
                    $totalAcceptedAmount = (clone $baseQuery)->where('status', 'accepted')->sum('reimbursement_amount');
                    $totalAcceptedOverall += $totalAcceptedAmount;

                    $totalRejectedAmount = (clone $baseQuery)->where('status', 'rejected')->sum('reimbursement_amount');
                    $totalRejectedOverall += $totalRejectedAmount;

                    $totalPendingAmount = (clone $baseQuery)->where('status', 'pending')->sum('reimbursement_amount');
                    $totalPendingOverall += $totalPendingAmount;
                    $annualStatistics[] = [
                        'year' => $year,
                        'total_accepted_amount' => $totalAcceptedAmount,
                        'total_rejected_amount' => $totalRejectedAmount,
                        'total_pending_amount' => $totalPendingAmount,
                    ];
                }
                return response()->json([
                    'status' => 'success',
                    'message' => 'Annual refund statistics calculated successfully',
                    'statistics' => $annualStatistics,
                    'totals' => [
                        'total_accepted_overall' => $totalAcceptedOverall,
                        'total_rejected_overall' => $totalRejectedOverall,
                        'total_pending_overall' => $totalPendingOverall,
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid period specified. Use "monthly" or "annual".',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to calculate refund statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




    /*|--------------------------------------------------------------------------
    | calculate Monthly Refund Statistics FOR AUTH USER
    |--------------------------------------------------------------------------
    */
    public function calculateMonthlyRefundStatisticsAuth(Request $request): JsonResponse
    {
        try {
            if (!auth()->user()->can('voir les statistiques par utilisateur')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de voir les statistiques des utilisateurs.',
                ], 403);
            }


            $year = $request->query('year', now()->year);
            $userId = Auth::id();
            $monthlyStatistics = [];
            for ($month = 1; $month <= 12; $month++) {
                $totalAcceptedAmount = Refund::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('user_id', $userId)
                    ->where('status', 'accepted')
                    ->sum('reimbursement_amount');
                $totalRejectedAmount = Refund::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('user_id', $userId)
                    ->where('status', 'rejected')
                    ->sum('reimbursement_amount');

                $totalPendingAmount = Refund::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('user_id', $userId)
                    ->where('status', 'pending')
                    ->sum('reimbursement_amount');

                $monthlyStatistics[] = [
                    'month' => $month,
                    'total_accepted_amount' => $totalAcceptedAmount,
                    'total_rejected_amount' => $totalRejectedAmount,
                    'total_pending_amount' => $totalPendingAmount,
                ];
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Monthly refund statistics for authenticated user calculated successfully',
                'monthly_statistics' => $monthlyStatistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to calculate monthly refund statistics for authenticated user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
