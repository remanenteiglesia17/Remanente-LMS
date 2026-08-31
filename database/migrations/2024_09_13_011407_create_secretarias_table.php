<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('secretarias', function (Blueprint $table) {
            $table->id(); // JD
            // nombres/apellidos viven en 'users' (no se duplican aquí)
            $table->integer('cc')->unique();
            $table->string('telefono', 100);
            $table->string('fecha_nacimiento', 100);
            $table->string('direccion', 100);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secretarias');
    }
};
