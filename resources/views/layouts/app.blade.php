<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'SI Kunjung Pemda') &middot; {{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6fb;
        }

        .navbar-brand {
            letter-spacing: 0.3px;
        }

        .card {
            border: 0;
        }
    </style>

    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('home') }}">
                {{ config('app.name') }}
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Buka navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto">
                    @auth
                        @if (auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                    href="{{ route('admin.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.karyawan.*') ? 'active' : '' }}"
                                    href="{{ route('admin.karyawan.index') }}">
                                    Karyawan
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.kunjungan.*') ? 'active' : '' }}"
                                    href="{{ route('admin.kunjungan.index') }}">
                                    Pengajuan Kunjungan
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.setting-kunjungan.*') ? 'active' : '' }}"
                                    href="{{ route('admin.setting-kunjungan.index') }}">
                                    Setting Kunjungan
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.kegiatan.*') ? 'active' : '' }}"
                                    href="{{ route('admin.kegiatan.index') }}">
                                    Kegiatan
                                </a>
                            </li>

                        @elseif (auth()->user()->isKaryawan())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}"
                                    href="{{ route('karyawan.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('karyawan.kunjungan.*') ? 'active' : '' }}"
                                    href="{{ route('karyawan.kunjungan.index') }}">
                                    Kunjungan
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('karyawan.setting.*') ? 'active' : '' }}"
                                    href="{{ route('karyawan.setting.index') }}">
                                    Setting Kunjungan
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('karyawan.kegiatan.*') ? 'active' : '' }}"
                                    href="{{ route('karyawan.kegiatan.index') }}">
                                    Kegiatan
                                </a>
                            </li>

                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pemohon.dashboard') ? 'active' : '' }}"
                                    href="{{ route('pemohon.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pemohon.kunjungan.*') ? 'active' : '' }}"
                                    href="{{ route('pemohon.kunjungan.index') }}">
                                    Kunjungan Saya
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pemohon.histori.*') ? 'active' : '' }}"
                                    href="{{ route('pemohon.histori.index') }}">
                                    Histori Kunjungan
                                </a>
                            </li>
                        @endif

                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('jadwal-kunjungan') ? 'active' : '' }}"
                                href="{{ route('jadwal-kunjungan') }}">
                                Jadwal Kunjungan
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('struktur-karyawan') ? 'active' : '' }}"
                                href="{{ route('struktur-karyawan') }}">
                                Struktur Karyawan
                            </a>
                        </li>
                    @endauth
                </ul>

                <ul class="navbar-nav align-items-lg-center">
                    @auth
                        <li class="nav-item me-lg-3">
                            <span class="navbar-text small">
                                {{ auth()->user()->name }}

                                <span class="badge text-bg-light text-primary ms-1">
                                    {{ ucfirst(auth()->user()->role) }}
                                </span>
                            </span>
                        </li>

                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button type="submit" class="btn btn-outline-light btn-sm">
                                    Keluar
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item me-lg-2">
                            <a class="nav-link" href="{{ route('daftar') }}">
                                Daftar
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="btn btn-light btn-sm text-primary fw-semibold" href="{{ route('login') }}">
                                Masuk
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container pb-5">
        @include('partials.flash')
        @include('partials.errors')

        @yield('content')
    </main>

    <footer class="text-center text-muted small py-4">
        {{ config('app.name') }}
        &middot; Sistem Layanan Kunjungan Instansi Pemerintahan
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>