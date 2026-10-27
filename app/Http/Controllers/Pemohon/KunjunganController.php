<?php

namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Kunjungan;
use App\Models\Pemohon;
use App\Models\SettingKunjungan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KunjunganController extends Controller
{
    /**
     * Daftar pengajuan kunjungan milik pemohon.
     */
    public function index(Request $request): View
    {
        $pemohon = $this->pemohon($request);

        $kunjungans = Kunjungan::query()
            ->lengkap()
            ->where('kunjungan.pemohon_id', $pemohon->id)
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('kunjungan.status', $request->input('status'));
            })
            ->urut()
            ->paginate(10)
            ->withQueryString();

        return view('pemohon.kunjungan.index', compact('pemohon', 'kunjungans'));
    }

    /**
     * Form ajukan kunjungan baru.
     */
    public function create(Request $request): View
    {
        $pemohon = $this->pemohon($request);

        $karyawans = Karyawan::query()
            ->orderBy('nama')
            ->get();

        $settings = SettingKunjungan::query()
            ->lengkap()
            ->aktif()
            ->orderBy('setting_kunjungan.tanggal')
            ->orderBy('karyawan.nama')
            ->get();

        return view('pemohon.kunjungan.form', [
            'pemohon' => $pemohon,
            'kunjungan' => null,
            'karyawans' => $karyawans,
            'settings' => $settings,
        ]);
    }

    /**
     * Simpan pengajuan kunjungan baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $pemohon = $this->pemohon($request);

        $data = $request->validate([
            'karyawan_id' => ['required', 'exists:karyawan,id'],
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'jam' => ['required', 'date_format:H:i'],
            'keperluan' => ['required', 'string', 'max:255'],
        ]);

        $error = $this->periksaJadwal(
            (int) $data['karyawan_id'],
            $data['tanggal'],
            $data['jam']
        ) ?? $this->periksaDuplikat(
            $pemohon->id,
            (int) $data['karyawan_id'],
            $data['tanggal'],
            $data['jam']
        );

        if ($error !== null) {
            return back()->withInput()->withErrors($error);
        }

        Kunjungan::create([
            'pemohon_id' => $pemohon->id,
            'karyawan_id' => $data['karyawan_id'],
            'tanggal' => $data['tanggal'],
            'jam' => $data['jam'],
            'keperluan' => $data['keperluan'],
            'status' => 'menunggu',
        ]);

        return redirect()
            ->route('pemohon.kunjungan.index')
            ->with('success', 'Pengajuan kunjungan berhasil dikirim.');
    }

    /**
     * Form ubah pengajuan.
     */
    public function edit(Request $request, Kunjungan $kunjungan): View
    {
        $pemohon = $this->pemohon($request);

        $this->pastikanMilikPemohon($kunjungan, $pemohon->id);
        $this->pastikanMasihMenunggu($kunjungan);

        $karyawans = Karyawan::query()
            ->orderBy('nama')
            ->get();

        $settings = SettingKunjungan::query()
            ->lengkap()
            ->aktif()
            ->orderBy('setting_kunjungan.tanggal')
            ->orderBy('karyawan.nama')
            ->get();

        return view('pemohon.kunjungan.form', [
            'pemohon' => $pemohon,
            'kunjungan' => $kunjungan,
            'karyawans' => $karyawans,
            'settings' => $settings,
        ]);
    }

    /**
     * Perbarui pengajuan kunjungan.
     */
    public function update(
        Request $request,
        Kunjungan $kunjungan
    ): RedirectResponse {
        $pemohon = $this->pemohon($request);

        $this->pastikanMilikPemohon($kunjungan, $pemohon->id);
        $this->pastikanMasihMenunggu($kunjungan);

        $data = $request->validate([
            'karyawan_id' => ['required', 'exists:karyawan,id'],
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'jam' => ['required', 'date_format:H:i'],
            'keperluan' => ['required', 'string', 'max:255'],
        ]);

        $error = $this->periksaJadwal(
            (int) $data['karyawan_id'],
            $data['tanggal'],
            $data['jam']
        ) ?? $this->periksaDuplikat(
            $pemohon->id,
            (int) $data['karyawan_id'],
            $data['tanggal'],
            $data['jam'],
            $kunjungan->id
        );

        if ($error !== null) {
            return back()->withInput()->withErrors($error);
        }

        $kunjungan->update([
            'karyawan_id' => $data['karyawan_id'],
            'tanggal' => $data['tanggal'],
            'jam' => $data['jam'],
            'keperluan' => $data['keperluan'],
        ]);

        return redirect()
            ->route('pemohon.kunjungan.index')
            ->with('success', 'Pengajuan kunjungan berhasil diperbarui.');
    }

    /**
     * Hapus pengajuan kunjungan.
     */
    public function destroy(
        Request $request,
        Kunjungan $kunjungan
    ): RedirectResponse {
        $pemohon = $this->pemohon($request);

        $this->pastikanMilikPemohon($kunjungan, $pemohon->id);
        $this->pastikanMasihMenunggu($kunjungan);

        $kunjungan->delete();

        return redirect()
            ->route('pemohon.kunjungan.index')
            ->with('success', 'Pengajuan kunjungan berhasil dihapus.');
    }

    /**
     * Profil pemohon milik pengguna yang sedang login.
     */
    private function pemohon(Request $request): Pemohon
    {
        $pemohon = $request->user()->profilPemohon();

        abort_if(
            $pemohon === null,
            403,
            'Profil pemohon tidak ditemukan.'
        );

        return $pemohon;
    }

    /**
     * Pastikan kunjungan milik pemohon yang sedang login.
     */
    private function pastikanMilikPemohon(
        Kunjungan $kunjungan,
        int $pemohonId
    ): void {
        abort_if(
            $kunjungan->pemohon_id !== $pemohonId,
            403,
            'Anda hanya bisa mengelola kunjungan milik Anda sendiri.'
        );
    }

    /**
     * Pastikan pengajuan masih menunggu.
     */
    private function pastikanMasihMenunggu(Kunjungan $kunjungan): void
    {
        abort_unless(
            $kunjungan->masihMenunggu(),
            403,
            'Kunjungan yang sudah diproses tidak bisa diubah lagi.'
        );
    }

    /**
     * Pastikan ada jadwal layanan aktif dan jam sesuai rentang.
     *
     * @return array<string, string>|null
     */
    private function periksaJadwal(
        int $karyawanId,
        string $tanggal,
        string $jam
    ): ?array {
        $setting = SettingKunjungan::query()
            ->where('karyawan_id', $karyawanId)
            ->whereDate('tanggal', $tanggal)
            ->where('status', SettingKunjungan::STATUS_AKTIF)
            ->first();

        if ($setting === null) {
            return [
                'tanggal' => 'Belum ada layanan kunjungan untuk karyawan tersebut pada tanggal itu. Pilih tanggal lain sesuai jadwal.',
            ];
        }

        $jamAktif = substr($setting->jam_aktif, 0, 5);
        $jamNonAktif = substr($setting->jam_non_aktif, 0, 5);

        if ($jam < $jamAktif || $jam > $jamNonAktif) {
            return [
                'jam' => 'Jam kunjungan harus di antara '
                    . $jamAktif . ' dan ' . $jamNonAktif . '.',
            ];
        }

        return null;
    }

    /**
     * Pastikan tidak ada pengajuan pada jam yang sama.
     *
     * @return array<string, string>|null
     */
    private function periksaDuplikat(
        int $pemohonId,
        int $karyawanId,
        string $tanggal,
        string $jam,
        ?int $kecualiId = null
    ): ?array {
        $duplikat = Kunjungan::query()
            ->where('pemohon_id', $pemohonId)
            ->where('karyawan_id', $karyawanId)
            ->whereDate('tanggal', $tanggal)
            ->where('jam', $jam)
            ->when($kecualiId !== null, function ($query) use ($kecualiId) {
                $query->where('id', '!=', $kecualiId);
            })
            ->exists();

        if ($duplikat) {
            return [
                'jam' => 'Anda sudah memiliki pengajuan kepada karyawan tersebut pada tanggal dan jam yang sama.',
            ];
        }

        return null;
    }
}