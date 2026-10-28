<?php
    namespace Tests\Feature;

    use App\Models\User;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Tests\TestCase;

    class RoleAccessTest extends TestCase
    {
    use RefreshDatabase;

    public function test_halaman_publik_bisa_diakses_tanpa_login(): void
    {
        $this->get(route('home'))->assertOk();
        $this->get(route('jadwal-kunjungan'))->assertOk();
        $this->get(route('struktur-karyawan'))->assertOk();
        $this->get(route('login'))->assertOk();
        $this->get(route('daftar'))->assertOk();
    }

    public function test_dashboard_diarahkan_ke_login_jika_belum_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $this->get(route('karyawan.dashboard'))
            ->assertRedirect(route('login'));

        $this->get(route('pemohon.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_tidak_bisa_mengakses_dashboard_peran_lain(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('karyawan.dashboard'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('pemohon.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_tidak_bisa_mengakses_daftar_kunjungan_karyawan(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('karyawan.kunjungan.index'))
            ->assertForbidden();
    }

    public function test_karyawan_tidak_bisa_mengakses_dashboard_peran_lain(): void
    {
        $karyawan = User::factory()->karyawan()->create();

        $this->actingAs($karyawan)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->actingAs($karyawan)
            ->get(route('pemohon.dashboard'))
            ->assertForbidden();
    }

    public function test_pemohon_tidak_bisa_mengakses_dashboard_peran_lain(): void
    {
        $pemohon = User::factory()->pemohon()->create();

        $this->actingAs($pemohon)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->actingAs($pemohon)
            ->get(route('karyawan.dashboard'))
            ->assertForbidden();
    }
}
