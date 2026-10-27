<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemohon_id')->constrained('pemohon')->cascadeOnDelete();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam');
            $table->string('keperluan');
            $table->string('status', 10)->default('menunggu');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->unique(['pemohon_id', 'karyawan_id', 'tanggal', 'jam']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kunjungan');
    }
};
