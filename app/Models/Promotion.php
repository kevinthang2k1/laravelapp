<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\SoftDeletes;


class Promotion extends Model
{
    use HasFactory,QueryScopes,SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'code',
        'description',
        'method',
        'discountInformation',
        'neverEndDate',
        'startDate',
        'endDate',
        'publish',
        'order',
    ];

    protected $casts = [
        'discountInformation' => 'json',
    ];

    protected $table = 'promotions';

    public function products(){
        return $this->belongsToMany(Promotion::class, 'promotion_product_variant' , 'promotion_id' , 'product_id')
        ->withPivot(
            'product_variant_id',
            'model',
        )->withTimestamps();
    }
}
