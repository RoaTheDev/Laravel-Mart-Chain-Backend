<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 *
 * @OA\Schema(
 *     schema="Category",
 *     title="Category",
 *     description="Category model",
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="name", type="string", example="Electronics"),
 *     @OA\Property(property="description", type="string", example="Category for electronic items"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 * )
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Category extends Model
{
    protected $table = 'category';

    protected $fillable = [
        'name',
        'description'
    ];
}
