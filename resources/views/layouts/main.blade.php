<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.partials.head') <!-- Mengambil bagian head dari partial -->
</head>
<body>
    <!-- Navbar -->
    @include('layouts.partials.navbar')
    <!-- End Navbar -->

    <div class="d-flex">
        <!-- Sidebar -->
        @include('layouts.partials.sidebar')
        <!-- End Sidebar -->

        <!-- Main Content -->
        <div class="flex-grow-1">
            <main id="main" class="main">
                <!-- Page Title -->
                <div class="pagetitle">
                    @if(Auth::check())
                        <p>Selamat datang, {{ Auth::user()->name }}!</p>
                    @endif

                    <!-- Konten Tambahan untuk Role Owner -->
                    @auth
                        @if(auth()->user()->isOwner())
                            <div class="admin-menu">
                                <h2>Menu Admin</h2>
                                <ul>
                                    <li><a href="{{ route('users.index') }}">Kelola Users</a></li>
                                    <li><a href="{{ route('settings.index') }}">Pengaturan Sistem</a></li>
                                </ul>
                            </div>
                        @endif
                    @endauth
                </div>
                <!-- End Page Title -->

                <!-- Konten Halaman -->
                @yield('content')
                <!-- End Konten Halaman -->
            </main>
        </div>
        <!-- End Main Content -->
    </div>

    <!-- Footer -->
    @include('layouts.partials.footer')
    <!-- End Footer -->

    <!-- Scripts -->
    @stack('scripts') <!-- Stack untuk menambahkan script spesifik dari halaman -->
</body>
</html>
