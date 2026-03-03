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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('perangkat_daerah_id')
                ->after('id') // Menempatkan kolom setelah kolom 'id' agar rapi
                ->nullable()
                ->constrained('perangkat_daerah') // Otomatis mencari tabel 'perangkat_daerah'
                ->onDelete('cascade'); // Jika data OPD dihapus, user terkait juga terhapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['perangkat_daerah_id']);
            $table->dropColumn('perangkat_daerah_id');
        });
    }
};
