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
        Schema::create('latest_news', function (Blueprint $table) {
            $table->id();
            $table->date('latest_news_date');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('category', 10);
            
            $table->foreignId('zakat_id')->constrained()->onDelete('cascade');
            $table->foreignId('infak_id')->constrained()->onDelete('cascade');
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('wakaf_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('latest_news');
    }
};
