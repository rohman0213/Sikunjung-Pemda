
@extends('layouts.app')

@php
    $editing = $karyawan !== null;
@endphp

@section('title', $editing ? 'Ubah Karyawan' : 'Tambah Karyawan')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 fw-semibold">
                    {{ $editing ? 'Ubah' : 'Tambah' }} Karyawan
                </div>

                <div class="card-body">
                    <form
                        method="POST"
                        action="{{ $editing
                            ? route('admin.karyawan.update', $karyawan)
                            : route('admin.karyawan.store') }}"
                    >
                        @csrf

                        @if ($editing)
                            @method('PUT')
                        @endif

                        {{-- NIK --}}
                        <div class="mb-3">
                            <label for="nik" class="form-label">NIK</label>
                            <input
                                type="text"
                                class="form-control @error('nik') is-invalid @enderror"
                                id="nik"
                                name="nik"
                                value="{{ old('nik', $karyawan?->nik) }}"
                                maxlength="16"
                                inputmode="numeric"
                                required
                            >
                            @error('nik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nama --}}
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input
                                type="text"
                                class="form-control @error('nama') is-invalid @enderror"
                                id="nama"
                                name="nama"
                                value="{{ old('nama', $karyawan?->nama) }}"
                                required
                            >
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jabatan --}}
                        <div class="mb-3">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input
                                type="text"
                                class="form-control @error('jabatan') is-invalid @enderror"
                                id="jabatan"
                                name="jabatan"
                                value="{{ old('jabatan', $karyawan?->jabatan) }}"
                                required
                            >
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nomor telepon --}}
                        <div class="mb-3">
                            <label for="no_telepon" class="form-label">
                                Nomor Telepon
                            </label>
                            <input
                                type="text"
                                class="form-control @error('no_telepon') is-invalid @enderror"
                                id="no_telepon"
                                name="no_telepon"
                                value="{{ old('no_telepon', $karyawan?->no_telepon) }}"
                                maxlength="20"
                            >
                            @error('no_telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email akun --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email Akun
                            </label>
                            <input
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email', $karyawan?->emailAkun()) }}"
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                Password {{ $editing ? '(Opsional)' : '' }}
                            </label>
                            <input
                                type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                id="password"
                                name="password"
                                {{ $editing ? '' : 'required' }}
                                minlength="8"
                                autocomplete="new-password"
                            >
                            @if ($editing)
                                <div class="form-text">
                                    Kosongkan jika tidak ingin mengubah password.
                                </div>
                            @else
                                <div class="form-text">
                                    Password minimal 8 karakter.
                                </div>
                            @endif
                            @error('password')
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
                                href="{{ route('admin.karyawan.index') }}"
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
