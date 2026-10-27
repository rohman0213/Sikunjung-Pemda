<?php
namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Kunjungan;
use App\Models\SettingKunjungan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman utama beserta statistik dan jadwal.
     */
    public function index(): View
    {
        $statistik = [
            'karyawan'            => Karyawan::count(),

            'jadwal_aktif'        => SettingKunjungan::aktif()->count(),

            'kunjungan_disetujui' => Kunjungan::where(
                'status',
                'disetujui'
            )->count(),
        ];

        $jadwals = SettingKunjungan::lengkap()
            ->aktif()
            ->orderBy('setting_kunjungan.tanggal')
            ->orderBy('setting_kunjungan.jam_aktif')
            ->limit(5)
            ->get();

        return view('home', compact('statistik', 'jadwals'));
    }

    /**
     * Menampilkan jadwal kunjungan.
     */

    public function jadwalKunjungan(Request $request): View
    {
        $jadwals = SettingKunjungan::query()
            ->lengkap()
            ->aktif()
            ->when($request->filled('q'), function ($query) use ($request) {
                $kataKunci = '%' . $request->input('q') . '%';

                $query->where(function ($query) use ($kataKunci) {
                    $query->where('karyawan.nama', 'like', $kataKunci)
                        ->orWhere('setting_kunjungan.jabatan', 'like', $kataKunci);
                });
            })
            ->when($request->filled('tanggal'), function ($query) use ($request) {
                $query->whereDate(
                    'setting_kunjungan.tanggal',
                    $request->input('tanggal')
                );
            })
            ->orderBy('setting_kunjungan.tanggal')
            ->orderBy('karyawan.nama')
            ->paginate(12)
            ->withQueryString();

        return view('jadwal-kunjungan', compact('jadwals'));
    }

    /**
     * Menampilkan struktur karyawan.
     */

    public function strukturKaryawan(): View
    {
        $karyawans = Karyawan::query()
            ->orderBy('jabatan')
            ->orderBy('nama')
            ->get();

        return view('struktur-karyawan', compact('karyawans'));
    }

}
