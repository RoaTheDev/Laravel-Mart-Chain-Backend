<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class InvoiceItem
 *
 * @OA\Schema(
 *     schema="InvoiceItem",
 *     title="InvoiceItem",
 *     description="InvoiceItem model",
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="invoice_id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="qty", type="integer", example=2),
 *     @OA\Property(property="price", type="number", format="float", example=499.99),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
 * )
 *
 * @property int $id
 * @property int $invoice_id
 * @property int $product_id
 * @property int $qty
 * @property float $price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @package App\Models
 */
class InvoiceItem extends Model
{
    use SoftDeletes;

    protected $table = 'invoice_item';

    protected $casts = [
        'invoice_id' => 'int',
        'product_id' => 'int',
        'qty' => 'int',
        'price' => 'float'
    ];

    protected $fillable = [
        'invoice_id',
        'product_id',
        'qty',
        'price'
    ];

    protected $dates = ['deleted_at'];
}
