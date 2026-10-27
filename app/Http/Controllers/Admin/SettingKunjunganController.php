<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\SettingKunjungan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingKunjunganController extends Controller
{
    /**
     * Daftar setting layanan kunjungan.
     */
    public function index(Request $request): View
    {
        $settings = SettingKunjungan::query()
            ->lengkap()
            ->when($request->filled('q'), function ($query) use ($request) {
                $kataKunci = '%'.$request->input('q').'%';
                $query->where(function ($query) use ($kataKunci) {
                    $query->where('karyawan.nama', 'like', $kataKunci)
                        ->orWhere('setting_kunjungan.jabatan', 'like', $kataKunci);
                });
            })
            ->when($request->filled('tanggal'), function ($query) use ($request) {
                $query->whereDate('setting_kunjungan.tanggal', $request->input('tanggal'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('setting_kunjungan.status', $request->input('status'));
            })
            ->orderByDesc('setting_kunjungan.tanggal')
            ->orderBy('karyawan.nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.setting-kunjungan.index', compact('settings'));
    }

    /**
     * Form tambah setting kunjungan.
     */
    public function create(): View
    {
        return view('admin.setting-kunjungan.form', [
            'setting' => null,
            'karyawans' => Karyawan::orderBy('nama')->get(),
        ]);
    }

    /**
     * Simpan setting kunjungan baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        SettingKunjungan::create($data);

        return redirect()->route('admin.setting-kunjungan.index')->with('success', 'Setting
kunjungan berhasil ditambahkan.');
    }

    /**
     * Form ubah setting kunjungan.
     */
    public function edit(SettingKunjungan $setting_kunjungan): View
    {
        return view('admin.setting-kunjungan.form', [
            'setting' => $setting_kunjungan,
            'karyawans' => Karyawan::orderBy('nama')->get(),
        ]);
    }

    /**
     * Perbarui setting kunjungan.
     */
    public function update(Request $request, SettingKunjungan $setting_kunjungan): RedirectResponse
    {
        $data = $this->validasi($request, $setting_kunjungan->id);
        $setting_kunjungan->update($data);

        return redirect()->route('admin.setting-kunjungan.index')->with('success', 'Setting
kunjungan berhasil diperbarui.');
    }

    /**
     * Hapus setting kunjungan.
     */
    public function destroy(SettingKunjungan $setting_kunjungan): RedirectResponse
    {
        $setting_kunjungan->delete();

        return redirect()->route('admin.setting-kunjungan.index')->with('success', 'Setting
kunjungan berhasil dihapus.');
    }

    /**
     * Validasi input setting kunjungan.
     *
     * @return array<string, mixed>
     */
    private function validasi(Request $request, ?int $kecualiId = null): array
    {
        $data = $request->validate([
            'karyawan_id' => ['required', 'exists:karyawan,id'],
            'tanggal' => [
                'required',

                'date',
                Rule::unique('setting_kunjungan', 'tanggal')
                    ->where(fn ($query) => $query->where('karyawan_id', $request->input('karyawan_id')))
                    ->ignore($kecualiId),
            ],
            'jam_aktif' => ['required', 'date_format:H:i'],
            'jam_non_aktif' => ['required', 'date_format:H:i', 'after:jam_aktif'],
            'status' => ['required', Rule::in([SettingKunjungan::STATUS_AKTIF,
                SettingKunjungan::STATUS_NONAKTIF])],
        ]);
        $data['jabatan'] = Karyawan::where('id', $data['karyawan_id'])->value('jabatan');

        return $data;
    }
}
