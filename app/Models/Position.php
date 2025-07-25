<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Position
 *
 * @OA\Schema(
 *     schema="Position",
 *     title="Position",
 *     description="Position model",
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="branch_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Manager"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Branch manager role"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
 * )
 *
 * @property int $id
 * @property int $branch_id
 * @property string $name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @package App\Models
 */
class Position extends Model
{
    use SoftDeletes;

    protected $table = 'position';

    protected $casts = [
        'branch_id' => 'int'
    ];

    protected $fillable = [
        'branch_id',
        'name',
        'description'
    ];

    protected $dates = ['deleted_at'];
}
