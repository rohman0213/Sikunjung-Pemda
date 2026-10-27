
@extends('layouts.app')

@section('title', 'Kunjungan Saya')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h1 class="h3 mb-0">Kunjungan</h1>
        <div class="text-muted small">
            {{ $karyawan->nama }} &middot; {{ $karyawan->jabatan }}
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close"
            data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body border-bottom">
        <form method="GET"
            action="{{ route('karyawan.kunjungan.index') }}"
            class="row g-2">

            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">Semua status</option>

                    @foreach (\App\Models\Kunjungan::STATUS as $nilai => $label)
                        <option value="{{ $nilai }}"
                            @selected(request('status') === $nilai)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <input type="date" class="form-control"
                    name="tanggal" value="{{ request('tanggal') }}">
            </div>

            <div class="col-md-4">
                <input type="text" class="form-control"
                    name="q" value="{{ request('q') }}"
                    placeholder="Cari nama pemohon atau keperluan...">
            </div>

            <div class="col-md-auto">
                <button class="btn btn-outline-primary" type="submit">
                    Cari
                </button>
            </div>

            <div class="col-md-auto">
                <a href="{{ route('karyawan.kunjungan.index') }}"
                    class="btn btn-outline-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Pemohon</th>
                    <th>Keperluan</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($kunjungans as $kunjungan)
                    <tr>
                        <td>
                            {{ $kunjungans->firstItem() + $loop->index }}
                        </td>

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

                        <td>
                            <span class="badge text-bg-{{ $kunjungan->badgeStatus() }}">
                                {{ $kunjungan->statusLabel() }}
                            </span>

                            @if ($kunjungan->status === 'ditolak' && $kunjungan->catatan)
                                <div class="text-muted small mt-1">
                                    Catatan: {{ $kunjungan->catatan }}
                                </div>
                            @endif
                        </td>

                        <td class="text-end">
                            @if ($kunjungan->masihMenunggu())
                                <a href="{{ route('karyawan.kunjungan.edit', $kunjungan) }}"
                                    class="btn btn-sm btn-outline-warning">
                                    Proses
                                </a>
                            @else
                                <span class="text-muted small">
                                    Sudah diproses
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7"
                            class="text-center text-muted py-4">
                            Belum ada pengajuan kunjungan untuk Anda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-white">
        {{ $kunjungans->links() }}
    </div>
</div>
@endsection
