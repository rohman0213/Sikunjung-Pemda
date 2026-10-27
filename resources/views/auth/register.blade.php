@extends('layouts.guest')
@section('title', 'Daftar Pemohon')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 mb-1 text-center">Daftar Akun Pemohon</h1>
                    <p class="text-muted text-center small mb-4">Isi data diri Anda. Data NIK
                        dipakai untuk pengajuan kunjungan.</p>
                    <form method="POST" action="{{ route('daftar.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required
                                autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="nik" class="form-label">NIK</label>

                            <input type="text" class="form-control" id="nik" name="nik" value="{{ old('nik') }}"
                                inputmode="numeric" maxlength="16" required>
                            <div class="form-text">16 angka sesuai KTP.</div>
                        </div>
                        <div class="mb-3">
                            <label for="no_telepon" class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" id="no_telepon" name="no_telepon"
                                value="{{ old('no_telepon') }}" placeholder="Opsional">
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>

                            <textarea class="form-control" id="alamat" name="alamat" rows="2"
                                placeholder="Opsional">{{ old('alamat') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>

                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                                required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">Minimal 8 karakter.</div>
                            </div>

                            <div class="col-md-6 mb-4">

                                <label for="password_confirmation" class="form-label">Ulangi
                                    Password</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            Daftar
                        </button>
                    </form>
                </div>
            </div>
            <p class="text-center mt-3 mb-0">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
            </p>
        </div>
    </div>
@endsection