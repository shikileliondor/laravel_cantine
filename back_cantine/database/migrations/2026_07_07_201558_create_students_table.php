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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('classe')->index();
            $table->date('date_naissance')->nullable();
            $table->string('nom_tuteur');
            $table->string('telephone_tuteur');
            $table->string('adresse')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();

            $table->index(['nom', 'prenom']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
