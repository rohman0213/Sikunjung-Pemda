@extends('layouts.app')
@section('title', 'Kelola Karyawan')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h1 class="h3 mb-0">Karyawan</h1>
        <a href="{{ route('admin.karyawan.create') }}" class="btn btn-primary">
            Tambah Karyawan
        </a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('admin.karyawan.index') }}" class="row g-2">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="q" value="{{ request('q')
        }}" placeholder="Cari NIK, nama, atau jabatan...">
                </div>
                <div class="col-md-auto">
                    <button class="btn btn-outline-primary" type="submit">Cari</button>
                </div>
                <div class="col-md-auto">

                    <a href="{{ route('admin.karyawan.index') }}" class="btn btn-outline-
        secondary">Reset</a>

                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px">#</th>

                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>No. Telepon</th>
                        <th>Akun</th>
                        <th>Kunjungan</th>
                        <th class="text-end">Aksi</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse ($karyawans as $karyawan)
                                    <tr>
                                        <td>{{ $karyawans->firstItem() + $loop->index }}</td>
                                        <td>{{ $karyawan->nik }}</td>
                                        <td>{{ $karyawan->nama }}</td>
                                        <td>{{ $karyawan->jabatan }}</td>
                                        <td>{{ $karyawan->no_telepon ?? '-' }}</td>
                                        <td>{{ $karyawan->user_email ?? 'Tanpa akun' }}</td>

                                        <td>{{ $karyawan->kunjungan_count }}</td>
                                        <td class="text-end">

                                            <a href="{{ route('admin.karyawan.edit', $karyawan) }}" class="btn btn-sm btn-outline-warning">
                                                Ubah
                                            </a>

                                            <form method="POST" action="{{ route(
                            'admin.karyawan.destroy',

                            $karyawan
                        ) }}" class="d-inline" onsubmit="return confirm('Hapus karyawan ini beserta
                                            akunnya?')">
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
                            <td colspan="8" class="text-center text-muted py-4">Belum ada data
                                karyawan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $karyawans->links() }}
        </div>
    </div>
@endsection