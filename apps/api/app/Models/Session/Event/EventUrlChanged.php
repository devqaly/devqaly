<?php

namespace App\Models\Session\Event;

use App\Models\Session\Event;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class EventUrlChanged extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'event_url_changes';

    public $timestamps = false;

    protected $fillable = [
        'to_url'
    ];

    public function event(): MorphOne
    {
        return $this->morphOne(Event::class, 'event');
    }
}
