<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EventImage;

class Event extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'date', 'location'];

    public function images()
    {
        return $this->hasMany(EventImage::class);
    }
}
