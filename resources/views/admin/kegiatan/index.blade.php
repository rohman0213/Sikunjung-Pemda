@extends('layouts.app')

@section('title', 'Kegiatan Karyawan')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h1 class="h3 mb-0">Kegiatan Karyawan</h1>
        <a href="{{ route('admin.kegiatan.create') }}" class="btn btn-primary">
            Tambah Kegiatan
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('admin.kegiatan.index') }}" class="row g-2">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari nama karyawan atau deskripsi...">
                </div>

                <div class="col-md-4">
                    <select class="form-select" name="karyawan_id">
                        <option value="">Semua karyawan</option>
                        @foreach ($karyawans as $karyawan)
                            <option value="{{ $karyawan->id }}" @selected((int) request('karyawan_id') === $karyawan->id)>
                                {{ $karyawan->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-auto">
                    <button class="btn btn-outline-primary" type="submit">Cari</button>
                </div>

                <div class="col-md-auto">
                    <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px">#</th>
                        <th>Karyawan</th>
                        <th>Jabatan</th>
                        <th>Rentang Tanggal</th>
                        <th>Deskripsi</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kegiatans as $kegiatan)
                        <tr>
                            <td>{{ $kegiatans->firstItem() + $loop->index }}</td>
                            <td>{{ $kegiatan->karyawan_nama }}</td>
                            <td>{{ $kegiatan->jabatan }}</td>
                            <td>{{ $kegiatan->rentangTanggal() }}</td>
                            <td>{{ $kegiatan->deskripsi_kegiatan }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.kegiatan.edit', $kegiatan) }}" class="btn btn-sm btn-outline-warning">
                                    Ubah
                                </a>

                                <form method="POST" action="{{ route('admin.kegiatan.destroy', $kegiatan) }}" class="d-inline" onsubmit="return confirm('Hapus kegiatan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data kegiatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white">
            {{ $kegiatans->links() }}
        </div>
    </div>
@endsection
