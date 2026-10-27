<?php

namespace App\Models;

use Database\Factories\KunjunganFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

#[Fillable([
    'pemohon_id',
    'karyawan_id',
    'tanggal',
    'jam',
    'keperluan',
    'status',
    'catatan',
])]
class Kunjungan extends Model
{
    /** @use HasFactory<KunjunganFactory> */
    use HasFactory;

    protected $table = 'kunjungan';

    /**
     * Daftar status kunjungan yang tersedia.
     *
     * @var array<string, string>
     */
    public const STATUS = [
        'menunggu' => 'Menunggu',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
    ];

    /**
     * Tanggal kunjungan: disimpan sebagai Y-m-d, dibaca sebagai Carbon.
     *
     * @return Attribute<Carbon|null, string|null>
     */
    protected function tanggal(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value === null
                ? null
                : Carbon::parse($value),
            set: fn ($value) => $value === null
                ? null
                : Carbon::parse($value)->toDateString(),
        );
    }

    /**
     * Join pemohon dan karyawan, lengkap dengan kolom alias.
     *
     * @param  Builder<Kunjungan>  $query
     * @return Builder<Kunjungan>
     */
    public function scopeLengkap(Builder $query): Builder
    {
        return $query
            ->join(
                'pemohon',
                'pemohon.id',
                '=',
                'kunjungan.pemohon_id',
            )
            ->join(
                'karyawan',
                'karyawan.id',
                '=',
                'kunjungan.karyawan_id',
            )
            ->select(
                'kunjungan.*',
                'pemohon.nama as pemohon_nama',
                'pemohon.nik as pemohon_nik',
                'karyawan.nama as karyawan_nama',
                'karyawan.jabatan as karyawan_jabatan',
            );
    }

    /**
     * Urutkan kunjungan dari tanggal dan jam terbaru.
     *
     * @param  Builder<Kunjungan>  $query
     * @return Builder<Kunjungan>
     */
    public function scopeUrut(Builder $query): Builder
    {
        return $query
            ->orderByDesc('kunjungan.tanggal')
            ->orderByDesc('kunjungan.jam')
            ->orderByDesc('kunjungan.id');
    }

    /**
     * Apakah kunjungan masih menunggu sehingga boleh diubah pemohon.
     */
    public function masihMenunggu(): bool
    {
        return $this->status === 'menunggu';
    }

    public function statusLabel(): string
    {
        return self::STATUS[$this->status] ?? ucfirst($this->status);
    }

    public function badgeStatus(): string
    {
        return match ($this->status) {
            'disetujui' => 'success',
            'ditolak' => 'danger',
            default => 'warning',
        };
    }
}
