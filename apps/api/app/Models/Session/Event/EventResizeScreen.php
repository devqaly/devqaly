<?php

namespace App\Models\Session\Event;

use App\Models\Session\Event;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class EventResizeScreen extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'inner_height',
        'inner_width',
    ];

    public function event(): MorphOne
    {
        return $this->morphOne(Event::class, 'event');
    }
}
