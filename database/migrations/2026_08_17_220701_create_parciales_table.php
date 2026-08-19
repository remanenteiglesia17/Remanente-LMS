<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parciales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');

            $table->string('nombre', 150);      // ej: "Primer Parcial"
            $table->unsignedInteger('numero');  // ej: 1, 2, 3... orden del parcial dentro del curso

            // Rango de fechas propio del parcial (opcional, dentro del rango del curso).
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();

            // Peso del parcial dentro de la nota final del curso (%). Si se deja en
            // blanco en todos los parciales de un curso, se promedian por igual.
            $table->unsignedInteger('porcentaje')->nullable();

            $table->timestamps();

            $table->unique(['curso_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parciales');
    }
};
