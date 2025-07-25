<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Staff
 *
 * @OA\Schema(
 *     schema="Staff",
 *     title="Staff",
 *     description="Staff model",
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="position_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="gender", type="string", example="male"),
 *     @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
 *     @OA\Property(property="pob", type="string", example="New York"),
 *     @OA\Property(property="address", type="string", example="123 Main St"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="nation_id_card", type="string", example="123456789"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
 * )
 *
 * @property int $id
 * @property int $position_id
 * @property string $name
 * @property string $gender
 * @property Carbon $dob
 * @property string $pob
 * @property string $address
 * @property string $phone
 * @property string $nation_id_card
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @package App\Models
 */
class Staff extends Model
{
    use SoftDeletes;

    protected $table = 'staff';

    protected $casts = [
        'position_id' => 'int',
        'dob' => 'datetime'
    ];

    protected $fillable = [
        'position_id',
        'name',
        'gender',
        'dob',
        'pob',
        'address',
        'phone',
        'nation_id_card'
    ];

    protected $dates = ['deleted_at'];
}
