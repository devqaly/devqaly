<?php

namespace App\Models\Session;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Event extends Model
{
    use HasFactory, HasUuids;

    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $fillable = [
        'event_id',
        'event_type',
        'session_id',
        'source',
        'client_utc_event_created_at',
    ];

    protected $casts = [
        'client_utc_event_created_at' => 'datetime'
    ];

    public function eventable(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'event_type', 'event_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id');
    }
}
