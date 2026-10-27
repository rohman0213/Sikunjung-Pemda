@extends('layouts.app')

@php
    $editing = $kunjungan !== null;
@endphp

@section('title', $editing ? 'Ubah Pengajuan Kunjungan' : 'Ajukan Kunjungan')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 fw-semibold">
                    {{ $editing ? 'Ubah' : 'Ajukan' }} Kunjungan
                </div>

                <div class="card-body">
                    <form method="POST"
                          action="{{ $editing
                              ? route('pemohon.kunjungan.update', $kunjungan)
                              : route('pemohon.kunjungan.store') }}">
                        @csrf

                        @if ($editing)
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="karyawan_id" class="form-label">
                                Karyawan Tujuan
                            </label>

                            <select class="form-select @error('karyawan_id') is-invalid @enderror"
                                    id="karyawan_id"
                                    name="karyawan_id"
                                    required>
                                <option value="">Pilih karyawan...</option>

                                @foreach ($karyawans as $karyawan)
                                    <option value="{{ $karyawan->id }}"
                                        @selected((int) old('karyawan_id', $kunjungan?->karyawan_id) === $karyawan->id)>
                                        {{ $karyawan->nama }} &mdash; {{ $karyawan->jabatan }}
                                    </option>
                                @endforeach
                            </select>

                            @error('karyawan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal Kunjungan</label>

                            <input type="date"
                                   class="form-control @error('tanggal') is-invalid @enderror"
                                   id="tanggal"
                                   name="tanggal"
                                   min="{{ now()->toDateString() }}"
                                   value="{{ old('tanggal', $kunjungan?->tanggal?->format('Y-m-d')) }}"
                                   required>

                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="jam" class="form-label">Jam Kunjungan</label>

                            <input type="time"
                                   class="form-control @error('jam') is-invalid @enderror"
                                   id="jam"
                                   name="jam"
                                   value="{{ old('jam', $kunjungan ? substr($kunjungan->jam, 0, 5) : '') }}"
                                   required>

                            @error('jam')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="keperluan" class="form-label">Keperluan</label>

                            <textarea class="form-control @error('keperluan') is-invalid @enderror"
                                      id="keperluan"
                                      name="keperluan"
                                      rows="3"
                                      maxlength="255"
                                      required>{{ old('keperluan', $kunjungan?->keperluan) }}</textarea>

                            @error('keperluan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ $editing ? 'Simpan Perubahan' : 'Kirim Pengajuan' }}
                            </button>

                            <a href="{{ route('pemohon.kunjungan.index') }}"
                               class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white py-3 fw-semibold">
                    Jadwal Layanan Aktif
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Karyawan</th>
                                <th>Jam Layanan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($settings as $setting)
                                <tr>
                                    <td>
                                        {{ $setting->tanggal->translatedFormat('d M Y') }}
                                    </td>
                                    <td>{{ $setting->karyawan_nama }}</td>
                                    <td>{{ $setting->jamRentang() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">
                                        Belum ada jadwal layanan aktif.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection