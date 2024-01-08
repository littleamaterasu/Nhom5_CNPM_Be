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
        Schema::table('games', function (Blueprint $table) {
            $table->foreign(['genre_id'], 'games_genres_id_fk')->references(['id'])->on('game_genres');
            $table->foreign(['user_id'], 'games_users_id_fk')->references(['id'])->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropForeign('games_genres_id_fk');
            $table->dropForeign('games_users_id_fk');
        });
    }
};
