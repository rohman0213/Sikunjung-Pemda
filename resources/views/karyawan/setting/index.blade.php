
@extends('layouts.app')

@section('title', 'Pengaturan Jadwal Kunjungan')

@section('content')
<div class="mb-3">
    <h1 class="h3 mb-1">Pengaturan Jadwal Kunjungan</h1>
    <div class="text-muted small">
        {{ $karyawan->nama }} &middot; {{ $karyawan->jabatan }}
    </div>
</div>

<div class="alert alert-info">
    Halaman ini hanya untuk melihat jadwal layanan Anda.
    Perubahan jadwal dilakukan oleh admin.
</div>

<div class="card shadow-sm">
    <div class="card-body border-bottom">
        <form method="GET"
            action="{{ route('karyawan.setting.index') }}"
            class="row g-2">

            <div class="col-md-4">
                <label for="tanggal" class="form-label">
                    Tanggal
                </label>
                <input type="date"
                    id="tanggal"
                    name="tanggal"
                    class="form-control"
                    value="{{ request('tanggal') }}">
            </div>

            <div class="col-md-4">
                <label for="status" class="form-label">
                    Status layanan
                </label>
                <select id="status" name="status" class="form-select">
                    <option value="">Semua status</option>

                    @foreach (\App\Models\SettingKunjungan::STATUS as $nilai => $label)
                        <option value="{{ $nilai }}"
                            @selected(request('status') === (string) $nilai)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-auto align-self-end">
                <button type="submit" class="btn btn-outline-primary">
                    Cari
                </button>
                <a href="{{ route('karyawan.setting.index') }}"
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
                    <th>Jam Layanan</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($settings as $setting)
                    <tr>
                        <td>
                            {{ $settings->firstItem() + $loop->index }}
                        </td>

                        <td>
                            {{ $setting->tanggal->translatedFormat('d M Y') }}
                        </td>

                        <td>{{ $setting->jamRentang() }}</td>

                        <td>
                            <span class="badge text-bg-{{ $setting->badgeStatus() }}">
                                {{ $setting->statusLabel() }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4"
                            class="text-center text-muted py-4">
                            Belum ada pengaturan jadwal untuk Anda.
                        </td>
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
