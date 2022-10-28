<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @property integer id
 * @property string bank
 * @property string date
 * @property string date_format
 * @property string amount
 * @property string description
 * @method create(array $array)
 * @method static latest()
 */
class Map extends Model
{
    use HasFactory;

    protected array $fillable = ['bank', 'date', 'date_format', 'amount', 'description', 'type'];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['bank'] ?? false, fn($query, $bank) =>
            $query->where(fn($query) =>
                $query->where('bank', 'like', '%' . $bank . '%')
            )
        );

        $query->when($filters['type'] ?? false, fn($query, $type) =>
            $query->where(fn($query) =>
                $query->where('type', $type)
            )
        );
    }
}
