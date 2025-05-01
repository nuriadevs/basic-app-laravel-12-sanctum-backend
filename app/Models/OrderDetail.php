<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderDetail
 *
 * Represents an order detail in the application.
 *
 * @package App\Models
 */
class OrderDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['order_id', 'product_id', 'total_amount', 'quantity'];


    /**
     * Get the order associated with the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The order model relationship.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    /**
     * Get the product associated with the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The product model relationship.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
