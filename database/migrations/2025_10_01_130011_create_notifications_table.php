<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // exchange_request, exchange_approved, etc.
            $table->string('title');
            $table->text('message');
            $table->string('related_type')->nullable(); // exchange, transaction, etc.
            $table->unsignedBigInteger('related_id')->nullable(); // ID del recurso relacionado
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Índices para mejor performance
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
            $table->index('type');
            $table->index(['related_type', 'related_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};