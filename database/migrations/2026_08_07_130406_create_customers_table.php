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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Customer belongs to a client
            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            $table->string('customer_id')->nullable()->unique();
            $table->string('name');
            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->string('category')->nullable();
            $table->string('status')->default('Active');

            $table->date('joining_date')->nullable();

            $table->text('address')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};