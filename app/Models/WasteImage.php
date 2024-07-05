<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Waste;
class WasteImage extends Model
{
    use HasFactory;
    protected $fillable = ['waste_id', 'image_path'];
    public function waste()
    {
        return $this->belongsTo(Waste::class);
    }
}
