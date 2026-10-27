
@extends('layouts.app')

@section('title', 'Kegiatan Saya')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>Kegiatan Saya</h3>
            <p class="text-muted mb-0">
                Kelola daftar kegiatan dan agenda pekerjaan Anda.
            </p>
        </div>

        <a href="{{ route('karyawan.kegiatan.create') }}"
           class="btn btn-primary">
            + Tambah Kegiatan
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET"
                  action="{{ route('karyawan.kegiatan.index') }}"
                  class="row g-2">
                <div class="col-md-9">
                    <input type="text"
                           name="q"
                           class="form-control"
                           placeholder="Cari deskripsi kegiatan..."
                           value="{{ request('q') }}">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        Cari
                    </button>

                    <a href="{{ route('karyawan.kegiatan.index') }}"
                       class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px">No.</th>
                            <th>Jabatan</th>
                            <th>Periode Kegiatan</th>
                            <th>Deskripsi Kegiatan</th>
                            <th style="width: 160px">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($kegiatans as $kegiatan)
                            <tr>
                                <td>
                                    {{ $kegiatans->firstItem() + $loop->index }}
                                </td>

                                <td>{{ $kegiatan->jabatan }}</td>

                                <td>
                                    {{ $kegiatan->rentangTanggal() }}
                                </td>

                                <td>
                                    {{ $kegiatan->deskripsi_kegiatan }}
                                </td>

                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('karyawan.kegiatan.edit', $kegiatan->id) }}"
                                           class="btn btn-sm btn-warning">
                                            Edit
                                        </a>

                                        <form method="POST"
                                              action="{{ route('karyawan.kegiatan.destroy', $kegiatan->id) }}"
                                              onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    Belum ada data kegiatan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $kegiatans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
