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
        Schema::create('statistic_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recommendation_id')->constrained(); // Menghubungkan ke struktur tabel
            $table->foreignId('user_id')->constrained(); // OPD pemilik data
            $table->integer('tahun');
            $table->json('isi_data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistic_data');
    }
};
