<?php
namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\Kunjungan;
use App\Models\Pemohon;
use App\Models\SettingKunjungan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KunjunganFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Pemohon $pemohon;

    private Karyawan $karyawan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->pemohon()->create();

        $this->pemohon = Pemohon::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->karyawan = Karyawan::factory()->create([
            'jabatan' => 'Kepala Bidang Pelayanan',
        ]);
    }

    /**
     * Buat jadwal layanan untuk karyawan pada tanggal tertentu.
     */
    private function buatSetting(
        string $tanggal,
        string $jamAktif = '08:00',
        string $jamNonAktif = '14:00'
    ): SettingKunjungan {
        return SettingKunjungan::factory()->create([
            'karyawan_id'   => $this->karyawan->id,
            'jabatan'       => $this->karyawan->jabatan,
            'tanggal'       => $tanggal,
            'jam_aktif'     => $jamAktif,
            'jam_non_aktif' => $jamNonAktif,
        ]);
    }

    /**
     * Data standar untuk form pengajuan kunjungan.
     *
     * @return array<string, mixed>
     */
    private function payload(
        string $tanggal,
        string $jam = '09:00'
    ): array {
        return [
            'karyawan_id' => $this->karyawan->id,
            'tanggal'     => $tanggal,
            'jam'         => $jam,
            'keperluan'   => 'Konsultasi izin usaha',
        ];
    }

    public function test_pemohon_bisa_mengajukan_kunjungan_sesuai_jadwal_layanan(): void
    {
        $tanggal = now()->addDays(2)->toDateString();

        $this->buatSetting($tanggal);

        $this->actingAs($this->user)
            ->post(
                route('pemohon.kunjungan.store'),
                $this->payload($tanggal)
            )
            ->assertRedirect(route('pemohon.kunjungan.index'));

        $this->assertDatabaseHas('kunjungan', [
            'pemohon_id'  => $this->pemohon->id,
            'karyawan_id' => $this->karyawan->id,
            'tanggal'     => $tanggal,
            'jam'         => '09:00',
            'status'      => 'menunggu',
        ]);
    }

    public function test_pengajuan_ditolak_bila_tidak_ada_setting_layanan(): void
    {
        $tanggal = now()->addDays(2)->toDateString();

        $this->actingAs($this->user)
            ->post(
                route('pemohon.kunjungan.store'),
                $this->payload($tanggal)
            )
            ->assertSessionHasErrors('tanggal');

        $this->assertDatabaseCount('kunjungan', 0);
    }

    public function test_pengajuan_ditolak_bila_jam_di_luar_rentang_layanan(): void
    {
        $tanggal = now()->addDays(2)->toDateString();

        $this->buatSetting($tanggal, '08:00', '14:00');

        $this->actingAs($this->user)
            ->post(
                route('pemohon.kunjungan.store'),
                $this->payload($tanggal, '16:00')
            )
            ->assertSessionHasErrors('jam');

        $this->assertDatabaseCount('kunjungan', 0);
    }

    public function test_pengajuan_ditolak_bila_setting_nonaktif(): void
    {
        $tanggal = now()->addDays(2)->toDateString();

        $this->buatSetting($tanggal);

        SettingKunjungan::query()
            ->where('karyawan_id', $this->karyawan->id)
            ->whereDate('tanggal', $tanggal)
            ->update([
                'status' => SettingKunjungan::STATUS_NONAKTIF,
            ]);

        $this->actingAs($this->user)
            ->post(
                route('pemohon.kunjungan.store'),
                $this->payload($tanggal)
            )
            ->assertSessionHasErrors('tanggal');

        $this->assertDatabaseCount('kunjungan', 0);
    }

    public function test_pengajuan_duplikat_ditolak(): void
    {
        $tanggal = now()->addDays(2)->toDateString();

        $this->buatSetting($tanggal);

        Kunjungan::factory()->create([
            'pemohon_id'  => $this->pemohon->id,
            'karyawan_id' => $this->karyawan->id,
            'tanggal'     => $tanggal,
            'jam'         => '09:00',
        ]);

        $this->actingAs($this->user)
            ->post(
                route('pemohon.kunjungan.store'),
                $this->payload($tanggal)
            )
            ->assertSessionHasErrors('jam');

        $this->assertDatabaseCount('kunjungan', 1);
    }

    public function test_kunjungan_yang_sudah_diproses_tidak_bisa_diubah_pemohon(): void
    {
        $kunjungan = Kunjungan::factory()->disetujui()->create([
            'pemohon_id'  => $this->pemohon->id,
            'karyawan_id' => $this->karyawan->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('pemohon.kunjungan.edit', $kunjungan))
            ->assertForbidden();

        $this->actingAs($this->user)
            ->delete(route('pemohon.kunjungan.destroy', $kunjungan))
            ->assertForbidden();
    }

    public function test_pemohon_tidak_bisa_mengubah_kunjungan_milik_pemohon_lain(): void
    {
        $userLain = User::factory()->pemohon()->create();

        $pemohonLain = Pemohon::factory()->create([
            'user_id' => $userLain->id,
        ]);

        $kunjungan = Kunjungan::factory()->create([
            'pemohon_id'  => $pemohonLain->id,
            'karyawan_id' => $this->karyawan->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('pemohon.kunjungan.edit', $kunjungan))
            ->assertForbidden();
    }

    public function test_pemohon_bisa_mengubah_pengajuan_yang_masih_menunggu(): void
    {
        $tanggal = now()->addDays(2)->toDateString();

        $this->buatSetting($tanggal);

        $kunjungan = Kunjungan::factory()->create([
            'pemohon_id'  => $this->pemohon->id,
            'karyawan_id' => $this->karyawan->id,
            'tanggal'     => $tanggal,
            'jam'         => '09:00',
            'keperluan'   => 'Keperluan lama',
            'status'      => 'menunggu',
        ]);

        // Siapkan data secara terpisah agar keperluan tertimpa dengan benar.
        $data              = $this->payload($tanggal, '10:00');
        $data['keperluan'] = 'Keperluan diperbarui';

        $this->actingAs($this->user)
            ->put(
                route('pemohon.kunjungan.update', $kunjungan),
                $data
            )
            ->assertRedirect(route('pemohon.kunjungan.index'));

        $this->assertDatabaseHas('kunjungan', [
            'id'        => $kunjungan->id,
            'jam'       => '10:00',
            'keperluan' => 'Keperluan diperbarui',
            'status'    => 'menunggu',
        ]);
    }

    public function test_pemohon_bisa_menghapus_pengajuan_yang_masih_menunggu(): void
    {
        $kunjungan = Kunjungan::factory()->create([
            'pemohon_id'  => $this->pemohon->id,
            'karyawan_id' => $this->karyawan->id,
            'status'      => 'menunggu',
        ]);

        $this->actingAs($this->user)
            ->delete(route('pemohon.kunjungan.destroy', $kunjungan))
            ->assertRedirect(route('pemohon.kunjungan.index'));

        $this->assertDatabaseMissing('kunjungan', [
            'id' => $kunjungan->id,
        ]);
    }
}
