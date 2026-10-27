<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KunjunganController extends Controller
{
/**
 * Daftar semua pengajuan kunjungan.
 */
    public function index(Request $request): View
    {
        $kunjungans = Kunjungan::query()
            ->lengkap()
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('kunjungan.status', $request->input('status'));
            })
            ->when($request->filled('tanggal'), function ($query) use ($request) {
                $query->whereDate('kunjungan.tanggal', $request->input('tanggal'));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $kataKunci = '%' . $request->input('q') . '%';
                $query->where(function ($query) use ($kataKunci) {
                    $query->where('pemohon.nama', 'like', $kataKunci)
                        ->orWhere('karyawan.nama', 'like', $kataKunci)
                        ->orWhere('kunjungan.keperluan', 'like', $kataKunci);

                });

            })
            ->urut()
            ->paginate(10)
            ->withQueryString();
        return view('admin.kunjungan.index', compact('kunjungans'));
    }
/**
 * Form pemrosesan pengajuan kunjungan.
 */
    public function edit(Kunjungan $kunjungan): View
    {
        $kunjungan = Kunjungan::query()
            ->lengkap()
            ->where('kunjungan.id', $kunjungan->id)
            ->firstOrFail();
        return view('admin.kunjungan.form', compact('kunjungan'));
    }
/**
 * Perbarui status pengajuan kunjungan.
 */
    public function update(Request $request, Kunjungan $kunjungan): RedirectResponse
    {
        $data = $request->validate([
            'status'  => ['required', Rule::in(['disetujui', 'ditolak'])],
            'catatan' => ['nullable', 'string', 'max:255', 'required_if:status,ditolak'],
        ]);
        $kunjungan->update([
            'status'  => $data['status'],
            'catatan' => $data['status'] === 'ditolak' ? $data['catatan'] : null,
        ]);
        return redirect()->route('admin.kunjungan.index')->with('success', 'Status pengajuan
kunjungan berhasil diperbarui.');
    }
}
