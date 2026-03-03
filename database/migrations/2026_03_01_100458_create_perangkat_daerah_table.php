<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perangkat_daerah', function (Blueprint $table) {
            $table->id();
            $table->string('kode_opd')->unique(); // Contoh: 1.02.01
            $table->string('nama_opd');           // Contoh: Dinas Pertanian
            $table->string('alias_opd')->nullable(); // Contoh: DISTAN
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perangkat_daerah');
    }
};
