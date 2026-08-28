<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La ponderación por categoría (tarea/quiz/examen/proyecto/foro) se
     * define por MÓDULO (no por curso ni por tarea individual). Cada
     * módulo también obtiene su propio rango de fechas, ya que representa
     * una etapa concreta del curso con inicio y fin definidos.
     */
    public function up(): void
    {
        Schema::table('modulos', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable()->after('descripcion');
            $table->date('fecha_fin')->nullable()->after('fecha_inicio');
            $table->decimal('peso_tarea', 5, 2)->default(20.00)->after('fecha_fin');
            $table->decimal('peso_quiz', 5, 2)->default(20.00)->after('peso_tarea');
            $table->decimal('peso_examen', 5, 2)->default(30.00)->after('peso_quiz');
            $table->decimal('peso_proyecto', 5, 2)->default(20.00)->after('peso_examen');
            $table->decimal('peso_foro', 5, 2)->default(10.00)->after('peso_proyecto');
        });
    }

    public function down(): void
    {
        Schema::table('modulos', function (Blueprint $table) {
            $table->dropColumn(['fecha_inicio', 'fecha_fin', 'peso_tarea', 'peso_quiz', 'peso_examen', 'peso_proyecto', 'peso_foro']);
        });
    }
};
