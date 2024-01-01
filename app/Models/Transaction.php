<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    use HasFactory;
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'amount',
        'deskripsi',
        'receipt',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
    public function debts(): BelongsTo
    {
        return $this->belongsTo(Debt::class);
    }


    public function purposable(): MorphTo
    {
        return $this->morphTo();
    }
}
