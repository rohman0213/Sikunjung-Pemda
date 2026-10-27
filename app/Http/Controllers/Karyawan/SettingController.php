<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\SettingKunjungan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Menampilkan pengaturan jadwal milik karyawan yang login.
     */
    public function index(Request $request): View
    {
        $karyawan = $request->user()->profilKaryawan();

        abort_if(
            $karyawan === null,
            403,
            'Profil karyawan tidak ditemukan.'
        );

        $settings = SettingKunjungan::query()
            ->lengkap()
            ->where('setting_kunjungan.karyawan_id', $karyawan->id)
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where(
                    'setting_kunjungan.status',
                    $request->input('status')
                );
            })
            ->when($request->filled('tanggal'), function ($query) use ($request) {
                $query->whereDate(
                    'setting_kunjungan.tanggal',
                    $request->input('tanggal')
                );
            })
            ->orderByDesc('setting_kunjungan.tanggal')
            ->paginate(10)
            ->withQueryString();

        return view(
            'karyawan.setting.index',
            compact('karyawan', 'settings')
        );
    }
}
