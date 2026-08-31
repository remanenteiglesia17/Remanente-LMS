<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profesors', function (Blueprint $table) {
            $table->id();
            // nombres/apellidos viven en 'users' (no se duplican aquí)
            $table->string('telefono');
            // $table->string('especialidad'); //JD
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profesors');
    }
};
