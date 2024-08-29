<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeFee extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id',
        'title',
        'description',
        'percentage',
        'unit_price',
        'refund_type',
        'ceiling',
        'ceiling_type'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
