<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id(); // JD
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('cc', 20)->unique();
            $table->string('genero', 10);
            $table->string('telefono', 20);
            $table->string('direccion', 150);
            $table->string('contacto_emergencia', 10)->nullable();
            $table->string('observaciones', 255)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
