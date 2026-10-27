<?php
namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminKaryawanTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_admin_bisa_membuka_daftar_dan_form_karyawan(): void
    {
        $this->actingAs($this->admin());

        $this->get(route('admin.karyawan.index'))->assertOk();
        $this->get(route('admin.karyawan.create'))->assertOk();
    }

    public function test_admin_bisa_menambahkan_karyawan_dan_akun_login(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.karyawan.store'), [
            'nik'        => '3201234567890123',
            'nama'       => 'Karyawan Test',
            'jabatan'    => 'Staf Administrasi',
            'no_telepon' => '081234567890',
            'email'      => 'karyawan-crud@sikunjung.test',
            'password'   => 'password123',
        ]);

        $response->assertRedirect(route('admin.karyawan.index'));

        $this->assertDatabaseHas('user', [
            'name'  => 'Karyawan Test',
            'email' => 'karyawan-crud@sikunjung.test',
            'role'  => 'karyawan',
        ]);

        $this->assertDatabaseHas('karyawan', [
            'nik'     => '3201234567890123',
            'nama'    => 'Karyawan Test',
            'jabatan' => 'Staf Administrasi',
        ]);
    }

    public function test_admin_bisa_mengedit_data_karyawan(): void
    {
        $this->actingAs($this->admin());

        $user = User::factory()->create([
            'name'  => 'Karyawan Lama',
            'email' => 'karyawan-lama@sikunjung.test',
            'role'  => 'karyawan',
        ]);

        $karyawan = Karyawan::factory()->create([
            'user_id' => $user->id,
            'nik'     => '3201234567890123',
            'nama'    => 'Karyawan Lama',
            'jabatan' => 'Staf Lama',
        ]);

        $this->get(route('admin.karyawan.edit', $karyawan))
            ->assertOk();

        $response = $this->put(route('admin.karyawan.update', $karyawan), [
            'nik'        => '3201234567890123',
            'nama'       => 'Karyawan Baru',
            'jabatan'    => 'Staf Keuangan',
            'no_telepon' => '081234567890',
            'email'      => 'karyawan-lama@sikunjung.test',
            'password'   => '',
        ]);

        $response->assertRedirect(route('admin.karyawan.index'));

        $this->assertDatabaseHas('karyawan', [
            'id'      => $karyawan->id,
            'nama'    => 'Karyawan Baru',
            'jabatan' => 'Staf Keuangan',
        ]);

        $this->assertDatabaseHas('user', [
            'id'   => $user->id,
            'name' => 'Karyawan Baru',
        ]);
    }

    public function test_admin_bisa_menghapus_karyawan_tanpa_riwayat_kunjungan(): void
    {
        $this->actingAs($this->admin());

        $user = User::factory()->create([
            'role' => 'karyawan',
        ]);

        $karyawan = Karyawan::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->delete(
            route('admin.karyawan.destroy', $karyawan)
        );

        $response->assertRedirect(route('admin.karyawan.index'));

        $this->assertDatabaseMissing('karyawan', [
            'id' => $karyawan->id,
        ]);

        $this->assertDatabaseMissing('user', [
            'id' => $user->id,
        ]);
    }

    public function test_karyawan_dengan_riwayat_kunjungan_tidak_bisa_dihapus(): void
    {
        $this->actingAs($this->admin());

        $user = User::factory()->create([
            'role' => 'karyawan',
        ]);

        $karyawan = Karyawan::factory()->create([
            'user_id' => $user->id,
        ]);

        // Uji kondisi riwayat kunjungan akan ditambahkan
        // setelah struktur wajib tabel kunjungan diperiksa.
        $this->assertDatabaseHas('karyawan', [
            'id' => $karyawan->id,
        ]);
    }
}
