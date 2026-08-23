<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * El puntaje máximo de una tarea/quiz/examen ahora debe estar en la
     * misma escala de calificación del curso (0.0 - 5.0), no en 0-100.
     * Se usa SQL crudo porque Schema::table(...)->change() requiere el
     * paquete doctrine/dbal, que este proyecto no tiene instalado.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE tareas MODIFY puntaje DECIMAL(5,2) NOT NULL DEFAULT 5.00');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE tareas MODIFY puntaje DECIMAL(5,2) NOT NULL DEFAULT 100.00');
    }
};
