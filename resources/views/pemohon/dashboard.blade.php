@extends('layouts.app')

@section('title', 'Dashboard Pemohon')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1">Dashboard Pemohon</h1>
            <p class="text-muted mb-0">
                Selamat datang, {{ $pemohon->nama }} &middot; NIK {{ $pemohon->nik }}.
            </p>
        </div>

        <span class="text-muted small">
            {{ now()->format('d/m/Y') }}
        </span>
    </div>

    @php
        $kartu = [
            [
                'label' => 'Total Pengajuan',
                'value' => $statistik['total'],
                'route' => route('pemohon.kunjungan.index'),
            ],
            [
                'label' => 'Menunggu',
                'value' => $statistik['menunggu'],
                'route' => route('pemohon.kunjungan.index', ['status' => 'menunggu']),
            ],
            [
                'label' => 'Disetujui',
                'value' => $statistik['disetujui'],
                'route' => route('pemohon.kunjungan.index', ['status' => 'disetujui']),
            ],
            [
                'label' => 'Ditolak',
                'value' => $statistik['ditolak'],
                'route' => route('pemohon.kunjungan.index', ['status' => 'ditolak']),
            ],
        ];
    @endphp

    <div class="row g-3 mb-4">
        @foreach ($kartu as $item)
            <div class="col-6 col-xl-3">
                <a href="{{ $item['route'] }}" class="text-decoration-none">
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

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold">Kunjungan Terdekat yang Disetujui</span>

            <a href="{{ route('pemohon.kunjungan.create') }}"
               class="btn btn-sm btn-primary">
                Ajukan Kunjungan
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Karyawan Tujuan</th>
                        <th>Keperluan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($jadwalTerdekat as $kunjungan)
                        <tr>
                            <td>
                                {{ $kunjungan->tanggal->translatedFormat('d M Y') }}
                            </td>
                            <td>{{ substr($kunjungan->jam, 0, 5) }}</td>
                            <td>
                                {{ $kunjungan->karyawan_nama }}
                                <div class="text-muted small">
                                    {{ $kunjungan->karyawan_jabatan }}
                                </div>
                            </td>
                            <td>{{ $kunjungan->keperluan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Belum ada kunjungan terjadwal.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection