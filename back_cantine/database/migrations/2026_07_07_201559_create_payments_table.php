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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('montant', 10, 2);
            $table->date('date_paiement')->index();
            $table->date('periode_debut')->nullable();
            $table->date('periode_fin')->nullable();
            $table->string('mode_paiement')->default('especes');
            $table->string('reference')->nullable();
            $table->text('observation')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'date_paiement']);
            $table->index(['periode_debut', 'periode_fin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
