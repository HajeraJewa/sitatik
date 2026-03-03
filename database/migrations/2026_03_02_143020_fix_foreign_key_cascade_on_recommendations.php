<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recommendations', function (Blueprint $table) {
            // Hapus foreign key yang lama tanpa cascade
            $table->dropForeign(['user_id']);

            // Tambahkan kembali foreign key dengan cascade
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });

        Schema::table('statistic_data', function (Blueprint $table) {
            // Hapus foreign key yang lama
            $table->dropForeign(['recommendation_id']);

            // Tambahkan kembali dengan cascade agar jika struktur dihapus, data isinya ikut hapus
            $table->foreign('recommendation_id')
                ->references('id')->on('recommendations')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Logika untuk mengembalikan ke semula jika diperlukan
    }
};