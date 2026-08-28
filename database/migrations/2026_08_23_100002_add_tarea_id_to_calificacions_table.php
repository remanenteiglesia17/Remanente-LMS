<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 'tarea_id': enlace directo y confiable a la actividad calificada
     * (antes solo se enlazaba por texto libre 'concepto' == titulo_tarea,
     * lo que se rompía si el título cambiaba). Necesario también para
     * poder ubicar a qué módulo pertenece cada calificación.
     */
    public function up(): void
    {
        Schema::table('calificacions', function (Blueprint $table) {
            $table->foreignId('tarea_id')->nullable()->after('entrega_id')
                ->constrained('tareas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('calificacions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tarea_id');
        });
    }
};
