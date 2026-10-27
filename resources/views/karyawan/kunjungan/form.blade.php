
@extends('layouts.app')

@section('title', 'Proses Pengajuan Kunjungan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 fw-semibold">
                Proses Pengajuan Kunjungan
            </div>

            <div class="card-body">
                <dl class="row mb-4">
                    <dt class="col-sm-4 text-muted fw-normal">
                        Pemohon
                    </dt>
                    <dd class="col-sm-8">
                        {{ $kunjungan->pemohon_nama }}
                        <div class="text-muted small">
                            NIK {{ $kunjungan->pemohon_nik }}
                        </div>
                    </dd>

                    <dt class="col-sm-4 text-muted fw-normal">
                        Jadwal
                    </dt>
                    <dd class="col-sm-8">
                        {{ $kunjungan->tanggal->translatedFormat('d M Y') }},
                        {{ substr($kunjungan->jam, 0, 5) }}
                    </dd>

                    <dt class="col-sm-4 text-muted fw-normal">
                        Keperluan
                    </dt>
                    <dd class="col-sm-8">
                        {{ $kunjungan->keperluan }}
                    </dd>

                    <dt class="col-sm-4 text-muted fw-normal">
                        Status Saat Ini
                    </dt>
                    <dd class="col-sm-8">
                        <span class="badge text-bg-{{ $kunjungan->badgeStatus() }}">
                            {{ $kunjungan->statusLabel() }}
                        </span>
                    </dd>
                </dl>

                <hr class="my-4">

                <form method="POST"
                    action="{{ route('karyawan.kunjungan.update', $kunjungan) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="status" class="form-label">
                            Keputusan
                        </label>

                        <select name="status" id="status"
                            class="form-select @error('status') is-invalid @enderror"
                            required>
                            <option value="">Pilih keputusan</option>

                            <option value="disetujui"
                                @selected(old('status') === 'disetujui')>
                                Disetujui
                            </option>

                            <option value="ditolak"
                                @selected(old('status') === 'ditolak')>
                                Ditolak
                            </option>
                        </select>

                        @error('status')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label">
                            Catatan penolakan
                        </label>

                        <textarea name="catatan" id="catatan" rows="3"
                            maxlength="255"
                            class="form-control @error('catatan') is-invalid @enderror"
                            placeholder="Wajib diisi jika pengajuan ditolak">{{ old('catatan') }}</textarea>

                        @error('catatan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-text">
                            Maksimal 255 karakter. Wajib diisi jika keputusan ditolak.
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('karyawan.kunjungan.index') }}"
                            class="btn btn-outline-secondary">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Simpan Keputusan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
