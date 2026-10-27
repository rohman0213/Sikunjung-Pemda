@extends('layouts.app')

@php
    $editing = $kegiatan !== null;
@endphp

@section('title', $editing ? 'Ubah Kegiatan' : 'Tambah Kegiatan')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 fw-semibold">
                    {{ $editing ? 'Ubah' : 'Tambah' }} Kegiatan
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ $editing ? route('admin.kegiatan.update', $kegiatan) : route('admin.kegiatan.store') }}">
                        @csrf

                        @if ($editing)
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="karyawan_id" class="form-label">Karyawan</label>
                            <select class="form-select" id="karyawan_id" name="karyawan_id" required>
                                <option value="">Pilih karyawan...</option>
                                @foreach ($karyawans as $karyawan)
                                    <option value="{{ $karyawan->id }}" @selected((int) old('karyawan_id', $kegiatan?->karyawan_id) === $karyawan->id)>
                                        {{ $karyawan->nama }} &mdash; {{ $karyawan->jabatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                                <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" value="{{ old('tanggal_awal', $kegiatan?->tanggal_awal?->toDateString()) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="{{ old('tanggal_akhir', $kegiatan?->tanggal_akhir?->toDateString()) }}" required>
                                <div class="form-text">Tidak boleh lebih awal dari tanggal awal.</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="deskripsi_kegiatan" class="form-label">Deskripsi Kegiatan</label>
                            <textarea class="form-control" id="deskripsi_kegiatan" name="deskripsi_kegiatan" rows="3" required>{{ old('deskripsi_kegiatan', $kegiatan?->deskripsi_kegiatan) }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" type="submit">
                                Simpan
                            </button>
                            <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
