<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Waste extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'owner', 'price', 'categories', 'contact_number',
        'location', 'item_amount', 'description', 'photo_path',
    ];
}
