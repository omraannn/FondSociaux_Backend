<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'type_fee_id',
        'amount_spent',
        'expense_date',
        'HR_comment',
        'status',
        'payed',
        'reimbursement_amount',
        'quantity'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function typeFee()
    {
        return $this->belongsTo(TypeFee::class);
    }

    public function refundDocuments()
    {
        return $this->hasMany(RefundDocuments::class);
    }
}
