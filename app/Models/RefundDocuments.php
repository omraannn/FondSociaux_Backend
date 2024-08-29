<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundDocuments extends Model
{
    use HasFactory;

    protected $fillable = [
        'refund_id',
        'document_path',
    ];

    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }
}
