<?php

namespace App\Http\Controllers;

use App\Models\Pemohon;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthController extends Controller
{
    /*** Tampilkan halaman login.
    */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Proses login dan arahkan sesuai role.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }
        $request->session()->regenerate();

        return redirect()->intended($this->homeRoute($request->user()));
    }

    /**
     * Tampilkan formulir pendaftaran pemohon.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Daftarkan akun pemohon baru beserta profilnya.
     */
    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:user,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nik' => ['required', 'string', 'digits:16', 'unique:pemohon,nik'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
        ]);
        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => User::ROLE_PEMOHON,
            ]);
            Pemohon::create([
                'user_id' => $user->id,
                'nik' => $data['nik'],
                'nama' => $data['name'],
                'no_telepon' => $data['no_telepon'] ?? null,
                'alamat' => $data['alamat'] ?? null,
            ]);

            return $user;
        });
        Auth::login($user);

        return redirect()->route('pemohon.dashboard')->with('success', 'Pendaftaran berhasil.
Selamat datang!');
    }

    /**
     * Logout dan hapus sesi.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Halaman utama sesuai role pengguna.
     */
    private function homeRoute(User $user): string
    {
        return match ($user->role) {
            User::ROLE_ADMIN => route('admin.dashboard'),
            User::ROLE_KARYAWAN => route('karyawan.dashboard'),
            default => route('pemohon.dashboard'),
        };
    }
}
