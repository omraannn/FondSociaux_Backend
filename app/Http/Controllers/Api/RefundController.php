<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcceptRefundRequest;
use App\Http\Requests\RejectRefundRequest;
use App\Http\Requests\StoreRefundByAdminRequest;
use App\Http\Requests\StoreRefundByUserRequest;
use App\Http\Requests\UpdateRefundByAdminRequest;
use App\Http\Requests\UpdateRefundByUserRequest;
use App\Jobs\SendRefundStatusNotification;
use App\Mail\RefundAdminNotification;
use App\Models\Refund;
use App\Models\RefundDocuments;
use App\Models\TypeFee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RefundController extends Controller
{

   /*|--------------------------------------------------------------------------
   |    Fetch refunds of auth user
   |-------------------------------------------------------------------------- */
    public function fetchAuthRefunds() : JsonResponse
    {
        try {

            if (!auth()->user()->can('voir les remboursements par utilisateur')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de voir les remboursements.',
                ], 403);
            }

            $user = auth()->user();

            $refunds = Refund::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->with(['refundDocuments', 'user', 'typeFee'])
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'refunds fetched successfully',
                'refunds' => $refunds,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch refunds',
                'error' => $e->getMessage(),
            ], 500);

        }
    }



   /*|--------------------------------------------------------------------------
   | Fetch last 8 Refund demands of the authenticated user
   |-------------------------------------------------------------------------- */
    public function fetchLast8RefundDemands() : JsonResponse
    {
        try {

            if (!auth()->user()->can('voir les remboursements par utilisateur')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de voir les remboursements.',
                ], 403);
            }

            $user = auth()->user();

            $refunds = Refund::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->with(['refundDocuments', 'user', 'typeFee'])
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'refunds fetched successfully',
                'refunds' => $refunds,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch refunds',
                'error' => $e->getMessage(),
            ], 500);

        }
    }



    /*|--------------------------------------------------------------------------
    |    Fetch all refund demands with permission
    |-------------------------------------------------------------------------- */
    public function fetchRefundDemands(Request $request) : JsonResponse
    {
        try {

            if (!auth()->user()->can('voir les remboursements des utilisateurs')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de voir les remboursements.',
                ], 403);
            }

          // $refunds = Refund::with(['refundDocuments', 'user', 'typeFee'])->orderBy('created_at', 'desc')->get();

            $query = Refund::with(['refundDocuments', 'user', 'typeFee']);

            // Filtrer par type de frais
            if ($request->has('type_fee_id') && $request->input('type_fee_id') !== null) {
                $query->where('type_fee_id', $request->input('type_fee_id'));
            }

            // Filtrer par CIN de l'utilisateur
            if ($request->has('cin') && $request->input('cin') !== null) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('cin', $request->input('cin'));
                });
            }

            // Filtrer par statut
            if ($request->has('status') && $request->input('status') !== null) {
                $query->where('status', $request->input('status'));
            }

            // Filtrer par paiement (payé ou non)
            if ($request->has('payed') && $request->input('payed') !== null) {
                $query->where('payed', $request->input('payed'));
            }

            // Filtrer par date (optionnel, par exemple pour une plage de dates)
            if ($request->has('date_start') && $request->input('date_start') !== null) {
                $query->where('created_at', '>=', $request->input('date_start'));
            }

            if ($request->has('date_end') && $request->input('date_end') !== null) {
                $query->where('created_at', '<=', $request->input('date_end'));
            }

            // Gestion de la pagination
            $perPage = $request->query('per_page', 10);
            $refunds = $query->orderBy('created_at', 'desc')->paginate($perPage);


            return response()->json([
                'status' => 'success',
                'message' => 'refunds fetched successfully',
                'refunds' => $refunds->items(),
                'pagination' => [
                    'total' => $refunds->total(),
                    'per_page' => $refunds->perPage(),
                    'current_page' => $refunds->currentPage(),
                    'last_page' => $refunds->lastPage(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch refunds',
                'error' => $e->getMessage(),
            ], 500);

        }
    }



    /*|--------------------------------------------------------------------------
    |   Fetch all refund demands without permissions
    |-------------------------------------------------------------------------- */
    public function fetchPendingRefundDemandsAll() : JsonResponse
    {
        try {

            $pendingRefunds = Refund::where('status', 'pending')->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'refunds fetched successfully',
                'pendingRefunds' => $pendingRefunds,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch refunds',
                'error' => $e->getMessage(),
            ], 500);

        }
    }



    /*|--------------------------------------------------------------------------
    |    Fetch pending refund demands with permissions
    |-------------------------------------------------------------------------- */
    public function fetchPendingRefundDemands() : JsonResponse
    {
        try {

            if (!auth()->user()->can('voir les remboursements')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de voir les remboursements.',
                ], 403);
            }

            $pendingRefunds = Refund::where('status', 'pending')->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'refunds fetched successfully',
                'pendingRefunds' => $pendingRefunds,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch refunds',
                'error' => $e->getMessage(),
            ], 500);

        }
    }



    /*|--------------------------------------------------------------------------
    |   Store Refund demand by admin for user
    |--------------------------------------------------------------------------
    */
    public function storeRefundForUser(StoreRefundByAdminRequest $request)
    {
        try {



            if (!auth()->user()->can('créer un remboursement pour un utilisateur')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de créer un remboursement pour un utilisateur.',
                ], 403);
            }

            return $this->handleRefundRequest($request, 'accepted');



        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Échec de la soumission de la demande de remboursement. ' . $e->getMessage(),
            ], 500);

        }
    }



    /*|--------------------------------------------------------------------------
    |   Store Refund demand by employee
    |--------------------------------------------------------------------------
    */
    public function storeRefundByUser(StoreRefundByUserRequest $request)
    {
        try {

            if (!auth()->user()->can('créer un remboursement par un utilisateur')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de créer un
                                  remboursement.',
                ], 403);
            }

            return $this->handleRefundRequest($request, 'pending');

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Échec de la soumission de la demande de remboursement. ' . $e->getMessage(),
            ], 500);

        }
    }



  /*|--------------------------------------------------------------------------
    |   Reject refund demand
    |--------------------------------------------------------------------------
    */
    public function rejectRefundDemand(RejectRefundRequest $request, $id)
    {
        try {

            if (!auth()->user()->can('rejeter un remboursement')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de rejeter un remboursement.',
                ], 403);
            }

            $refund = Refund::findOrFail($id);

            // Vérifier si la demande est déjà confirmée ou rejetée
            if ($refund->status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cette demande de remboursement a déjà été traitée.',
                ], 400);
            }

            $refund->status = 'rejected';
            $refund->ceiling_reached = 0;
            $refund->HR_comment = $request->comment;
            $refund->save();

            // Envoyer un e-mail
            dispatch(new SendRefundStatusNotification($refund));

            return response()->json([
                'status' => 'success',
                'message' => 'La demande de remboursement a été rejetée avec succès.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Échec du rejet de la demande de remboursement. ' . $e->getMessage(),
            ], 500);
        }
    }



    /*|--------------------------------------------------------------------------
    |   Accept refund demand
    |--------------------------------------------------------------------------
    */
    public function acceptRefundDemand(AcceptRefundRequest $request, $id)
    {
        try {

            if (!auth()->user()->can('accepter un remboursement')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de accepter un remboursement.',
                ], 403);
            }

            $refund = Refund::findOrFail($id);

            // Vérifier si la demande est déjà confirmée ou rejetée
            if ($refund->status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cette demande de remboursement a déjà été traitée.',
                ], 400);
            }

            $refund->status = 'accepted';
            $refund->HR_comment = $request->comment;
            $refund->save();

            // Envoyer un e-mail
            dispatch(new SendRefundStatusNotification($refund));

            return response()->json([
                'status' => 'success',
                'message' => 'La demande de remboursement a été confirmée avec succès.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Échec de la confirmation de la demande de remboursement. ' . $e->getMessage(),
            ], 500);
        }
    }



    /*|--------------------------------------------------------------------------
    |    Cancel refund demand by employee
    |--------------------------------------------------------------------------
    */
    public function cancelRefundDemand($id)
    {
        try {
            DB::beginTransaction();

            $refund = Refund::findOrFail($id);

            if ($refund->status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cette demande de remboursement ne peut pas être annulée car elle est déjà confirmée ou rejetée.',
                ], 400);
            }

            $documents = RefundDocuments::where('refund_id', $refund->id)->get();

            foreach ($documents as $document) {

                $fullPath = 'public/documents/' . $document->document_path;

                if (Storage::exists($fullPath)) {
                    Storage::delete($fullPath);
                }

                $document->delete();
            }

            $refund->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'La demande de remboursement a été annulée avec succès.',
            ]);

        } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => "Échec de l'annulation de la demande de remboursement. " . $e->getMessage(),
                ], 500);
        }
    }


    /*|--------------------------------------------------------------------------
    |     delete refund demand by admin
    |--------------------------------------------------------------------------
    */
    public function deleteRefundDemand($id)
    {
        try {
            DB::beginTransaction();

            if (!auth()->user()->can('supprimer un remboursement')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de supprimer un remboursement.',
                ], 403);
            }

            $refund = Refund::findOrFail($id);

            $documents = RefundDocuments::where('refund_id', $refund->id)->get();

            foreach ($documents as $document) {

                $fullPath = 'public/documents/' . $document->document_path;

                if (Storage::exists($fullPath)) {
                    Storage::delete($fullPath);
                }

                $document->delete();
            }

            $refund->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'La demande de remboursement a été annulée avec succès.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => "Échec de l'annulation de la demande de remboursement. " . $e->getMessage(),
            ], 500);
        }
    }


    /*|--------------------------------------------------------------------------
     | Update Refund Demand by user
     |--------------------------------------------------------------------------
     */
    public function updateRefundByUser(UpdateRefundByUserRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            if (!auth()->user()->can('mettre à jour un remboursement par un utilisateur')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de mettre à jour un remboursement par un utilisateur.',
                ], 403);
            }

            $refund = Refund::findOrFail($id);
            $typeFee = TypeFee::findOrFail($request->input('type_fee_id', $refund->type_fee_id));

            $this->processRefundUpdate($refund, $typeFee, $request);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'La demande de remboursement a été mise à jour avec succès.',
                'refund' => $refund,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Échec de la mise à jour de la demande de remboursement. Veuillez réessayer plus tard.',
            ], 500);
        }
    }



    /*|--------------------------------------------------------------------------
    | Update Refund Demand by admin Rh
    |--------------------------------------------------------------------------
    */
    public function updateRefundByRh(UpdateRefundByAdminRequest $request, $id, $user_id)
    {
        try {
            DB::beginTransaction();

            if (!auth()->user()->can('mettre à jour un remboursement pour un utilisateur')) {
                return response()->json([
                    'status' => '403',
                    'message' => 'Vous n\'avez pas la permission de mettre à jour un remboursement pour un utilisateur.',
                ], 403);
            }

            $refund = Refund::findOrFail($id);
            $typeFee = TypeFee::findOrFail($request->input('type_fee_id', $refund->type_fee_id));

            // Mettre à jour le user_id de la demande de remboursement
            $refund->user_id = $user_id;
            $refund->status = $request->status;

            $this->processRefundUpdate($refund, $typeFee, $request);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'La demande de remboursement a été mise à jour avec succès.',
                'refund' => $refund,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Échec de la mise à jour de la demande de remboursement. Veuillez réessayer plus tard.',
            ], 500);
        }
    }



    /*|--------------------------------------------------------------------------
   | Update refund payment status by admin
   |-------------------------------------------------------------------------- */
    public function updatePayedStatus(Request $request)
    {
        if (!auth()->user()->can('mettre à jour le statut payé')) {
            return response()->json([
                'status' => '403',
                'message' => 'Vous n\'avez pas la permission de mettre à jour le statut payé.',
            ], 403);
        }

        $ids = $request->input('ids');
        $payed = $request->input('payed');

        // Validate input
        if (!is_array($ids) || !is_bool($payed)) {
            return response()->json(['error' => 'Invalid input'], 400);
        }

        try {
            // Update the payed status
            Refund::whereIn('id', $ids)->update(['payed' => $payed]);

            return response()->json(['message' => 'Refund statuses updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating refund statuses'], 500);
        }
    }

    /*|--------------------------------------------------------------------------
    | Private functions
    |-------------------------------------------------------------------------- */
    private function processRefundUpdate(Refund $refund, TypeFee $typeFee, Request $request)
    {
        // Totale des demandes acceptées ou en attente de l'utilisateur dans ce type de frais
        $totalPendingOrAcceptedRefunds = Refund::where('user_id', $refund->user_id)
            ->where('type_fee_id', $refund->type_fee_id)
            ->where('id', '!=', $refund->id) // Exclusion de la demande courante
            ->whereIn('status', ['pending', 'accepted']);

        $currentYear = now()->year;
        $currentDate = now()->format('Y-m-d');

        if ($typeFee->ceiling_type === 'per_day') {
            $totalPendingOrAcceptedRefunds = $totalPendingOrAcceptedRefunds->whereDate('created_at', $currentDate);
        } elseif ($typeFee->ceiling_type === 'per_year') {
            $totalPendingOrAcceptedRefunds = $totalPendingOrAcceptedRefunds->whereYear('created_at', $currentYear);
        }
        $totalPendingOrAcceptedRefunds = $totalPendingOrAcceptedRefunds->sum('reimbursement_amount');
        $amountAvailable = ($typeFee->ceiling_type !== 'none' && $typeFee->ceiling !== null)
            ? $typeFee->ceiling - $totalPendingOrAcceptedRefunds
            : PHP_INT_MAX;

        // Calcul du montant de remboursement mis à jour
        $currentRequestAmount = $this->calculateReimbursementAmount($typeFee, $request);

        if ($currentRequestAmount > $amountAvailable) {
            $currentRequestAmount = $amountAvailable;
        }

        // Mise à jour de la demande de remboursement
        $refund->update($request->only([
            'amount_spent',
            'expense_date',
            'quantity',
            'subject',
            'message',
            'type_fee_id',
        ]));

        $refund->reimbursement_amount = $currentRequestAmount;
        $refund->ceiling_reached = ($typeFee->ceiling_type !== 'none' && $totalPendingOrAcceptedRefunds + $currentRequestAmount >= $typeFee->ceiling);

        // Get the IDs of the files that are currently associated with the refund
        $existingDocuments = RefundDocuments::where('refund_id', $refund->id)->pluck('id')->toArray();

        // Get the IDs of the files that are being sent in the request
        $uploadedDocumentIds = $request->input('uploaded_document_ids', []);

        if (!is_array($uploadedDocumentIds)) {
            $uploadedDocumentIds = json_decode($uploadedDocumentIds, true);
            if (!is_array($uploadedDocumentIds)) {
                $uploadedDocumentIds = [];
            }
        }

        $documentsToDelete = array_diff($existingDocuments, $uploadedDocumentIds);
        if (!empty($documentsToDelete)) {
            foreach ($documentsToDelete as $documentId) {
                $documentPath = RefundDocuments::where('id', $documentId)->value('document_path');
                if ($documentPath && Storage::exists('public/documents/' . $documentPath)) {
                    Storage::delete('public/documents/' . $documentPath);
                }
                RefundDocuments::where('id', $documentId)->delete();
            }
        }

        if ($request->hasFile('supporting_documents')) {
            foreach ($request->file('supporting_documents') as $file) {
                $documentPath = $this->handleFileUpload($file);
                RefundDocuments::create([
                    'refund_id' => $refund->id,
                    'document_path' => $documentPath,
                ]);
            }
        }
        $refund->save();
    }


    private function calculateReimbursementAmount(TypeFee $typeFee, Request $request)
    {
        if ($typeFee->refund_type === 'percentage') {
            return $request->amount_spent * ($typeFee->percentage / 100);
        } elseif ($typeFee->refund_type === 'per_unit') {
            return $typeFee->unit_price * $request->quantity;
        }
        return 0;
    }


    private function handleFileUpload($file)
    {
        $originalName = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/documents', $originalName);
        return $originalName;
    }


    private function handleRefundRequest(Request $request, $status)
    {
        $typeFee = TypeFee::findOrFail($request->type_fee_id);

        // Définition des règles de validation en fonction du type de remboursement
        $validationRules = [
            'quantity' => 'nullable|numeric',
            'amount_spent' => 'nullable|numeric',
        ];
        if ($typeFee->refund_type === 'percentage') {
            $validationRules['amount_spent'] = 'required|numeric';
        } elseif ($typeFee->refund_type === 'per_unit') {
            $validationRules['quantity'] = 'required|numeric';
        }

        $request->validate($validationRules);

        $currentYear = now()->year;
        $currentDate = now()->format('Y-m-d');

        // Vérification du plafond en fonction du type
        $totalPendingOrAcceptedRefunds = Refund::where('user_id', $request->user_id)
            ->where('type_fee_id', $request->type_fee_id)
            ->whereIn('status', ['pending', 'accepted']);

        if ($typeFee->ceiling_type === 'per_day') {
            $totalPendingOrAcceptedRefunds = $totalPendingOrAcceptedRefunds->whereDate('created_at', $currentDate);
        } elseif ($typeFee->ceiling_type === 'per_year') {
            $totalPendingOrAcceptedRefunds = $totalPendingOrAcceptedRefunds->whereYear('created_at', $currentYear);
        }

        // Vérification si le plafond est atteint
        if ($typeFee->ceiling_type !== 'none') {
            $totalPendingOrAcceptedRefunds = $totalPendingOrAcceptedRefunds->sum('reimbursement_amount');

            if ($typeFee->ceiling !== null && $totalPendingOrAcceptedRefunds >= $typeFee->ceiling) {
                return response()->json([
                    'status' => 'ceiling',
                    'message' => 'Le plafond pour ce type de frais a été atteint avec des demandes en attente ou acceptées.',
                ], 400);
            }

            $amountAvailable = $typeFee->ceiling - $totalPendingOrAcceptedRefunds;
        } else {
            // Si aucun plafond n'est défini, il est illimité
            $amountAvailable = PHP_INT_MAX; // Utilisation d'une valeur élevée pour représenter un plafond illimité
        }

        // Calcul du montant de la demande
        if ($typeFee->refund_type === 'percentage') {
            $currentRequestAmount = $request->amount_spent * ($typeFee->percentage / 100);
            if ($currentRequestAmount > $amountAvailable) {
                $currentRequestAmount = $amountAvailable;
            }
        } elseif ($typeFee->refund_type === 'per_unit') {
            $currentRequestAmount = $typeFee->unit_price * $request->quantity;
            if ($currentRequestAmount > $amountAvailable) {
                $currentRequestAmount = $amountAvailable;
            }
        }

        // Création de l'objet Refund
        $refund = new Refund();
        $refund->user_id = $request->user_id;
        $refund->subject = $request->subject;
        $refund->message = $request->message;
        $refund->quantity = $request->quantity;
        $refund->type_fee_id = $request->type_fee_id;
        $refund->amount_spent = $request->amount_spent;
        $refund->expense_date = $request->expense_date;
        $refund->reimbursement_amount = $currentRequestAmount;
        $refund->status = $status;
        $refund->ceiling_reached = ($typeFee->ceiling_type !== 'none' && $totalPendingOrAcceptedRefunds + $currentRequestAmount >= $typeFee->ceiling);
        $refund->save();

        if ($status === 'accepted') {
            dispatch(new SendRefundStatusNotification($refund));
        }

        // Gestion des documents de support
        foreach ($request->file('supporting_documents') as $file) {
            $documentPath = $this->handleFileUpload($file);
            RefundDocuments::create([
                'refund_id' => $refund->id,
                'document_path' => $documentPath,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'La demande de remboursement a été soumise avec succès.',
            'refund' => $refund,
        ], 201);
    }
}
