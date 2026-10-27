@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1">Dashboard Admin</h1>
            <p class="text-muted mb-0">Ringkasan layanan kunjungan.</p>
        </div>
        <span class="text-muted small">
            {{ now()->format('d/m/Y') }}
        </span>
    </div>
    @php
        $kartu = [
            [
                'label' => 'Karyawan',
                'value' => $statistik['karyawan'],
                'route' =>
                    route('admin.karyawan.index')
            ],
            [
                'label' => 'Kunjungan Hari Ini',
                'value' => $statistik['kunjungan_hari_ini'],
                'route' => route('admin.kunjungan.index')
            ],
            [
                'label' => 'Menunggu Persetujuan',
                'value' => $statistik['menunggu'],
                'route' =>
                    route('admin.kunjungan.index', ['status' => 'menunggu'])
            ],
            [
                'label' => 'Layanan Hari Ini',
                'value' => $statistik['layanan_hari_ini'],
                'route'
                => route('admin.setting-kunjungan.index')
            ],
        ];
    @endphp
    <div class="row g-3 mb-4">
        @foreach ($kartu as $item)
            <div class="col-6 col-xl-3">
                <a href="{{ $item['route'] }}" class="text-decoration-none">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="fs-4 fw-semibold text-dark">{{ $item['value'] }}</div>
                            <div class="small text-muted">{{ $item['label'] }}</div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
    <div class="card shadow-sm">

        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-
                        items-center gap-2">

            <span class="fw-semibold">Pengajuan Menunggu Persetujuan</span>
            <a href="{{ route('admin.kunjungan.index') }}" class="btn btn-sm btn-primary">
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
                        <th>Tujuan</th>
                        <th>Keperluan</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse ($menunggu as $kunjungan)
                        <tr>
                            <td>{{ $kunjungan->tanggal->translatedFormat('d M Y') }}</td>
                            <td>{{ substr($kunjungan->jam, 0, 5) }}</td>

                            <td>

                                {{ $kunjungan->pemohon_nama }}

                                <div class="text-muted small">NIK {{ $kunjungan->pemohon_nik

                                }}</div>
                            </td>
                            <td>

                                {{ $kunjungan->karyawan_nama }}

                                <div class="text-muted small">{{ $kunjungan->karyawan_jabatan

                                                                }}</div>
                            </td>

                            <td>{{ $kunjungan->keperluan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Tidak ada
                                pengajuan yang menunggu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection