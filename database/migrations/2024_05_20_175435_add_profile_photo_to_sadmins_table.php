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
        Schema::table('sadmins', function (Blueprint $table) {
            $table->string('profile_photo')->nullable()->after('email'); // ajoutez le champ après le champ 'email'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sadmins', function (Blueprint $table) {
            $table->dropColumn('profile_photo');

        });
    }
};
