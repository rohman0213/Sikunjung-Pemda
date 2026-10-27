<?php
    namespace Database\Seeders;

    use App\Models\Karyawan;
    use App\Models\Kegiatan;
    use App\Models\Kunjungan;
    use App\Models\Pemohon;
    use App\Models\SettingKunjungan;
    use App\Models\User;
    use Illuminate\Database\Seeder;
    use Illuminate\Support\Carbon;

    class DemoSeeder extends Seeder
    {
    /**
     * Seed data contoh: karyawan, pemohon, setting layanan,
     * kegiatan, dan kunjungan.
     */
    public function run(): void
    {
        $karyawans = $this->seedKaryawan();
        $pemohons  = $this->seedPemohon();

        $this->seedSettingKunjungan($karyawans);
        $this->seedKegiatan($karyawans);
        $this->seedKunjungan($pemohons, $karyawans);
    }

    /**
     * @return array<int, Karyawan>
     */
    private function seedKaryawan(): array
    {
        $data = [
            [
                'nik'        => '3273010101010001',
                'nama'       => 'Agus Pratama',
                'jabatan'    => 'Kepala Dinas',
                'no_telepon' => '081234567001',
                'email'      => null,
            ],
            [
                'nik'        => '3273010101010002',
                'nama'       => 'Rina Kartika',
                'jabatan'    => 'Sekretaris Dinas',
                'no_telepon' => '081234567002',
                'email'      => 'rina@sikunjung.test',
            ],
            [
                'nik'        => '3273010101010003',
                'nama'       => 'Budi Santoso',
                'jabatan'    => 'Kepala Bidang Pelayanan',
                'no_telepon' => '081234567003',
                'email'      => 'budi@sikunjung.test',
            ],
            [
                'nik'        => '3273010101010004',
                'nama'       => 'Sari Dewi',
                'jabatan'    => 'Kepala Bidang Umum',
                'no_telepon' => '081234567004',
                'email'      => 'sari@sikunjung.test',
            ],
            [
                'nik'        => '3273010101010005',
                'nama'       => 'Maya Lestari',
                'jabatan'    => 'Staf Pelayanan',
                'no_telepon' => '081234567005',
                'email'      => 'maya@sikunjung.test',
            ],
            [
                'nik'        => '3273010101010006',
                'nama'       => 'Fajar Nugroho',
                'jabatan'    => 'Staf Umum',
                'no_telepon' => '081234567006',
                'email'      => null,
            ],
        ];

        $hasil = [];

        foreach ($data as $item) {
            $userId = null;

            if ($item['email'] !== null) {
                $user = User::updateOrCreate(
                    ['email' => $item['email']],
                    [
                        'name'     => $item['nama'],
                        'password' => 'password',
                        'role'     => User::ROLE_KARYAWAN,
                    ],
                );

                $userId = $user->id;
            }

            $hasil[] = Karyawan::updateOrCreate(
                ['nik' => $item['nik']],
                [
                    'user_id'    => $userId,
                    'nama'       => $item['nama'],
                    'jabatan'    => $item['jabatan'],
                    'no_telepon' => $item['no_telepon'],
                ],
            );
        }

        return $hasil;
    }

    /**
     * @return array<int, Pemohon>
     */
    private function seedPemohon(): array
    {
        $data = [
            [
                'nik'        => '3273020202020001',
                'nama'       => 'Dedi Kurniawan',
                'no_telepon' => '082134567001',
                'alamat'     => 'Jl. Merdeka No. 10, Bandung',
                'email'      => 'dedi@sikunjung.test',
            ],
            [
                'nik'        => '3273020202020002',
                'nama'       => 'Putri Handayani',
                'no_telepon' => '082134567002',
                'alamat'     => 'Jl. Sudirman No. 25, Bandung',
                'email'      => 'putri@sikunjung.test',
            ],
        ];

        $hasil = [];

        foreach ($data as $item) {
            $user = User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'name'     => $item['nama'],
                    'password' => 'password',
                    'role'     => User::ROLE_PEMOHON,
                ],
            );

            $hasil[] = Pemohon::updateOrCreate(
                ['nik' => $item['nik']],
                [
                    'user_id'    => $user->id,
                    'nama'       => $item['nama'],
                    'no_telepon' => $item['no_telepon'],
                    'alamat'     => $item['alamat'],
                ],
            );
        }

        return $hasil;
    }

    /**
     * Setting layanan untuk 14 hari mulai hari ini.
     *
     * @param array<int, Karyawan> $karyawans
     */
    private function seedSettingKunjungan(array $karyawans): void
    {
        foreach ($karyawans as $karyawan) {
            for ($hari = 0; $hari < 14; $hari++) {
                $tanggal    = now()->addDays($hari);
                $akhirPekan = $tanggal->isWeekend();

                SettingKunjungan::updateOrCreate(
                    [
                        'karyawan_id' => $karyawan->id,
                        'tanggal'     => $tanggal->toDateString(),
                    ],
                    [
                        'jabatan'       => $karyawan->jabatan,
                        'jam_aktif'     => '08:00',
                        'jam_non_aktif' => '14:00',
                        'status'        => $akhirPekan
                            ? SettingKunjungan::STATUS_NONAKTIF
                            : SettingKunjungan::STATUS_AKTIF,
                    ],
                );
            }
        }
    }

    /**
     * Satu kegiatan selesai dan satu sedang berjalan per karyawan.
     *
     * @param array<int, Karyawan> $karyawans
     */
    private function seedKegiatan(array $karyawans): void
    {
        $deskripsi = [
            'Rapat koordinasi bulanan',
            'Verifikasi berkas permohonan',
            'Pendampingan layanan publik',
            'Sosialisasi program instansi',
            'Evaluasi kinerja mingguan',
            'Kunjungan lapangan',
        ];

        foreach ($karyawans as $index => $karyawan) {
            $selesai = [
                'tanggal_awal'       => now()->subDays(10)->toDateString(),
                'tanggal_akhir'      => now()->subDays(6)->toDateString(),
                'deskripsi_kegiatan' =>
                $deskripsi[$index % count($deskripsi)],
            ];

            $berjalan = [
                'tanggal_awal'       => now()->subDay()->toDateString(),
                'tanggal_akhir'      => now()->addDays(4)->toDateString(),
                'deskripsi_kegiatan' =>
                $deskripsi[($index + 1) % count($deskripsi)],
            ];

            foreach ([$selesai, $berjalan] as $kegiatan) {
                Kegiatan::updateOrCreate(
                    [
                        'karyawan_id'        => $karyawan->id,
                        'tanggal_awal'       => $kegiatan['tanggal_awal'],
                        'deskripsi_kegiatan' =>
                        $kegiatan['deskripsi_kegiatan'],
                    ],
                    [
                        'jabatan'       => $karyawan->jabatan,
                        'tanggal_akhir' => $kegiatan['tanggal_akhir'],
                    ],
                );
            }
        }
    }

    /**
     * Kunjungan contoh: campuran status masa lalu dan masa depan.
     *
     * @param array<int, Pemohon> $pemohons
     * @param array<int, Karyawan> $karyawans
     */
    private function seedKunjungan(
        array $pemohons,
        array $karyawans
    ): void {
        $jams = ['08:00', '09:00', '10:00', '11:00', '13:00'];

        $keperluan = [
            'Konsultasi izin usaha',
            'Pengurusan surat keterangan domisili',
            'Koordinasi program bantuan sosial',
            'Pengaduan layanan publik',
            'Permohonan data statistik',
            'Audiensi organisasi masyarakat',
        ];

        for ($i = 0; $i < 20; $i++) {
            $pemohon    = $pemohons[$i % count($pemohons)];
            $karyawan   = $karyawans[$i % count($karyawans)];
            $offsetHari = ($i % 10) - 5;
            $tanggal    = Carbon::now()->addDays($offsetHari);

            if ($offsetHari < 0) {
                $status = $i % 7 === 3
                    ? 'ditolak'
                    : 'disetujui';
            } else {
                $status = $i % 3 === 0
                    ? 'disetujui'
                    : 'menunggu';
            }

            Kunjungan::updateOrCreate(
                [
                    'pemohon_id'  => $pemohon->id,
                    'karyawan_id' => $karyawan->id,
                    'tanggal'     => $tanggal->toDateString(),
                    'jam'         => $jams[$i % count($jams)],
                ],
                [
                    'keperluan' =>
                    $keperluan[$i % count($keperluan)],
                    'status'    => $status,
                    'catatan'   => $status === 'ditolak'
                        ? 'Jadwal bentrok dengan agenda dinas.'
                        : null,
                ],
            );
        }
    }
}
