<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
            $table->foreignId('modulo_id')->nullable()->constrained('modulos')->onDelete('cascade');

            // Tipo de actividad y disponibilidad
            $table->enum('tipo', ['tarea', 'quiz', 'examen', 'proyecto', 'foro'])->default('tarea');
            $table->dateTime('fecha_apertura')->nullable();
            $table->date('fecha_entrega')->nullable();
            
            // Información general
            $table->string('titulo_tarea');
            $table->text('descripcion_tarea')->nullable();
            $table->text('requisitos')->nullable();
            $table->text('criterios_evaluacion')->nullable();

            // Configuración de entrega
            $table->boolean('permite_entregas_tardias')->default(false);
            $table->decimal('penalizacion_tardia', 5, 2)->default(0); // Porcentaje de penalización
            $table->boolean('visible')->default(true);
            $table->integer('intentos_permitidos')->default(1);
            $table->enum('formato_entrega', ['archivo', 'enlace', 'texto', 'ambos'])->default('archivo');
            $table->string('formatos_aceptados')->nullable(); // Ej: .zip,.pdf,.docx
            $table->integer('tamanio_maximo')->default(50); // En Megabytes (MB)

            // Evaluación y Calificación
            $table->decimal('puntaje', 3, 2)->default(5.00); // Nota máxima (ej. 5.00)
            $table->decimal('peso', 5, 2)->default(0.00);    // Porcentaje/Ponderación en el curso (ej. 10.00%)

            // Índices para optimización de consultas
            $table->index('visible');
            $table->index(['curso_id', 'fecha_entrega']);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};