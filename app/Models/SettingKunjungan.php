<?php

namespace App\Models;

use Database\Factories\SettingKunjunganFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

#[Fillable(['karyawan_id', 'jabatan', 'tanggal', 'jam_aktif', 'jam_non_aktif', 'status'])]
class SettingKunjungan extends Model
{
    /**
     * @use HasFactory<SettingKunjunganFactory>
     */
    use HasFactory;

    protected $table = 'setting_kunjungan';

    public const STATUS_AKTIF = 'Y';

    public const STATUS_NONAKTIF = 'T';

    /**
     * Label status setting kunjungan.
     *
     * @var array<string, string>
     */
    public const STATUS = [
        self::STATUS_AKTIF => 'Aktif',
        self::STATUS_NONAKTIF => 'Nonaktif',
    ];

    /**
     * Tanggal layanan: disimpan sebagai Y-m-d, dibaca sebagai Carbon.
     *
     * @return Attribute<Carbon, string>
     */
    protected function tanggal(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value === null ? null : Carbon::parse($value),
            set: fn ($value) => $value === null ? null : Carbon::parse($value)->toDateString(),
        );
    }

    /**
     * Join karyawan, lengkap dengan kolom alias karyawan_nama.
     *
     * @param  Builder<SettingKunjungan>  $query
     * @return Builder<SettingKunjungan>
     */
    public function scopeLengkap(Builder $query): Builder
    {
        return $query
            ->join('karyawan', 'karyawan.id', '=', 'setting_kunjungan.karyawan_id')
            ->select(
                'setting_kunjungan.*',
                'karyawan.nama as karyawan_nama',
            );
    }

    /**
     * Hanya setting yang aktif dan tanggalnya belum lewat.
     *
     * @param  Builder<SettingKunjungan>  $query
     * @return Builder<SettingKunjungan>
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query
            ->where('setting_kunjungan.status', self::STATUS_AKTIF)
            ->whereDate('setting_kunjungan.tanggal', '>=', now()->toDateString());
    }

    /**
     * Rentang jam layanan, contoh 08:00 - 14:00.
     */
    public function jamRentang(): string
    {
        return substr($this->jam_aktif, 0, 5).' - '.substr($this->jam_non_aktif, 0, 5);
    }

    public function statusLabel(): string
    {
        return self::STATUS[$this->status] ?? ucfirst($this->status);
    }

    public function badgeStatus(): string
    {
        return $this->status === self::STATUS_AKTIF ? 'success' : 'secondary';
    }
}
