<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;
class Product extends Model
{
    use HasFactory;
    // protected $fillable = ['name', 'description', 'price','location',"image",'contact_number', 'categories', 'user_id'];
    protected $fillable = [
        'name', 'location', 'contact_number', 'description', 'price', 'categories', 'img',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
