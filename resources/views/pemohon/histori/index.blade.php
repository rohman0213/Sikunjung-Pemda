@extends('layouts.app')

@section('title', 'Histori Kunjungan')

@section('content')
    <div class="mb-3">
        <h1 class="h3 mb-0">Histori Kunjungan</h1>
        <p class="text-muted mb-0">
            Catatan seluruh kunjungan Anda, termasuk yang sudah diproses.
        </p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body border-bottom">
            <form method="GET"
                  action="{{ route('pemohon.histori.index') }}"
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
                    <input type="date"
                           class="form-control"
                           name="dari"
                           value="{{ request('dari') }}"
                           aria-label="Dari tanggal">
                </div>

                <div class="col-md-3">
                    <input type="date"
                           class="form-control"
                           name="sampai"
                           value="{{ request('sampai') }}"
                           aria-label="Sampai tanggal">
                </div>

                <div class="col-md-auto">
                    <button class="btn btn-outline-primary" type="submit">
                        Cari
                    </button>
                </div>

                <div class="col-md-auto">
                    <a href="{{ route('pemohon.histori.index') }}"
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
                        <th style="width: 60px">#</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Tujuan</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($kunjungans as $kunjungan)
                        <tr>
                            <td>{{ $kunjungans->firstItem() + $loop->index }}</td>

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

                            <td>
                                <span class="badge text-bg-{{ $kunjungan->badgeStatus() }}">
                                    {{ $kunjungan->statusLabel() }}
                                </span>
                            </td>

                            <td class="text-muted">
                                {{ $kunjungan->catatan ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada histori kunjungan.
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