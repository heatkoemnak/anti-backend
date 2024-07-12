<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event;

class EventImage extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'image_path'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
