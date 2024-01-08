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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign(['game_id'], 'transactions_games_id_fk')->references(['id'])->on('games');
            $table->foreign(['user_id'], 'transactions_users_id_fk')->references(['id'])->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('transactions_games_id_fk');
            $table->dropForeign('transactions_users_id_fk');
        });
    }
};
