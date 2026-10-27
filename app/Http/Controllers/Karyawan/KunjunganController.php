<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Kunjungan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KunjunganController extends Controller
{
    /**
     * Mengambil daftar pengajuan kunjungan milik karyawan login.
     */
    public function index(Request $request): View
    {
        $karyawan = $this->karyawan($request);

        $kunjungans = Kunjungan::query()
            ->lengkap()
            ->where('kunjungan.karyawan_id', $karyawan->id)
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where(
                    'kunjungan.status',
                    $request->input('status')
                );
            })
            ->when($request->filled('tanggal'), function ($query) use ($request) {
                $query->whereDate(
                    'kunjungan.tanggal',
                    $request->input('tanggal')
                );
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $kataKunci = '%' . $request->input('q') . '%';

                $query->where(function ($query) use ($kataKunci) {
                    $query->where('pemohon.nama', 'like', $kataKunci)
                        ->orWhere(
                            'kunjungan.keperluan',
                            'like',
                            $kataKunci
                        );
                });
            })
            ->urut()
            ->paginate(10)
            ->withQueryString();

        return view(
            'karyawan.kunjungan.index',
            compact('karyawan', 'kunjungans')
        );
    }

    /**
     * Menampilkan form pemrosesan pengajuan.
     */
    public function edit(
        Request $request,
        Kunjungan $kunjungan
    ): View {
        $karyawan = $this->karyawan($request);

        $this->pastikanMilikKaryawan($kunjungan, $karyawan->id);
        $this->pastikanMasihMenunggu($kunjungan);

        // Ambil kembali data beserta informasi pemohon.
        $kunjungan = Kunjungan::query()
            ->lengkap()
            ->where('kunjungan.id', $kunjungan->id)
            ->firstOrFail();

        return view(
            'karyawan.kunjungan.form',
            compact('karyawan', 'kunjungan')
        );
    }

    /**
     * Menyimpan keputusan persetujuan atau penolakan.
     */
    public function update(
        Request $request,
        Kunjungan $kunjungan
    ): RedirectResponse {
        $karyawan = $this->karyawan($request);

        $this->pastikanMilikKaryawan($kunjungan, $karyawan->id);
        $this->pastikanMasihMenunggu($kunjungan);

        $data = $request->validate([
            'status' => [
                'required',
                Rule::in(['disetujui', 'ditolak']),
            ],
            'catatan' => [
                'nullable',
                'string',
                'max:255',
                'required_if:status,ditolak',
            ],
        ]);

        $kunjungan->update([
            'status' => $data['status'],
            'catatan' => $data['status'] === 'ditolak'
                ? $data['catatan']
                : null,
        ]);

        return redirect()
            ->route('karyawan.kunjungan.index')
            ->with(
                'success',
                'Status pengajuan kunjungan berhasil diperbarui.'
            );
    }

    /**
     * Mendapatkan profil karyawan dari akun yang sedang login.
     */
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

    /**
     * Memastikan pengajuan memang milik karyawan login.
     */
    private function pastikanMilikKaryawan(
        Kunjungan $kunjungan,
        int $karyawanId
    ): void {
        abort_if(
            (int) $kunjungan->karyawan_id !== $karyawanId,
            403,
            'Anda hanya bisa memproses kunjungan untuk diri Anda sendiri.'
        );
    }

    /**
     * Mencegah pengajuan yang sudah diproses diproses ulang.
     */
    private function pastikanMasihMenunggu(
        Kunjungan $kunjungan
    ): void {
        abort_if(
            ! $kunjungan->masihMenunggu(),
            403,
            'Pengajuan ini sudah diproses.'
        );
    }
}
