<?php
namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
/**
 * Ringkasan kunjungan milik pemohon yang sedang login.
 */
    public function index(Request $request): View
    {
        $pemohon = $request->user()->profilPemohon();
        abort_if($pemohon === null, 403, 'Profil pemohon tidak ditemukan.');
        $statistik = [
            'total'     => Kunjungan::where('pemohon_id', $pemohon->id)->count(),
            'menunggu'  => Kunjungan::where('pemohon_id', $pemohon->id)->where('status',
                'menunggu')->count(),
            'disetujui' => Kunjungan::where('pemohon_id', $pemohon->id)->where('status',
                'disetujui')->count(),
            'ditolak'   => Kunjungan::where('pemohon_id', $pemohon->id)->where('status',
                'ditolak')->count(),
        ];
        $jadwalTerdekat = Kunjungan::query()
            ->lengkap()
            ->where('kunjungan.pemohon_id', $pemohon->id)
            ->where('kunjungan.status', 'disetujui')
            ->whereDate('kunjungan.tanggal', '>=', now()->toDateString())
            ->orderBy('kunjungan.tanggal')
            ->orderBy('kunjungan.jam')
            ->limit(5)
            ->get();
        return view('pemohon.dashboard', compact('pemohon', 'statistik', 'jadwalTerdekat'));
    }
}
