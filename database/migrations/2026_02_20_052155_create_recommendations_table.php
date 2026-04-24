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
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('table_name');
            $table->text('table_structure'); // Struktur kolom yang diusulkan operator
            $table->string('category');
            $table->text('description')->nullable(); // Catatan opsional dari operator

            // Dibuat nullable karena Admin yang akan menentukan jadwal saat 'Setuju'
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Penambahan status 'corrected'
            $table->enum('status', ['pending', 'approved', 'rejected', 'corrected'])->default('pending');
            $table->text('admin_note')->nullable(); // Pesan dari admin (opsional/koreksi)
            $table->timestamps();
            // Di dalam file migration tabel recommendations
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            // ATAU jika kamu menggunakan nama manual:
// $table->unsignedBigInteger('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }

};
