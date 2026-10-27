
@extends('layouts.guest')

@section('title', 'Beranda')

@section('content')
    {{-- Hero / Sambutan --}}
    <div class="p-4 p-md-5 mb-4 bg-primary text-white rounded-4 shadow-sm">
        <div class="py-3">
            <h1 class="display-6 fw-bold">
                SI Kunjung Pemda
            </h1>

            <p class="lead mb-4">
                Layanan kunjungan instansi pemerintahan secara daring.
                Ajukan jadwal sebelum datang, pilih karyawan tujuan,
                lalu tunggu persetujuan.
            </p>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('daftar') }}"
                   class="btn btn-light btn-lg text-primary fw-semibold">
                    Daftar sebagai Pemohon
                </a>

                <a href="{{ route('jadwal-kunjungan') }}"
                   class="btn btn-outline-light btn-lg">
                    Lihat Jadwal Kunjungan
                </a>

                <a href="{{ route('struktur-karyawan') }}"
                   class="btn btn-outline-light btn-lg">
                    Struktur Karyawan
                </a>
            </div>
        </div>
    </div>

    {{-- Empat kartu fitur --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5">Pengajuan Daring</h2>
                    <p class="text-muted mb-0">
                        Pemohon mendaftar akun lalu mengajukan kunjungan
                        dengan memilih karyawan tujuan, tanggal, dan jam layanan.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5">Persetujuan Karyawan</h2>
                    <p class="text-muted mb-0">
                        Karyawan tujuan memproses pengajuan.
                        Pengajuan dapat disetujui atau ditolak dengan catatan.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5">Jadwal dan Kegiatan</h2>
                    <p class="text-muted mb-0">
                        Informasi jadwal layanan dan kegiatan karyawan
                        membantu pemohon merencanakan kunjungan.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5">Struktur Transparan</h2>
                    <p class="text-muted mb-0">
                        Kenali nama dan jabatan karyawan instansi
                        melalui halaman struktur karyawan.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Jumlah Karyawan</div>
                    <div class="display-6 fw-bold text-primary">
                        {{ number_format($statistik['karyawan']) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Jadwal Layanan Aktif</div>
                    <div class="display-6 fw-bold text-success">
                        {{ number_format($statistik['jadwal_aktif']) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Kunjungan Disetujui</div>
                    <div class="display-6 fw-bold text-info">
                        {{ number_format($statistik['kunjungan_disetujui']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lima jadwal terdekat --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h2 class="h5 mb-0">Jadwal Terdekat</h2>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Karyawan</th>
                        <th>Jam Layanan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($jadwals as $jadwal)
                        <tr>
                            <td>
                                {{ $jadwal->tanggal->translatedFormat('d M Y') }}
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $jadwal->karyawan_nama }}
                                </div>
                                <div class="text-muted small">
                                    {{ $jadwal->jabatan }}
                                </div>
                            </td>

                            <td>
                                {{ $jadwal->jamRentang() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3"
                                class="text-center text-muted py-4">
                                Belum ada jadwal layanan aktif.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white">
            <a href="{{ route('jadwal-kunjungan') }}"
               class="small text-decoration-none">
                Lihat semua jadwal &rarr;
            </a>
        </div>
    </div>
@endsection
