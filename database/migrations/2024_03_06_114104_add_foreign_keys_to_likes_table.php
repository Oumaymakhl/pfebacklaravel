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
        Schema::table('likes', function (Blueprint $table) {
            // Ajouter la colonne user_id
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Ajouter la colonne decision_id
            $table->foreignId('decision_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('likes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['decision_id']);
            $table->dropColumn('user_id');
            $table->dropColumn('decision_id');
        });
    }
};
