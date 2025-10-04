<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    public function up()
    {
        // Actualizar usuarios existentes con nuevos IDs
        User::all()->each(function ($user) {
            if (empty($user->user_id)) { // <-- Aquí faltaba el paréntesis de cierre
                $user->update(['user_id' => User::generateBankUserId()]);
            }
        });
    }

    public function down()
    {
        // No se puede revertir fácilmente
    }
};