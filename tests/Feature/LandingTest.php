<?php
    namespace Tests\Feature;

    use App\Models\Karyawan;
    use App\Models\SettingKunjungan;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Tests\TestCase;

    class LandingTest extends TestCase
    {
    use RefreshDatabase;

    public function test_halaman_beranda_bisa_diakses_tanpa_login(): void
    {
        $this->get('/')
            ->assertOk();
    }

    public function test_halaman_jadwal_kunjungan_bisa_diakses_tanpa_login(): void
    {
        $this->get('/jadwal-kunjungan')
            ->assertOk();
    }

    public function test_halaman_struktur_karyawan_bisa_diakses_tanpa_login(): void
    {
        $this->get('/struktur-karyawan')
            ->assertOk();
    }

    public function test_beranda_menampilkan_jadwal_terdekat(): void
    {
        $karyawan = Karyawan::factory()->create([
            'nama' => 'Rina Kartika',
        ]);

        SettingKunjungan::factory()->create([
            'karyawan_id' => $karyawan->id,
            'tanggal'     => now()->addDays(2)->toDateString(),
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Rina Kartika')
            ->assertSee('Jadwal Terdekat');
    }

    public function test_jadwal_kunjungan_bisa_dicari_dan_difilter_tanggal(): void
    {
        $karyawan = Karyawan::factory()->create([
            'nama' => 'Budi Santoso',
        ]);

        SettingKunjungan::factory()->create([
            'karyawan_id' => $karyawan->id,
            'jabatan'     => $karyawan->jabatan,
            'tanggal'     => now()->addDays(3)->toDateString(),
        ]);

        $this->get(route('jadwal-kunjungan', ['q' => 'Budi']))
            ->assertOk()
            ->assertSee('Budi Santoso');

        $this->get(route('jadwal-kunjungan', ['q' => 'Tidak Ada']))
            ->assertOk()
            ->assertSee(
                'Belum ada jadwal layanan kunjungan yang aktif.'
            );
    }

    public function test_struktur_karyawan_dikelompokkan_berdasarkan_jabatan(): void
    {
        Karyawan::factory()->create([
            'nama'    => 'Agus Pratama',
            'jabatan' => 'Kepala Dinas',
        ]);

        Karyawan::factory()->create([
            'nama'    => 'Maya Lestari',
            'jabatan' => 'Staf Pelayanan',
        ]);

        $this->get(route('struktur-karyawan'))
            ->assertOk()
            ->assertSee('Kepala Dinas')
            ->assertSee('Agus Pratama')
            ->assertSee('Staf Pelayanan')
            ->assertSee('Maya Lestari');
    }
}
