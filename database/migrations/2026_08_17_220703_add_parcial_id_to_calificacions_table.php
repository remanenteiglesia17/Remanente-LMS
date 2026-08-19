<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calificacions', function (Blueprint $table) {
            $table->foreignId('parcial_id')->nullable()->after('curso_id')
                ->constrained('parciales')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('calificacions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parcial_id');
        });
    }
};
