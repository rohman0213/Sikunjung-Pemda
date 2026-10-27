<?php

namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoriController extends Controller
{
    /**
     * Histori seluruh kunjungan pemohon (hanya baca).
     */
    public function index(Request $request): View
    {
        $pemohon = $request->user()->profilPemohon();

        abort_if(
            $pemohon === null,
            403,
            'Profil pemohon tidak ditemukan.'
        );

        $kunjungans = Kunjungan::query()
            ->lengkap()
            ->where('kunjungan.pemohon_id', $pemohon->id)
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('kunjungan.status', $request->input('status'));
            })
            ->when($request->filled('dari'), function ($query) use ($request) {
                $query->whereDate(
                    'kunjungan.tanggal',
                    '>=',
                    $request->input('dari')
                );
            })
            ->when($request->filled('sampai'), function ($query) use ($request) {
                $query->whereDate(
                    'kunjungan.tanggal',
                    '<=',
                    $request->input('sampai')
                );
            })
            ->urut()
            ->paginate(10)
            ->withQueryString();

        return view(
            'pemohon.histori.index',
            compact('pemohon', 'kunjungans')
        );
    }
}