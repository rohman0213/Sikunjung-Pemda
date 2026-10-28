<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\Kegiatan;
use App\Models\Kunjungan;
use App\Models\Pemohon;
use App\Models\SettingKunjungan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelKaryawanTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Karyawan $karyawan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->karyawan()->create();

        $this->karyawan = Karyawan::factory()->create([
            'user_id' => $this->user->id,
            'nama' => 'Rina Kartika',
            'jabatan' => 'Sekretaris Dinas',
        ]);
    }

    public function test_dashboard_menampilkan_ringkasan_kunjungan_karyawan(): void
    {
        $this->actingAs($this->user)
            ->get(route('karyawan.dashboard'))
            ->assertOk()
            ->assertSee('Rina Kartika')
            ->assertSee('Kunjungan Hari Ini');
    }

    public function test_karyawan_hanya_melihat_kunjungan_miliknya(): void
    {
        $karyawanLain = Karyawan::factory()->create();

        Kunjungan::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'keperluan' => 'Keperluan untuk Rina',
        ]);

        Kunjungan::factory()->create([
            'karyawan_id' => $karyawanLain->id,
            'keperluan' => 'Keperluan untuk karyawan lain',
        ]);

        $this->actingAs($this->user)
            ->get(route('karyawan.kunjungan.index'))
            ->assertOk()
            ->assertSee('Keperluan untuk Rina')
            ->assertDontSee('Keperluan untuk karyawan lain');
    }

    public function test_karyawan_bisa_menyetujui_kunjungan_miliknya(): void
    {
        $kunjungan = Kunjungan::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'status' => 'menunggu',
        ]);

        $this->actingAs($this->user)
            ->put(route('karyawan.kunjungan.update', $kunjungan), [
                'status' => 'disetujui',
            ])
            ->assertRedirect(route('karyawan.kunjungan.index'));

        $this->assertDatabaseHas('kunjungan', [
            'id' => $kunjungan->id,
            'status' => 'disetujui',
            'catatan' => null,
        ]);
    }

    public function test_penolakan_tanpa_catatan_ditolak(): void
    {
        $kunjungan = Kunjungan::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'status' => 'menunggu',
        ]);

        $this->actingAs($this->user)
            ->put(route('karyawan.kunjungan.update', $kunjungan), [
                'status' => 'ditolak',
            ])
            ->assertSessionHasErrors('catatan');

        $this->assertDatabaseHas('kunjungan', [
            'id' => $kunjungan->id,
            'status' => 'menunggu',
        ]);
    }

    public function test_karyawan_tidak_bisa_memproses_kunjungan_karyawan_lain(): void
    {
        $karyawanLain = Karyawan::factory()->create();

        $kunjungan = Kunjungan::factory()->create([
            'karyawan_id' => $karyawanLain->id,
            'status' => 'menunggu',
        ]);

        $this->actingAs($this->user)
            ->get(route('karyawan.kunjungan.edit', $kunjungan))
            ->assertForbidden();

        $this->actingAs($this->user)
            ->put(route('karyawan.kunjungan.update', $kunjungan), [
                'status' => 'disetujui',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('kunjungan', [
            'id' => $kunjungan->id,
            'status' => 'menunggu',
        ]);
    }

    public function test_karyawan_bisa_melihat_setting_kunjungan_miliknya(): void
    {
        SettingKunjungan::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'jabatan' => $this->karyawan->jabatan,
        ]);

        $this->actingAs($this->user)
            ->get(route('karyawan.setting.index'))
            ->assertOk();
    }

    public function test_karyawan_bisa_mengelola_kegiatan_miliknya(): void
    {
        $tanggalAwal = now()->addDay()->toDateString();
        $tanggalAkhir = now()->addDays(2)->toDateString();

        $this->actingAs($this->user)
            ->post(route('karyawan.kegiatan.store'), [
                'tanggal_awal' => $tanggalAwal,
                'tanggal_akhir' => $tanggalAkhir,
                'deskripsi_kegiatan' => 'Rapat koordinasi pelayanan',
            ])
            ->assertRedirect(route('karyawan.kegiatan.index'));

        $this->assertDatabaseHas('kegiatan', [
            'karyawan_id' => $this->karyawan->id,
            'jabatan' => 'Sekretaris Dinas',
            'deskripsi_kegiatan' => 'Rapat koordinasi pelayanan',
        ]);

        $kegiatan = Kegiatan::query()
            ->where('karyawan_id', $this->karyawan->id)
            ->firstOrFail();

        $this->actingAs($this->user)
            ->put(route('karyawan.kegiatan.update', $kegiatan), [
                'tanggal_awal' => $tanggalAwal,
                'tanggal_akhir' => $tanggalAkhir,
                'deskripsi_kegiatan' => 'Rapat koordinasi diperbarui',
            ])
            ->assertRedirect(route('karyawan.kegiatan.index'));

        $this->assertDatabaseHas('kegiatan', [
            'id' => $kegiatan->id,
            'deskripsi_kegiatan' => 'Rapat koordinasi diperbarui',
        ]);

        $this->actingAs($this->user)
            ->delete(route('karyawan.kegiatan.destroy', $kegiatan))
            ->assertRedirect(route('karyawan.kegiatan.index'));

        $this->assertDatabaseMissing('kegiatan', [
            'id' => $kegiatan->id,
        ]);
    }

    public function test_karyawan_tidak_bisa_mengelola_kegiatan_karyawan_lain(): void
    {
        $karyawanLain = Karyawan::factory()->create();

        $kegiatan = Kegiatan::factory()->create([
            'karyawan_id' => $karyawanLain->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('karyawan.kegiatan.edit', $kegiatan))
            ->assertForbidden();

        $this->actingAs($this->user)
            ->delete(route('karyawan.kegiatan.destroy', $kegiatan))
            ->assertForbidden();

        $this->assertDatabaseHas('kegiatan', [
            'id' => $kegiatan->id,
        ]);
    }

    public function test_karyawan_melihat_nama_pemohon_pada_daftar_kunjungan(): void
    {
        $pemohon = Pemohon::factory()->create([
            'nama' => 'Dedi Kurniawan',
        ]);

        Kunjungan::factory()->create([
            'pemohon_id' => $pemohon->id,
            'karyawan_id' => $this->karyawan->id,
            'keperluan' => 'Konsultasi izin usaha',
        ]);

        $this->actingAs($this->user)
            ->get(route('karyawan.kunjungan.index'))
            ->assertOk()
            ->assertSee('Dedi Kurniawan')
            ->assertSee('Konsultasi izin usaha');
    }
}
