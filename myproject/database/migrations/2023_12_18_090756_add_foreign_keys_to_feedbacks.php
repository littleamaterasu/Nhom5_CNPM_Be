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
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->foreign(['game_id'], 'feedbacks_games_id_fk')->references(['id'])->on('games');
            $table->foreign(['user_id'], 'feedbacks_users_id_fk')->references(['id'])->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropForeign('feedbacks_games_id_fk');
            $table->dropForeign('feedbacks_users_id_fk');
        });
    }
};
