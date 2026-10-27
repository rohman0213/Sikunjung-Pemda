```blade
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

                    {{-- Informasi pengajuan --}}
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
                            Karyawan Tujuan
                        </dt>
                        <dd class="col-sm-8">
                            {{ $kunjungan->karyawan_nama }}

                            <div class="text-muted small">
                                {{ $kunjungan->karyawan_jabatan }}
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

                        @if ($kunjungan->catatan)
                            <dt class="col-sm-4 text-muted fw-normal">
                                Catatan Sebelumnya
                            </dt>
                            <dd class="col-sm-8">
                                {{ $kunjungan->catatan }}
                            </dd>
                        @endif

                    </dl>

                    <hr class="my-4">

                    {{-- Form proses pengajuan --}}
                    <form
                        method="POST"
                        action="{{ route('admin.kunjungan.update', $kunjungan) }}"
                    >
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="status" class="form-label">
                                Status
                            </label>

                            <select
                                class="form-select @error('status') is-invalid @enderror"
                                id="status"
                                name="status"
                                required
                            >
                                <option value="">Pilih status...</option>

                                <option
                                    value="disetujui"
                                    @selected(old('status', $kunjungan->status) === 'disetujui')
                                >
                                    Disetujui
                                </option>

                                <option
                                    value="ditolak"
                                    @selected(old('status', $kunjungan->status) === 'ditolak')
                                >
                                    Ditolak
                                </option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="catatan" class="form-label">
                                Catatan
                            </label>

                            <textarea
                                class="form-control @error('catatan') is-invalid @enderror"
                                id="catatan"
                                name="catatan"
                                rows="3"
                                placeholder="Wajib diisi bila pengajuan ditolak"
                            >{{ old('catatan', $kunjungan->catatan) }}</textarea>

                            @error('catatan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text">
                                Jika pengajuan ditolak, isi catatan alasan penolakan.
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-primary" type="submit">
                                Simpan
                            </button>

                            <a
                                href="{{ route('admin.kunjungan.index') }}"
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
```