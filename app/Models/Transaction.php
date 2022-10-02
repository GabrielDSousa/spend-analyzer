<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @property integer id
 * @property mixed date
 * @property float amount
 * @property string description
 * @property string file
 * @property string type
 * @property string bank
 * @method static latest()
 * @method create(array $array)
 */
class Transaction extends Model
{
    use HasFactory;

    protected array $fillable = ['date', 'amount', 'description', 'file', 'type', 'bank'];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['date'] ?? false, fn($query, $date) =>
            $query->where(fn($query) =>
                $query->where('date', $date)
            )
        );

        $query->when($filters['description'] ?? false, fn($query, $description) =>
            $query->where(fn($query) =>
                $query->where('description', 'like', '%' . $description . '%')
            )
        );

        $query->when($filters['file'] ?? false, fn($query, $file) =>
            $query->where(fn($query) =>
                $query->where('file', $file)
            )
        );

        $query->when($filters['type'] ?? false, fn($query, $type) =>
            $query->where(fn($query) =>
                $query->where('type', $type)
            )
        );

        $query->when($filters['bank'] ?? false, fn($query, $bank) =>
            $query->where(fn($query) =>
                $query->where('bank', $bank)
            )
        );

        $query->when($filters['user_id'] ?? false, fn($query, $user) =>
            $query->where(fn($query) =>
                $query->where('user_id', $user)
            )
        );
    }
}
