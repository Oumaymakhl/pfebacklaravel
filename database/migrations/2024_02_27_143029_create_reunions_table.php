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
        Schema::create('reunions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_admin')->default(0); // Ajout de la colonne de clé étrangère
            $table->foreign('id_admin')->references('id')->on('admins')->onDelete('cascade'); // Définition de la clé étrangère
            $table->string('titre');
            $table->text('description')->nullable();
            $table->dateTime('date');
            $table->boolean('statut')->default(false);
            $table->timestamps();
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reunions');
    }
};
