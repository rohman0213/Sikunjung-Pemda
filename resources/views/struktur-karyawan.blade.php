
@extends('layouts.guest')

@section('title', 'Struktur Karyawan')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1">Struktur Karyawan</h1>
        <p class="text-muted mb-0">
            Daftar karyawan instansi beserta jabatan masing-masing.
        </p>
    </div>

    @php
        $perJabatan = $karyawans->groupBy('jabatan');
    @endphp

    @forelse ($perJabatan as $jabatan => $daftar)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0">{{ $jabatan ?: 'Jabatan belum ditentukan' }}</h2>
                    <span class="badge text-bg-primary">
                        {{ $daftar->count() }} orang
                    </span>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-4">
                    @foreach ($daftar as $karyawan)
                        <div class="col-sm-6 col-lg-4">
                            <div class="d-flex align-items-center gap-3">
                                <div
                                    class="rounded-circle bg-primary-subtle text-primary
                                           d-flex align-items-center justify-content-center
                                           fw-bold flex-shrink-0"
                                    style="width: 48px; height: 48px;"
                                >
                                    {{ strtoupper(substr($karyawan->nama, 0, 1)) }}
                                </div>

                                <div class="min-w-0">
                                    <div class="fw-semibold">
                                        {{ $karyawan->nama }}
                                    </div>

                                    <div class="text-muted small">
                                        NIK: {{ $karyawan->nik }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @empty
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <h2 class="h5">Belum ada data karyawan</h2>
                <p class="text-muted mb-0">
                    Data karyawan akan ditampilkan di halaman ini
                    setelah tersedia di database.
                </p>
            </div>
        </div>
    @endforelse
@endsection
