
@extends('layouts.guest')

@section('title', 'Jadwal Kunjungan')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h1 class="h3 mb-1">Jadwal Kunjungan</h1>
            <p class="text-muted mb-0">
                Lihat jadwal layanan karyawan sebelum mengajukan kunjungan.
            </p>
        </div>

        <a href="{{ route('daftar') }}" class="btn btn-primary">
            Daftar untuk Mengajukan
        </a>
    </div>

    {{-- Form pencarian dan filter tanggal --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET"
                  action="{{ route('jadwal-kunjungan') }}"
                  class="row g-2">

                <div class="col-md-5">
                    <input
                        type="text"
                        class="form-control"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari nama karyawan atau jabatan..."
                    >
                </div>

                <div class="col-md-3">
                    <input
                        type="date"
                        class="form-control"
                        name="tanggal"
                        value="{{ request('tanggal') }}"
                    >
                </div>

                <div class="col-md-auto">
                    <button type="submit" class="btn btn-outline-primary">
                        Cari
                    </button>
                </div>

                <div class="col-md-auto">
                    <a href="{{ route('jadwal-kunjungan') }}"
                       class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Informasi jumlah hasil --}}
    <p class="text-muted small">
        Menampilkan {{ $jadwals->count() }} jadwal pada halaman ini
        dari {{ $jadwals->total() }} hasil.
    </p>

    {{-- Daftar jadwal --}}
    <div class="row g-3">
        @forelse ($jadwals as $jadwal)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge text-bg-success">
                                {{ $jadwal->statusLabel() }}
                            </span>

                            <span class="text-muted small">
                                Jadwal layanan
                            </span>
                        </div>

                        <h2 class="h5 mb-1">
                            {{ $jadwal->karyawan_nama }}
                        </h2>

                        <p class="text-muted mb-3">
                            {{ $jadwal->jabatan }}
                        </p>

                        <hr>

                        <div class="mb-2">
                            <span class="text-muted small d-block">Tanggal</span>
                            <span class="fw-semibold">
                                {{ $jadwal->tanggal->translatedFormat('d M Y') }}
                            </span>
                        </div>

                        <div>
                            <span class="text-muted small d-block">Jam Layanan</span>
                            <span class="fw-semibold">
                                {{ $jadwal->jamRentang() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center py-5">
                        <h2 class="h5">Jadwal belum tersedia</h2>
                        <p class="text-muted mb-0">
                            Belum ada jadwal layanan kunjungan yang aktif.
                            Silakan coba kata kunci atau tanggal lain.
                        </p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Navigasi halaman --}}
    @if ($jadwals->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $jadwals->links() }}
        </div>
    @endif
@endsection
