<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_category_id')->constrained('campaign_categories'); // Foreign key untuk campaign_categories
            $table->string('campaign_name');
            $table->string('campaign_code')->unique();
            $table->string('campaign_thumbnail');
            $table->string('campaign_image_1');
            $table->string('campaign_image_2');
            $table->string('campaign_image_3');
            $table->text('description');
            $table->string('location');
            $table->double('target_amount');
            $table->double('current_amount')->default(0);
            $table->double('distribution');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('active');
            $table->boolean('approved');
            $table->boolean('priority');
            $table->boolean('recomendation');
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
