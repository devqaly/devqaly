<?php

namespace App\Models\Session\Event;

use App\Models\Session\Event;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

class EventNetworkRequest extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'method',
        'url',
        'request_headers',
        'request_body',
        'response_headers',
        'response_body',
        'response_status',
        'request_id',
    ];

    public function event(): MorphOne
    {
        return $this->morphOne(Event::class, 'event');
    }

    protected static function booted(): void
    {
        static::saving(function (EventNetworkRequest $event) {
            $event->request_id = $event->request_id ?? Str::uuid();
        });
    }
}
