<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Branch
 *
 * @OA\Schema(
 *     schema="Branch",
 *     title="Branch",
 *     description="Branch model",
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="name", type="string", example="Phnom Penh Branch"),
 *     @OA\Property(property="location", type="string", example="Phnom Penh"),
 *     @OA\Property(property="contact_number", type="string", example="012345678"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
 * )
 *
 * @property int $id
 * @property string $name
 * @property string $location
 * @property string $contact_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @package App\Models
 */
class Branch extends Model
{
    use SoftDeletes;

    protected $table = 'branch';

    protected $fillable = [
        'name',
        'location',
        'contact_number'
    ];

    protected array $dates = ['deleted_at'];
}
