<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Kunjungan;
use App\Models\SettingKunjungan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Ringkasan layanan kunjungan untuk admin.
     */
    public function index(): View
    {
        $statistik = [
            'karyawan' => Karyawan::count(),
            'kunjungan_hari_ini' => Kunjungan::whereDate('tanggal', now()->toDateString())->count(),
            'menunggu' => Kunjungan::where('status', 'menunggu')->count(),
            'layanan_hari_ini' => SettingKunjungan::whereDate('tanggal', now()->toDateString())
                ->where('status', SettingKunjungan::STATUS_AKTIF)
                ->count(),
        ];
        $menunggu = Kunjungan::query()
            ->lengkap()
            ->where('kunjungan.status', 'menunggu')
            ->orderBy('kunjungan.tanggal')
            ->orderBy('kunjungan.jam')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('statistik', 'menunggu'));
    }
}
