<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\SettingKunjungan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingKunjunganTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function karyawan(): Karyawan
    {
        return Karyawan::factory()->create();
    }

    public function test_admin_bisa_membuka_daftar_dan_form_setting(): void
    {
        $this->actingAs($this->admin());

        $this->get(route('admin.setting-kunjungan.index'))->assertOk();
        $this->get(route('admin.setting-kunjungan.create'))->assertOk();
    }

    public function test_admin_bisa_menambahkan_setting_kunjungan(): void
    {
        $this->actingAs($this->admin());
        $karyawan = $this->karyawan();

        $response = $this->post(route('admin.setting-kunjungan.store'), [
            'karyawan_id' => $karyawan->id,
            'tanggal' => '2026-11-15',
            'jam_aktif' => '08:00',
            'jam_non_aktif' => '14:00',
            'status' => SettingKunjungan::STATUS_AKTIF,
        ]);

        $response->assertRedirect(route('admin.setting-kunjungan.index'));

        $this->assertDatabaseHas('setting_kunjungan', [
            'karyawan_id' => $karyawan->id,
            'jabatan' => $karyawan->jabatan,
            'tanggal' => '2026-11-15',
            'jam_aktif' => '08:00',
            'jam_non_aktif' => '14:00',
            'status' => SettingKunjungan::STATUS_AKTIF,
        ]);
    }

    public function test_admin_bisa_mengedit_setting_kunjungan(): void
    {
        $this->actingAs($this->admin());
        $karyawan = $this->karyawan();

        $setting = SettingKunjungan::factory()->create([
            'karyawan_id' => $karyawan->id,
            'jabatan' => $karyawan->jabatan,
            'tanggal' => '2026-11-15',
        ]);

        $this->get(route('admin.setting-kunjungan.edit', $setting))
            ->assertOk();

        $response = $this->put(
            route('admin.setting-kunjungan.update', $setting),
            [
                'karyawan_id' => $karyawan->id,
                'tanggal' => '2026-11-16',
                'jam_aktif' => '09:00',
                'jam_non_aktif' => '15:00',
                'status' => SettingKunjungan::STATUS_NONAKTIF,
            ]
        );

        $response->assertRedirect(route('admin.setting-kunjungan.index'));

        $this->assertDatabaseHas('setting_kunjungan', [
            'id' => $setting->id,
            'tanggal' => '2026-11-16',
            'jam_aktif' => '09:00',
            'jam_non_aktif' => '15:00',
            'status' => SettingKunjungan::STATUS_NONAKTIF,
        ]);
    }

    public function test_admin_bisa_menghapus_setting_kunjungan(): void
    {
        $this->actingAs($this->admin());
        $setting = SettingKunjungan::factory()->create();

        $response = $this->delete(
            route('admin.setting-kunjungan.destroy', $setting)
        );

        $response->assertRedirect(route('admin.setting-kunjungan.index'));

        $this->assertDatabaseMissing('setting_kunjungan', [
            'id' => $setting->id,
        ]);
    }

    public function test_jam_nonaktif_harus_setelah_jam_aktif(): void
    {
        $this->actingAs($this->admin());
        $karyawan = $this->karyawan();

        $response = $this->post(route('admin.setting-kunjungan.store'), [
            'karyawan_id' => $karyawan->id,
            'tanggal' => '2026-11-17',
            'jam_aktif' => '14:00',
            'jam_non_aktif' => '08:00',
            'status' => SettingKunjungan::STATUS_AKTIF,
        ]);

        $response->assertSessionHasErrors('jam_non_aktif');

        $this->assertDatabaseMissing('setting_kunjungan', [
            'karyawan_id' => $karyawan->id,
            'tanggal' => '2026-11-17',
        ]);
    }

    public function test_karyawan_tidak_bisa_memiliki_dua_setting_pada_tanggal_yang_sama(): void
    {
        $this->actingAs($this->admin());
        $karyawan = $this->karyawan();

        SettingKunjungan::factory()->create([
            'karyawan_id' => $karyawan->id,
            'jabatan' => $karyawan->jabatan,
            'tanggal' => '2026-11-18',
        ]);

        $response = $this->post(route('admin.setting-kunjungan.store'), [
            'karyawan_id' => $karyawan->id,
            'tanggal' => '2026-11-18',
            'jam_aktif' => '09:00',
            'jam_non_aktif' => '16:00',
            'status' => SettingKunjungan::STATUS_AKTIF,
        ]);

        $response->assertSessionHasErrors('tanggal');

        $this->assertDatabaseCount('setting_kunjungan', 1);
    }
}
