<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Waste extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'category', 'seller', 'location', 'contact', 'amount', 'price', 'img', 'description'
    ];
}
