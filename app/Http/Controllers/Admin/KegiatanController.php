<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Kegiatan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KegiatanController extends Controller
{
    /**
     * Daftar kegiatan seluruh karyawan.
     */
    public function index(Request $request): View
    {
        $kegiatans = Kegiatan::query()
            ->lengkap()
            ->when($request->filled('q'), function ($query) use ($request) {
                $kataKunci = '%'.$request->input('q').'%';
                $query->where(function ($query) use ($kataKunci) {
                    $query->where('karyawan.nama', 'like', $kataKunci)
                        ->orWhere('kegiatan.deskripsi_kegiatan', 'like', $kataKunci);
                });
            })
            ->when($request->filled('karyawan_id'), function ($query) use ($request) {
                $query->where('kegiatan.karyawan_id', $request->input('karyawan_id'));
            })
            ->urut()
            ->paginate(10)
            ->withQueryString();

        $karyawans = Karyawan::orderBy('nama')->get();

        return view('admin.kegiatan.index', compact('kegiatans', 'karyawans'));
    }

    /**
     * Form tambah kegiatan.
     */
    public function create(): View
    {
        return view('admin.kegiatan.form', [
            'kegiatan' => null,
            'karyawans' => Karyawan::orderBy('nama')->get(),
        ]);
    }

    /**
     * Simpan kegiatan baru.
     */
    public function store(Request $request): RedirectResponse
    {
        Kegiatan::create($this->validasi($request));

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    /**
     * Form ubah kegiatan.
     */
    public function edit(Kegiatan $kegiatan): View
    {
        return view('admin.kegiatan.form', [
            'kegiatan' => $kegiatan,
            'karyawans' => Karyawan::orderBy('nama')->get(),
        ]);
    }

    /**
     * Perbarui kegiatan.
     */
    public function update(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        $kegiatan->update($this->validasi($request));

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    /**
     * Hapus kegiatan.
     */
    public function destroy(Kegiatan $kegiatan): RedirectResponse
    {
        $kegiatan->delete();

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }

    /**
     * Validasi input kegiatan.
     *
     * @return array<string, mixed>
     */
    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'karyawan_id' => ['required', 'exists:karyawan,id'],
            'tanggal_awal' => ['required', 'date'],
            'tanggal_akhir' => ['required', 'date', 'after_or_equal:tanggal_awal'],
            'deskripsi_kegiatan' => ['required', 'string'],
        ]);

        $data['jabatan'] = Karyawan::where('id', $data['karyawan_id'])->value('jabatan');

        return $data;
    }
}
