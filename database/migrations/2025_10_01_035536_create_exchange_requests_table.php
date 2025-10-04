<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exchange_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
            $table->string('from_currency', 3);
            $table->string('to_currency', 3);
            $table->decimal('from_amount', 10, 2);
            $table->decimal('to_amount', 10, 2);
            $table->decimal('exchange_rate', 8, 4);
            $table->enum('status', ['pending', 'approved', 'completed', 'rejected', 'cancelled'])->default('pending');
            $table->string('transaction_number')->unique();
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Índices para mejor performance
            $table->index(['from_user_id', 'status']);
            $table->index(['to_user_id', 'status']);
            $table->index('transaction_number');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('exchange_requests');
    }
};