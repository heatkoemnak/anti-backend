<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waste extends Model
{
    protected $fillable = [
        'name', 'owner', 'price', 'categories', 'contact_number',
        'location', 'item_amount', 'description', 'photo',
    ];

    // Your other model relationships or methods
}

