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
        Schema::create('setting_kunjungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->string('jabatan');
            $table->date('tanggal');
            $table->time('jam_aktif');
            $table->time('jam_non_aktif');
            $table->string('status', 1)->default('Y');
            $table->timestamps();
            $table->unique(['karyawan_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_kunjungan');
    }
};
