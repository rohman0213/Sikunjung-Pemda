<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Kegiatan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KegiatanController extends Controller
{
    public function index(Request $request): View
    {
        $karyawan = $this->karyawan($request);

        $kegiatans = Kegiatan::query()
            ->lengkap()
            ->where('kegiatan.karyawan_id', $karyawan->id)
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where(
                    'kegiatan.deskripsi_kegiatan',
                    'like',
                    '%' . $request->input('q') . '%'
                );
            })
            ->urut()
            ->paginate(10)
            ->withQueryString();

        return view(
            'karyawan.kegiatan.index',
            compact('karyawan', 'kegiatans')
        );
    }

    public function create(Request $request): View
    {
        $karyawan = $this->karyawan($request);

        return view('karyawan.kegiatan.form', [
            'karyawan' => $karyawan,
            'kegiatan' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $karyawan = $this->karyawan($request);
        $data = $this->validasi($request);

        Kegiatan::create([
            'karyawan_id' => $karyawan->id,
            'jabatan' => $karyawan->jabatan,
            'tanggal_awal' => $data['tanggal_awal'],
            'tanggal_akhir' => $data['tanggal_akhir'],
            'deskripsi_kegiatan' => $data['deskripsi_kegiatan'],
        ]);

        return redirect()
            ->route('karyawan.kegiatan.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function edit(
        Request $request,
        Kegiatan $kegiatan
    ): View {
        $karyawan = $this->karyawan($request);

        $this->pastikanMilikKaryawan(
            $kegiatan,
            $karyawan->id
        );

        return view(
            'karyawan.kegiatan.form',
            compact('karyawan', 'kegiatan')
        );
    }

    public function update(
        Request $request,
        Kegiatan $kegiatan
    ): RedirectResponse {
        $karyawan = $this->karyawan($request);

        $this->pastikanMilikKaryawan(
            $kegiatan,
            $karyawan->id
        );

        $kegiatan->update($this->validasi($request));

        return redirect()
            ->route('karyawan.kegiatan.index')
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(
        Request $request,
        Kegiatan $kegiatan
    ): RedirectResponse {
        $karyawan = $this->karyawan($request);

        $this->pastikanMilikKaryawan(
            $kegiatan,
            $karyawan->id
        );

        $kegiatan->delete();

        return redirect()
            ->route('karyawan.kegiatan.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    private function karyawan(Request $request): Karyawan
    {
        $karyawan = $request->user()->profilKaryawan();

        abort_if(
            $karyawan === null,
            403,
            'Profil karyawan tidak ditemukan.'
        );

        return $karyawan;
    }

    private function pastikanMilikKaryawan(
        Kegiatan $kegiatan,
        int $karyawanId
    ): void {
        abort_if(
            (int) $kegiatan->karyawan_id !== $karyawanId,
            403,
            'Anda hanya bisa mengelola kegiatan milik Anda sendiri.'
        );
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'tanggal_awal' => [
                'required',
                'date',
            ],
            'tanggal_akhir' => [
                'required',
                'date',
                'after_or_equal:tanggal_awal',
            ],
            'deskripsi_kegiatan' => [
                'required',
                'string',
            ],
        ]);
    }
}
