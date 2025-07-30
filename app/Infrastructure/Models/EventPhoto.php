<?php

namespace App\Infrastructure\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EventPhoto extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'event_id',
        'uploaded_by',
        'path'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFullUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }

    public function scopeForEvent($query, string $eventId): mixed
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeByUser($query, string $userId): mixed
    {
        return $query->where('uploaded_by', $userId);
    }

    public function scopeRecent($query): mixed
    {
        return $query->orderBy('created_at', 'desc');
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
