<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWastesTable extends Migration
{
    public function up()
    {
        Schema::create('wastes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('owner');
            $table->decimal('price', 10, 2); // Adjust precision and scale as needed
            $table->string('categories');
            $table->string('contact_number');
            $table->string('location');
            $table->string('item_amount');
            $table->text('description');
            $table->string('photo_path')->nullable(); // If storing image paths
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wastes');
    }
}
