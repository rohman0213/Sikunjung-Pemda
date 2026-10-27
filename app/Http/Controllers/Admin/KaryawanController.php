<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Kunjungan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KaryawanController extends Controller
{
    /**
     * Daftar karyawan beserta jumlah kunjungannya.
     */
    public function index(Request $request): View
    {
        $karyawans = Karyawan::query()
            ->leftJoin('user', 'user.id', '=', 'karyawan.user_id')
            ->leftJoin('kunjungan', 'kunjungan.karyawan_id', '=', 'karyawan.id')
            ->select(
                'karyawan.id',
                'karyawan.user_id',
                'karyawan.nik',
                'karyawan.nama',
                'karyawan.jabatan',
                'karyawan.no_telepon',
                'karyawan.created_at',
                'karyawan.updated_at',
                'user.email as user_email',
            )
            ->selectRaw('count(kunjungan.id) as kunjungan_count')
            ->when($request->filled('q'), function ($query) use ($request) {
                $kataKunci = '%'.$request->input('q').'%';
                $query->where(function ($query) use ($kataKunci) {
                    $query->where('karyawan.nik', 'like', $kataKunci)
                        ->orWhere('karyawan.nama', 'like', $kataKunci)
                        ->orWhere('karyawan.jabatan', 'like', $kataKunci);

                });
            })
            ->groupBy(
                'karyawan.id',
                'karyawan.user_id',
                'karyawan.nik',
                'karyawan.nama',
                'karyawan.jabatan',
                'karyawan.no_telepon',
                'karyawan.created_at',
                'karyawan.updated_at',
                'user.email',
            )
            ->orderBy('karyawan.nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.karyawan.index', compact('karyawans'));
    }

    /**
     * Form tambah karyawan.
     */
    public function create(): View
    {
        return view('admin.karyawan.form', ['karyawan' => null]);
    }

    /**
     * Simpan karyawan baru beserta akun loginnya.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nik' => ['required', 'string', 'digits:16', 'unique:karyawan,nik'],
            'nama' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:255'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:user,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);
        DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['nama'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => User::ROLE_KARYAWAN,
            ]);
            Karyawan::create([
                'user_id' => $user->id,
                'nik' => $data['nik'],
                'nama' => $data['nama'],
                'jabatan' => $data['jabatan'],
                'no_telepon' => $data['no_telepon'] ?? null,
            ]);
        });

        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan berhasil
ditambahkan.');
    }

    /**
     * Form ubah karyawan.
     */
    public function edit(Karyawan $karyawan): View
    {
        $karyawan->setAttribute('user_email', $karyawan->emailAkun());

        return view('admin.karyawan.form', compact('karyawan'));
    }

    /**
     * Perbarui karyawan dan akun loginnya.
     */
    public function update(Request $request, Karyawan $karyawan): RedirectResponse
    {
        $data = $request->validate([
            'nik' => ['required', 'string', 'digits:16', Rule::unique('karyawan', 'nik')->ignore($karyawan->id)],
            'nama' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:255'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', Rule::unique('user', 'email')->ignore($karyawan->user_id)],
            'password' => ['nullable', 'string', 'min:8'],
        ]);
        DB::transaction(function () use ($data, $karyawan) {
            $user = User::where('id', $karyawan->user_id)->first();
            $userData = [
                'name' => $data['nama'],
                'email' => $data['email'],
            ];
            if (! empty($data['password'])) {
                $userData['password'] = $data['password'];
            }
            $user?->update($userData);
            $karyawan->update([
                'nik' => $data['nik'],
                'nama' => $data['nama'],
                'jabatan' => $data['jabatan'],
                'no_telepon' => $data['no_telepon'] ?? null,
            ]);
        });

        return redirect()->route('admin.karyawan.index')->with('success', 'Data karyawan
berhasil diperbarui.');
    }

    /**
     * Hapus karyawan beserta akun loginnya.
     */
    public function destroy(Karyawan $karyawan): RedirectResponse
    {
        if (Kunjungan::where('karyawan_id', $karyawan->id)->exists()) {
            return back()->with('error', 'Karyawan tidak bisa dihapus karena sudah memiliki
riwayat kunjungan.');
        }
        DB::transaction(function () use ($karyawan) {
            if ($karyawan->user_id !== null) {
                User::where('id', $karyawan->user_id)->delete();
            } else {
                $karyawan->delete();
            }
        });

        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan berhasil
dihapus.');
    }
}
