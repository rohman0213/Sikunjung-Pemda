
@extends('layouts.app')

@section('title', $kegiatan ? 'Edit Kegiatan' : 'Tambah Kegiatan')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h3>{{ $kegiatan ? 'Edit Kegiatan' : 'Tambah Kegiatan' }}</h3>
        <p class="text-muted mb-0">
            Isi periode dan deskripsi kegiatan Anda.
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali data yang dimasukkan.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST"
                  action="{{ $kegiatan
                    ? route('karyawan.kegiatan.update', $kegiatan->id)
                    : route('karyawan.kegiatan.store') }}">

                @csrf

                @if ($kegiatan)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label class="form-label">Nama Karyawan</label>
                    <input type="text"
                           class="form-control"
                           value="{{ $karyawan->nama }}"
                           readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jabatan</label>
                    <input type="text"
                           class="form-control"
                           value="{{ $karyawan->jabatan }}"
                           readonly>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_awal" class="form-label">
                            Tanggal Awal
                        </label>

                        <input type="date"
                               id="tanggal_awal"
                               name="tanggal_awal"
                               class="form-control"
                               value="{{ old('tanggal_awal', $kegiatan?->tanggal_awal?->format('Y-m-d')) }}"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tanggal_akhir" class="form-label">
                            Tanggal Akhir
                        </label>

                        <input type="date"
                               id="tanggal_akhir"
                               name="tanggal_akhir"
                               class="form-control"
                               value="{{ old('tanggal_akhir', $kegiatan?->tanggal_akhir?->format('Y-m-d')) }}"
                               required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deskripsi_kegiatan" class="form-label">
                        Deskripsi Kegiatan
                    </label>

                    <textarea id="deskripsi_kegiatan"
                              name="deskripsi_kegiatan"
                              rows="5"
                              class="form-control"
                              required>{{ old('deskripsi_kegiatan', $kegiatan?->deskripsi_kegiatan) }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        {{ $kegiatan ? 'Simpan Perubahan' : 'Simpan Kegiatan' }}
                    </button>

                    <a href="{{ route('karyawan.kegiatan.index') }}"
                       class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
