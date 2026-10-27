@extends('layouts.app')

@php
    $editing = $setting !== null;
@endphp

@section('title', $editing ? 'Ubah Setting Kunjungan' : 'Tambah Setting Kunjungan')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 fw-semibold">
                    {{ $editing ? 'Ubah' : 'Tambah' }} Setting Kunjungan
                </div>

                <div class="card-body">
                    <form
                        method="POST"
                        action="{{ $editing
                            ? route('admin.setting-kunjungan.update', $setting)
                            : route('admin.setting-kunjungan.store') }}"
                    >
                        @csrf

                        @if ($editing)
                            @method('PUT')
                        @endif

                        {{-- Karyawan --}}
                        <div class="mb-3">
                            <label for="karyawan_id" class="form-label">
                                Karyawan
                            </label>

                            <select
                                class="form-select @error('karyawan_id') is-invalid @enderror"
                                id="karyawan_id"
                                name="karyawan_id"
                                required
                            >
                                <option value="">Pilih karyawan...</option>

                                @foreach ($karyawans as $karyawan)
                                    <option
                                        value="{{ $karyawan->id }}"
                                        @selected((int) old('karyawan_id', $setting?->karyawan_id) === (int) $karyawan->id)
                                    >
                                        {{ $karyawan->nama }} &mdash; {{ $karyawan->jabatan }}
                                    </option>
                                @endforeach
                            </select>

                            @error('karyawan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal layanan --}}
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">
                                Tanggal Layanan
                            </label>

                            <input
                                type="date"
                                class="form-control @error('tanggal') is-invalid @enderror"
                                id="tanggal"
                                name="tanggal"
                                value="{{ old('tanggal', $setting?->tanggal?->toDateString()) }}"
                                required
                            >

                            <div class="form-text">
                                Satu karyawan hanya boleh punya satu setting per tanggal.
                            </div>

                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jam layanan --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jam_aktif" class="form-label">
                                    Jam Aktif
                                </label>

                                <input
                                    type="time"
                                    class="form-control @error('jam_aktif') is-invalid @enderror"
                                    id="jam_aktif"
                                    name="jam_aktif"
                                    value="{{ old('jam_aktif', $setting ? substr($setting->jam_aktif, 0, 5) : '') }}"
                                    required
                                >

                                @error('jam_aktif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="jam_non_aktif" class="form-label">
                                    Jam Non-Aktif
                                </label>

                                <input
                                    type="time"
                                    class="form-control @error('jam_non_aktif') is-invalid @enderror"
                                    id="jam_non_aktif"
                                    name="jam_non_aktif"
                                    value="{{ old('jam_non_aktif', $setting ? substr($setting->jam_non_aktif, 0, 5) : '') }}"
                                    required
                                >

                                <div class="form-text">
                                    Harus lebih besar dari jam aktif.
                                </div>

                                @error('jam_non_aktif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="mb-4">
                            <label for="status" class="form-label">
                                Status
                            </label>

                            <select
                                class="form-select @error('status') is-invalid @enderror"
                                id="status"
                                name="status"
                                required
                            >
                                @foreach (\App\Models\SettingKunjungan::STATUS as $nilai => $label)
                                    <option
                                        value="{{ $nilai }}"
                                        @selected(old('status', $setting?->status ?? 'Y') === $nilai)
                                    >
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="form-text">
                                Hanya setting aktif yang muncul di jadwal publik dan bisa diajukan.
                            </div>

                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tombol aksi --}}
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-save me-1"></i>
                                Simpan
                            </button>

                            <a
                                href="{{ route('admin.setting-kunjungan.index') }}"
                                class="btn btn-outline-secondary"
                            >
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
