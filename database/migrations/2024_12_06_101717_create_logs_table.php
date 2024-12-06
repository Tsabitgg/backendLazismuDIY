<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('feature')->nullable(); // Nama fitur atau fungsi
            $table->json('request_data')->nullable(); // Data request
            $table->json('response_data')->nullable(); // Data response
            $table->text('error_message')->nullable(); // Pesan error
            $table->timestamps(); // created_at dan updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('logs');
    }
};
