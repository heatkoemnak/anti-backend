<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'owner_name',
        'location',
        'contact_number',
        'price',
    ];
    public $timestamps = false; // Disable automatic timestamps

}
