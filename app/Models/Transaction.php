<?php

namespace App\Models;

use Carbon\Carbon;
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

    protected array $fillable = ['date', 'amount', 'description', 'file', 'type', 'bank', 'user_id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected array $casts = [
        'date' => 'datetime'
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['description'] ?? false, fn($query, $description) =>
            $query->where(fn($query) =>
                $query->where('description', 'like', '%' . $description . '%')
            )
        );

        $query->when($filters['file'] ?? false, fn($query, $file) =>
            $query->where(fn($query) =>
                $query->where('file', 'like', '%' . $file . '%')
            )
        );

        $query->when($filters['type'] ?? false, fn($query, $type) =>
            $query->where(fn($query) =>
                $query->where('type', $type)
            )
        );

        $query->when($filters['bank'] ?? false, fn($query, $bank) =>
            $query->where(fn($query) =>
                $query->where('bank', 'like', '%' . $bank . '%')
            )
        );

        $query->when($filters['start'] ?? false, fn($query, $start) =>
            $query->where(fn($query) =>
                $query->where('date','>=', $start)
            )
        );

        $query->when($filters['until'] ?? false, fn($query, $end) =>
            $query->where(fn($query) =>
                $query->where('date','<=', $end)
            )
        );

        $query->when($filters['expenses'] ?? false, fn($query, $expenses) =>
            $query->where(fn($query) =>
                $query->where('amount','<', 0)
            )
        );

        $query->when($filters['incomes'] ?? false, fn($query, $incomes) =>
            $query->where(fn($query) =>
                $query->where('amount','>', 0)
            )
        );
    }

    /**
     * Get the post that owns the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
