<?php

namespace App\Infrastructure\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'organizer_id',
        'title',
        'description',
        'address',
        'start_time',
        'end_time',
        'min_participants',
        'max_participants',
        'price',
        'currency',
        'is_free',
        'images',
        'status'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'price' => 'float',
        'is_free' => 'boolean',
        'images' => 'array',
        'min_participants' => 'integer',
        'max_participants' => 'integer'
    ];

    protected static function newFactory()
    {
        return EventFactory::new();
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
