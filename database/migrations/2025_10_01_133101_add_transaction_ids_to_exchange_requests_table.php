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
    Schema::table('exchange_requests', function (Blueprint $table) {
        $table->foreignId('from_transaction_id')->nullable()->constrained('transactions');
        $table->foreignId('to_transaction_id')->nullable()->constrained('transactions');
    });
}

public function down()
{
    Schema::table('exchange_requests', function (Blueprint $table) {
        $table->dropForeign(['from_transaction_id']);
        $table->dropForeign(['to_transaction_id']);
        $table->dropColumn(['from_transaction_id', 'to_transaction_id']);
    });
}
    


};
