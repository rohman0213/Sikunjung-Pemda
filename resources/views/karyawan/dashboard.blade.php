
@extends('layouts.app')

@section('title', 'Dashboard Karyawan')

@section('content')
    <div class="d-flex flex-wrap justify-content-between
                align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1">Dashboard Karyawan</h1>
            <p class="text-muted mb-0">
                Selamat datang, {{ $karyawan->nama }}
                &middot; {{ $karyawan->jabatan }}.
            </p>
        </div>

        <span class="text-muted small">
            {{ now()->format('d/m/Y') }}
        </span>
    </div>

    @php
        $kartu = [
            [
                'label' => 'Menunggu',
                'value' => $statistik['menunggu'],
                'route' => route('karyawan.kunjungan.index', [
                    'status' => 'menunggu'
                ]),
            ],
            [
                'label' => 'Kunjungan Hari Ini',
                'value' => $statistik['hari_ini'],
                'route' => route('karyawan.kunjungan.index'),
            ],
            [
                'label' => 'Disetujui',
                'value' => $statistik['disetujui'],
                'route' => route('karyawan.kunjungan.index', [
                    'status' => 'disetujui'
                ]),
            ],
            [
                'label' => 'Ditolak',
                'value' => $statistik['ditolak'],
                'route' => route('karyawan.kunjungan.index', [
                    'status' => 'ditolak'
                ]),
            ],
        ];
    @endphp

    <div class="row g-3 mb-4">
        @foreach ($kartu as $item)
            <div class="col-6 col-xl-3">
                <a href="{{ $item['route'] }}"
                   class="text-decoration-none">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="fs-4 fw-semibold text-dark">
                                {{ $item['value'] }}
                            </div>
                            <div class="small text-muted">
                                {{ $item['label'] }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 fw-semibold">
                    Layanan Hari Ini
                </div>

                <div class="card-body">
                    @if ($settingHariIni)
                        <p class="mb-1">
                            Jam layanan:
                            <strong>
                                {{ $settingHariIni->jamRentang() }}
                            </strong>
                        </p>

                        <span class="badge text-bg-{{ $settingHariIni->badgeStatus() }}">
                            {{ $settingHariIni->statusLabel() }}
                        </span>
                    @else
                        <p class="text-muted mb-0">
                            Tidak ada setting layanan untuk Anda hari ini.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 fw-semibold">
                    Kegiatan Aktif
                </div>

                <ul class="list-group list-group-flush">
                    @forelse ($kegiatanAktif as $kegiatan)
                        <li class="list-group-item">
                            <div class="fw-semibold">
                                {{ $kegiatan->deskripsi_kegiatan }}
                            </div>
                            <div class="text-muted small">
                                {{ $kegiatan->rentangTanggal() }}
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-4">
                            Tidak ada kegiatan aktif saat ini.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3
                    d-flex flex-wrap justify-content-between
                    align-items-center gap-2">
            <span class="fw-semibold">
                Pengajuan Menunggu Persetujuan Anda
            </span>

            <a href="{{ route('karyawan.kunjungan.index') }}"
               class="btn btn-sm btn-primary">
                Lihat Semua
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Pemohon</th>
                        <th>Keperluan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($menunggu as $kunjungan)
                        <tr>
                            <td>
                                {{ $kunjungan->tanggal->translatedFormat('d M Y') }}
                            </td>
                            <td>{{ substr($kunjungan->jam, 0, 5) }}</td>
                            <td>
                                {{ $kunjungan->pemohon_nama }}
                                <div class="text-muted small">
                                    NIK {{ $kunjungan->pemohon_nik }}
                                </div>
                            </td>
                            <td>{{ $kunjungan->keperluan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4"
                                class="text-center text-muted py-4">
                                Tidak ada pengajuan yang menunggu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
