@extends('layouts.app')

@section('title', 'Kunjungan Saya')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h1 class="h3 mb-0">Kunjungan Saya</h1>

        <a href="{{ route('pemohon.kunjungan.create') }}" class="btn btn-primary">
            Ajukan Kunjungan
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body border-bottom">
            <form method="GET"
                  action="{{ route('pemohon.kunjungan.index') }}"
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

                <div class="col-md-auto">
                    <button class="btn btn-outline-primary" type="submit">
                        Terapkan
                    </button>
                </div>

                <div class="col-md-auto">
                    <a href="{{ route('pemohon.kunjungan.index') }}"
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
                        <th>Karyawan Tujuan</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
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

                                @if ($kunjungan->status === 'ditolak' && $kunjungan->catatan)
                                    <div class="text-muted small mt-1">
                                        Catatan: {{ $kunjungan->catatan }}
                                    </div>
                                @endif
                            </td>

                            <td class="text-end">
                                @if ($kunjungan->masihMenunggu())
                                    <a href="{{ route('pemohon.kunjungan.edit', $kunjungan) }}"
                                       class="btn btn-sm btn-outline-warning">
                                        Ubah
                                    </a>

                                    <form method="POST"
                                          action="{{ route('pemohon.kunjungan.destroy', $kunjungan) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Hapus pengajuan kunjungan ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-outline-danger" type="submit">
                                            Hapus
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">Sudah diproses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada pengajuan kunjungan.
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