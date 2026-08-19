<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();

            // Quién realizó la acción (se conserva el nombre/rol aunque el usuario luego se elimine)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();

            // Qué pasó
            $table->string('event', 30); // created | updated | deleted | login | logout | login_failed
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->string('auditable_label')->nullable(); // etiqueta legible del registro afectado

            // Detalle del cambio
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // Contexto de la petición
            $table->string('url', 500)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            // Los registros de auditoría son inmutables: solo created_at
            $table->timestamp('created_at')->useCurrent();

            $table->index(['auditable_type', 'auditable_id']);
            $table->index('user_id');
            $table->index('event');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};
