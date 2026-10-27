@extends('layouts.app')
@section('title', 'Setting Kunjungan')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h1 class="h3 mb-0">Setting Kunjungan</h1>
        <a href="{{ route('admin.setting-kunjungan.create') }}" class="btn btn-primary">
            Tambah Setting
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('admin.setting-kunjungan.index') }}" class="row g-2">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="q" value="{{ request('q')
        }}" placeholder="Cari karyawan atau jabatan...">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="tanggal" value="{{
        request('tanggal') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">Semua status</option>

                        @foreach (\App\Models\SettingKunjungan::STATUS as $nilai => $label)
                            <option value="{{ $nilai }}" @selected(
                                request('status') ===
                                $nilai
                            )>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-auto">
                    <button class="btn btn-outline-primary" type="submit">Cari</button>
                </div>
                <div class="col-md-auto">

                    <a href="{{ route('admin.setting-kunjungan.index') }}" class="btn btn-
        outline-secondary">Reset</a>

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
                        <th>Tanggal</th>
                        <th>Jam Layanan</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse ($settings as $setting)
                                <tr>
                                    <td>{{ $settings->firstItem() + $loop->index }}</td>
                                    <td>{{ $setting->karyawan_nama }}</td>
                                    <td>{{ $setting->jabatan }}</td>
                                    <td>{{ $setting->tanggal->translatedFormat('d M Y') }}</td>

                                    <td>{{ $setting->jamRentang() }}</td>

                                    <td>

                                        <span class="badge text-bg-{{ $setting->badgeStatus() }}">{{
                        $setting->statusLabel() }}</span>
                                    </td>

                                    <td class="text-end">

                                        <a href="{{ route('admin.setting-kunjungan.edit', $setting)
                                        }}" class="btn btn-sm btn-outline-warning">
                                            Ubah
                                        </a>

                                        <form method="POST" action="{{ route('admin.setting-kunjungan.destroy', $setting) }}"
                                            class="d-inline" onsubmit="return confirm('Hapus setting kunjungan ini?')">
                                            @csrf

                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">
                                                Hapus

                                                Modul Praktikum | Aplikasi SI Kunjung Pemda dengan Laravel

                                                Halaman 87
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada
                                setting kunjungan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $settings->links() }}
        </div>
    </div>
@endsection