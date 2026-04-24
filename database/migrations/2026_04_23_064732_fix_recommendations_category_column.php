<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recommendations', function (Blueprint $table) {
            // 1. Tambah kolom category_id baru (nullable dulu agar data lama tidak error)
            $table->foreignId('category_id')->after('user_id')->nullable()->constrained('categories')->onDelete('set null');

            // 2. Kita tidak hapus kolom 'category' dulu agar data lama aman
        });
    }

    public function down(): void
    {
        Schema::table('recommendations', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};