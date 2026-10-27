<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

#[Fillable([
    'karyawan_id',
    'jabatan',
    'tanggal_awal',
    'tanggal_akhir',
    'deskripsi_kegiatan',
])]
class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan';

    /**
     * Tanggal awal kegiatan: disimpan sebagai Y-m-d, dibaca sebagai Carbon.
     *
     * @return Attribute<Carbon, string>
     */
    protected function tanggalAwal(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value === null ? null : Carbon::parse($value),
            set: fn ($value) => $value === null ? null : Carbon::parse($value)->toDateString(),
        );
    }

    /**
     * Tanggal akhir kegiatan: disimpan sebagai Y-m-d, dibaca sebagai Carbon.
     *
     * @return Attribute<Carbon, string>
     */
    protected function tanggalAkhir(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value === null ? null : Carbon::parse($value),
            set: fn ($value) => $value === null ? null : Carbon::parse($value)->toDateString(),
        );
    }

    /**
     * Join karyawan, lengkap dengan kolom alias karyawan_nama.
     *
     * @param  Builder<Kegiatan>  $query
     * @return Builder<Kegiatan>
     */
    public function scopeLengkap(Builder $query): Builder
    {
        return $query
            ->join('karyawan', 'karyawan.id', '=', 'kegiatan.karyawan_id')
            ->select(
                'kegiatan.*',
                'karyawan.nama as karyawan_nama',
            );
    }

    /**
     * Urutkan kegiatan dari tanggal awal terbaru.
     *
     * @param  Builder<Kegiatan>  $query
     * @return Builder<Kegiatan>
     */
    public function scopeUrut(Builder $query): Builder
    {
        return $query
            ->orderByDesc('kegiatan.tanggal_awal')
            ->orderByDesc('kegiatan.id');
    }

    /**
     * Rentang tanggal kegiatan, contoh 12 Okt 2026 - 15 Okt 2026.
     */
    public function rentangTanggal(): string
    {
        $awal = $this->tanggal_awal?->translatedFormat('d M Y');
        $akhir = $this->tanggal_akhir?->translatedFormat('d M Y');

        return $awal === $akhir ? (string) $awal : $awal.' - '.$akhir;
    }
}
