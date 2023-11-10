<?php

namespace App\Models\Session\Event;

use App\Models\Session\Event;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class EventCustomEvent extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'payload',
        'name',
    ];

    protected $casts = [
        'payload' => 'array'
    ];

    public function event(): MorphOne
    {
        return $this->morphOne(Event::class, 'event');
    }
}
