<?php
    namespace Tests\Feature;

    use App\Models\Kunjungan;
    use App\Models\Pemohon;
    use App\Models\User;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Tests\TestCase;

    class PanelPemohonTest extends TestCase
    {
    use RefreshDatabase;

    /**
     * Membuat akun dan profil pemohon.
     */
    private function buatPemohon(): User
    {
        $user = User::factory()->pemohon()->create();

        Pemohon::factory()->create([
            'user_id' => $user->id,
        ]);

        return $user;
    }

    /**
     * Pemohon dapat membuka dashboard.
     */
    public function test_pemohon_dapat_membuka_dashboard(): void
    {
        $user = $this->buatPemohon();

        $this->actingAs($user)
            ->get(route('pemohon.dashboard'))
            ->assertOk();
    }

    /**
     * Pemohon hanya melihat kunjungan miliknya sendiri.
     */
    public function test_pemohon_hanya_melihat_kunjungannya_sendiri(): void
    {
        $pemohon     = $this->buatPemohon();
        $pemohonLain = $this->buatPemohon();

        $kunjunganSendiri = Kunjungan::factory()->create([
            'pemohon_id' => $pemohon->profilPemohon()->id,
        ]);

        $kunjunganLain = Kunjungan::factory()->create([
            'pemohon_id' => $pemohonLain->profilPemohon()->id,
        ]);

        $response = $this->actingAs($pemohon)
            ->get(route('pemohon.kunjungan.index'));

        $response->assertOk()
            ->assertSee($kunjunganSendiri->tujuan)
            ->assertDontSee($kunjunganLain->tujuan);
    }

    /**
     * Pemohon dapat membuka formulir kunjungan.
     *
     * Tidak membuat SettingKunjungan dengan kolom "aktif",
     * karena kolom tersebut tidak tersedia di database proyek.
     */
    public function test_pemohon_dapat_membuka_form_kunjungan(): void
    {
        $user = $this->buatPemohon();

        $this->actingAs($user)
            ->get(route('pemohon.kunjungan.create'))
            ->assertOk();
    }

    /**
     * Pemohon dapat membuka histori kunjungan.
     */
    public function test_pemohon_dapat_membuka_histori_kunjungan(): void
    {
        $user = $this->buatPemohon();

        $this->actingAs($user)
            ->get(route('pemohon.histori.index'))
            ->assertOk();
    }
}
