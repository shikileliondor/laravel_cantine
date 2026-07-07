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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date')->index();
            $table->time('heure_pointage')->nullable();
            $table->string('type_repas')->default('dejeuner');
            $table->boolean('present')->default(true);
            $table->text('observation')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'date', 'type_repas']);
            $table->index(['date', 'present']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
