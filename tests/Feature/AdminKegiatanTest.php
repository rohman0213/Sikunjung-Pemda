<?php
    namespace Tests\Feature;

    use App\Models\Karyawan;
    use App\Models\Kegiatan;
    use App\Models\User;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Tests\TestCase;

    class AdminKegiatanTest extends TestCase
    {
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    private function karyawan(): Karyawan
    {
        return Karyawan::factory()->create();
    }

    public function test_admin_bisa_membuka_daftar_dan_form_kegiatan(): void
    {
        $this->actingAs($this->admin());

        $this->get(route('admin.kegiatan.index'))->assertOk();
        $this->get(route('admin.kegiatan.create'))->assertOk();
    }

    public function test_admin_bisa_menambahkan_kegiatan(): void
    {
        $this->actingAs($this->admin());

        $karyawan = $this->karyawan();

        $response = $this->post(route('admin.kegiatan.store'), [
            'karyawan_id'        => $karyawan->id,
            'tanggal_awal'       => '2026-11-01',
            'tanggal_akhir'      => '2026-11-03',
            'deskripsi_kegiatan' => 'Rapat koordinasi internal',
        ]);

        $response->assertRedirect(route('admin.kegiatan.index'));

        $this->assertDatabaseHas('kegiatan', [
            'karyawan_id'        => $karyawan->id,
            'jabatan'            => $karyawan->jabatan,
            'tanggal_awal'       => '2026-11-01',
            'tanggal_akhir'      => '2026-11-03',
            'deskripsi_kegiatan' => 'Rapat koordinasi internal',
        ]);
    }

    public function test_admin_bisa_mengedit_kegiatan(): void
    {
        $this->actingAs($this->admin());

        $karyawan = $this->karyawan();

        $kegiatan = Kegiatan::factory()->create([
            'karyawan_id' => $karyawan->id,
            'jabatan'     => $karyawan->jabatan,
        ]);

        $this->get(route('admin.kegiatan.edit', $kegiatan))
            ->assertOk();

        $response = $this->put(
            route('admin.kegiatan.update', $kegiatan),
            [
                'karyawan_id'        => $karyawan->id,
                'tanggal_awal'       => '2026-11-05',
                'tanggal_akhir'      => '2026-11-07',
                'deskripsi_kegiatan' => 'Rapat koordinasi terbaru',
            ]
        );

        $response->assertRedirect(route('admin.kegiatan.index'));

        $this->assertDatabaseHas('kegiatan', [
            'id'                 => $kegiatan->id,
            'tanggal_awal'       => '2026-11-05',
            'tanggal_akhir'      => '2026-11-07',
            'deskripsi_kegiatan' => 'Rapat koordinasi terbaru',
        ]);
    }

    public function test_admin_bisa_menghapus_kegiatan(): void
    {
        $this->actingAs($this->admin());

        $karyawan = $this->karyawan();

        $kegiatan = Kegiatan::factory()->create([
            'karyawan_id' => $karyawan->id,
            'jabatan'     => $karyawan->jabatan,
        ]);

        $response = $this->delete(
            route('admin.kegiatan.destroy', $kegiatan)
        );

        $response->assertRedirect(route('admin.kegiatan.index'));

        $this->assertDatabaseMissing('kegiatan', [
            'id' => $kegiatan->id,
        ]);
    }

    public function test_tanggal_akhir_kegiatan_tidak_boleh_sebelum_tanggal_awal(): void
    {
        $this->actingAs($this->admin());

        $karyawan = $this->karyawan();

        $response = $this->post(route('admin.kegiatan.store'), [
            'karyawan_id'        => $karyawan->id,
            'tanggal_awal'       => '2026-11-10',
            'tanggal_akhir'      => '2026-11-09',
            'deskripsi_kegiatan' => 'Kegiatan dengan tanggal tidak valid',
        ]);

        $response->assertSessionHasErrors('tanggal_akhir');

        $this->assertDatabaseMissing('kegiatan', [
            'deskripsi_kegiatan' => 'Kegiatan dengan tanggal tidak valid',
        ]);
    }
}
