<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->string('invoice_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('donatur', 40)->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('email')->nullable();
            $table->double('transaction_amount')->nullable();
            $table->text('message')->nullable();
            $table->dateTime('transaction_date')->nullable(false);
            $table->string('channel')->nullable();
            $table->string('va_number')->nullable();
            $table->string('method')->nullable();
            $table->unsignedBigInteger('transaction_qr_id')->nullable();
            $table->string('created_time')->nullable();
            $table->boolean('success')->default(false);
            $table->string('category', 20)->nullable();
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->onDelete('set null');
            $table->foreignId('zakat_id')->nullable()->constrained('zakats')->onDelete('set null');
            $table->foreignId('infak_id')->nullable()->constrained('infaks')->onDelete('set null');
            $table->foreignId('wakaf_id')->nullable()->constrained('wakafs')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
