<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name');
            $table->text('description'); // Changed to text for longer descriptions
            $table->string('location');
            $table->dateTime('date'); // Changed to dateTime based on usage
            $table->timestamps();
        });

        // Consider adding a separate table for event images if needed
        // Example:
        // Schema::create('event_images', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('event_id')->constrained()->onDelete('cascade');
        //     $table->string('url');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the event images table if you added one
        // Example:
        // Schema::dropIfExists('event_images');

        Schema::dropIfExists('events');
    }
};
