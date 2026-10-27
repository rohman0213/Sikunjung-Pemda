<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_bisa_login_dan_diarahkan_ke_dashboard(): void
    {
        User::factory()->create([
            'email'    => 'admin-test@sikunjung.test',
            'password' => 'password',
            'role'     => 'admin',
        ]);

        $response = $this->post(route('login.store'), [
            'email'    => 'admin-test@sikunjung.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        User::factory()->create([
            'email'    => 'admin-test@sikunjung.test',
            'password' => 'password',
            'role'     => 'admin',
        ]);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email'    => 'admin-test@sikunjung.test',
            'password' => 'password-salah',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_karyawan_diarahkan_ke_dashboard_karyawan(): void
    {
        User::factory()->create([
            'email'    => 'karyawan-test@sikunjung.test',
            'password' => 'password',
            'role'     => 'karyawan',
        ]);

        $response = $this->post(route('login.store'), [
            'email'    => 'karyawan-test@sikunjung.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('karyawan.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_pemohon_diarahkan_ke_dashboard_pemohon(): void
    {
        User::factory()->create([
            'email'    => 'pemohon-test@sikunjung.test',
            'password' => 'password',
            'role'     => 'pemohon',
        ]);

        $response = $this->post(route('login.store'), [
            'email'    => 'pemohon-test@sikunjung.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('pemohon.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_pengguna_bisa_logout(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_pemohon_bisa_mendaftar_dan_profilnya_dibuat(): void
    {
        $response = $this->post(route('daftar.store'), [
            'name'                  => 'Pemohon Test',
            'email'                 => 'pemohon-baru@sikunjung.test',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'nik'                   => '3201234567890123',
            'no_telepon'            => '081234567890',
            'alamat'                => 'Probolinggo',
        ]);

        $response->assertRedirect(route('pemohon.dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('user', [
            'name'  => 'Pemohon Test',
            'email' => 'pemohon-baru@sikunjung.test',
            'role'  => 'pemohon',
        ]);

        $this->assertDatabaseHas('pemohon', [
            'nik'  => '3201234567890123',
            'nama' => 'Pemohon Test',
        ]);
    }

    public function test_registrasi_ditolak_jika_nik_bukan_16_digit(): void
    {
        $response = $this->from(route('daftar'))->post(route('daftar.store'), [
            'name'                  => 'Pemohon Test',
            'email'                 => 'pemohon-invalid@sikunjung.test',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'nik'                   => '12345',
        ]);

        $response->assertRedirect(route('daftar'));
        $response->assertSessionHasErrors('nik');

        $this->assertDatabaseMissing('user', [
            'email' => 'pemohon-invalid@sikunjung.test',
        ]);
    }

}
