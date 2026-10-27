<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Kunjungan;
use App\Models\SettingKunjungan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Ringkasan kunjungan untuk karyawan yang sedang login.
     */
    public function index(Request $request): View
    {
        $karyawan = $request->user()->profilKaryawan();

        abort_if(
            $karyawan === null,
            403,
            'Profil karyawan tidak ditemukan.'
        );

        $statistik = [
            'menunggu' => Kunjungan::where(
                'karyawan_id',
                $karyawan->id
            )->where('status', 'menunggu')->count(),

            'hari_ini' => Kunjungan::where(
                'karyawan_id',
                $karyawan->id
            )
                ->whereDate('tanggal', now()->toDateString())
                ->where('status', 'disetujui')
                ->count(),

            'disetujui' => Kunjungan::where(
                'karyawan_id',
                $karyawan->id
            )->where('status', 'disetujui')->count(),

            'ditolak' => Kunjungan::where(
                'karyawan_id',
                $karyawan->id
            )->where('status', 'ditolak')->count(),
        ];

        $menunggu = Kunjungan::query()
            ->lengkap()
            ->where('kunjungan.karyawan_id', $karyawan->id)
            ->where('kunjungan.status', 'menunggu')
            ->orderBy('kunjungan.tanggal')
            ->orderBy('kunjungan.jam')
            ->limit(5)
            ->get();

        $settingHariIni = SettingKunjungan::query()
            ->where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        $kegiatanAktif = Kegiatan::query()
            ->where('karyawan_id', $karyawan->id)
            ->whereDate(
                'tanggal_awal',
                '<=',
                now()->toDateString()
            )
            ->whereDate(
                'tanggal_akhir',
                '>=',
                now()->toDateString()
            )
            ->orderBy('tanggal_awal')
            ->get();

        return view('karyawan.dashboard', compact(
            'karyawan',
            'statistik',
            'menunggu',
            'settingHariIni',
            'kegiatanAktif'
        ));
    }
}
