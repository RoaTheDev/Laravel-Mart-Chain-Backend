<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Product
 *
 * @OA\Schema(
 *     schema="Product",
 *     title="Product",
 *     description="Product model",
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="name", type="string", example="Laptop"),
 *     @OA\Property(property="cost", type="number", format="float", example=500.00),
 *     @OA\Property(property="price", type="number", format="float", example=699.99),
 *     @OA\Property(property="image", type="string", nullable=true, example="https://example.com/images/laptop.jpg"),
 *     @OA\Property(property="description", type="string", nullable=true, example="High-performance laptop"),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
 * )
 *
 * @property int $id
 * @property string $name
 * @property float $cost
 * @property float $price
 * @property string|null $image
 * @property string|null $description
 * @property int $category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @package App\Models
 */
class Product extends Model
{
    use SoftDeletes;

    protected $table = 'product';

    protected $casts = [
        'cost' => 'float',
        'price' => 'float',
        'category_id' => 'int'
    ];

    protected $fillable = [
        'name',
        'cost',
        'price',
        'image',
        'description',
        'category_id'
    ];

    protected $dates = ['deleted_at'];
}
