<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_id')->unique()->nullable()->after('id');
        });

        // Generar user_id para usuarios existentes
        \App\Models\User::all()->each(function ($user) {
            if (empty($user->user_id)) {
                $user->update(['user_id' => \App\Models\User::generateUniqueUserId()]);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('user_id')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};