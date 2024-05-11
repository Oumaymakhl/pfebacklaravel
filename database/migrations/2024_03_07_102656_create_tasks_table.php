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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
           
            $table->enum('status', ['todo', 'completed'])->default('todo');
            $table->integer('estimated_time')->nullable();
            $table->integer('time_spent')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('comment')->nullable(); // Ajout de la colonne 'comment
            $table->timestamps();

            // Clé étrangère liant la colonne user_id à la table users
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
