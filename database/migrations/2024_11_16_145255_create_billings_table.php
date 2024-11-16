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
        Schema::create('billings', function (Blueprint $table) {
            $table->id('billing_id');
            $table->string('created_time', 8);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('username', 20)->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->double('billing_amount');
            $table->string('message')->nullable();
            $table->dateTime('billing_date')->nullable(); // Nilai otomatis diatur di model
            $table->bigInteger('va_number')->nullable();
            $table->string('method')->nullable();
            $table->bigInteger('transaction_qr_id')->nullable();
            $table->boolean('success')->default(false);
            $table->string('category', 20)->nullable();
            $table->foreignId('zakat_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('infak_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('campaign_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('wakaf_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps(); // Otomatis menangani created_at dan updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('billings');
    }
};
