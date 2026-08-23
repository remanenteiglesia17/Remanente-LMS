<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 'porcentaje' quedó como una copia exacta de 'nota_maxima' (que a su
     * vez viene del 'puntaje' de la tarea/quiz/examen). Es una columna
     * redundante: se elimina y todo el código pasa a usar 'nota_maxima'
     * directamente.
     */
    public function up(): void
    {
        Schema::table('calificacions', function (Blueprint $table) {
            $table->dropColumn('porcentaje');
        });
    }

    public function down(): void
    {
        Schema::table('calificacions', function (Blueprint $table) {
            $table->decimal('porcentaje', 5, 2)->default(5.00)->after('nota_maxima');
        });
    }
};
