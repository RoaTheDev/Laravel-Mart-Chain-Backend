<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Invoice
 *
 * @OA\Schema(
 *     schema="Invoice",
 *     title="Invoice",
 *     description="Invoice model",
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="total", type="number", format="float", example=999.99),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
 * )
 *
 * @property int $id
 * @property int $user_id
 * @property float $total
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @package App\Models
 */
class Invoice extends Model
{
    use SoftDeletes;

    protected $table = 'invoice';

    protected $casts = [
        'user_id' => 'int',
        'total' => 'float'
    ];

    protected $fillable = [
        'user_id',
        'total'
    ];

    protected $dates = ['deleted_at'];
}
