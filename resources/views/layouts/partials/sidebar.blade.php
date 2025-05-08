<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="/dashboard">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::is('data-*') ? '' : 'collapsed' }}" data-bs-target="#master-data-nav"
                data-bs-toggle="collapse" href="#">
                <i class="bi bi-database"></i><span>Master Data</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="master-data-nav" class="nav-content collapse {{ Request::is('data-*') ? 'show' : '' }}">
                <li><a href="/data-obat" class="{{ Request::is('data-obat') ? 'active' : '' }}"><i
                            class="bi bi-circle"></i>Data Obat</a></li>
                <li><a href="/data-supplier" class="{{ Request::is('data-supplier') ? 'active' : '' }}"><i
                            class="bi bi-circle"></i>Data Supplier</a></li>
                <li><a href="/data-satuan" class="{{ Request::is('data-satuan') ? 'active' : '' }}"><i
                            class="bi bi-circle"></i>Data Satuan</a></li>
                <li><a href="/data-kategori" class="{{ Request::is('data-kategori') ? 'active' : '' }}"><i
                            class="bi bi-circle"></i>Data Kategori</a></li>
                <li><a href="/data-golongan" class="{{ Request::is('data-golongan') ? 'active' : '' }}"><i
                            class="bi bi-circle"></i>Data Golongan</a></li>
            </ul>
        </li>
        <!-- Stok Obat -->
        <li class="nav-item">
            <a class="nav-link {{ Request::is('stok-*') ? '' : 'collapsed' }}" data-bs-target="#stok-nav"
                data-bs-toggle="collapse" href="#">
                <i class="bi bi-box"></i><span>Stok Obat</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="stok-nav" class="nav-content collapse {{ Request::is('stok-*') ? 'show' : '' }}">
                <li><a href="/stok-gudang" class="{{ Request::is('stok-gudang') ? 'active' : '' }}"><i
                            class="bi bi-circle"></i>Stok Gudang</a></li>
                <li><a href="/stok-etalase" class="{{ Request::is('stok-etalase') ? 'active' : '' }}"><i
                            class="bi bi-circle"></i>Stok Etalase</a></li>
                <li><a href="/pemindahan-stok" class="{{ Request::is('pemindahan-stok') ? 'active' : '' }}"><i
                            class="bi bi-circle"></i>Pemindahan Stok</a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::is('pemesanan-barang', 'penerimaan-barang', 'penjualan') ? '' : 'collapsed' }}"
                data-bs-target="#transaksi-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-cart"></i><span>Transaksi</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="transaksi-nav"
                class="nav-content collapse {{ Request::is('pemesanan-barang', 'penerimaan-barang', 'penjualan') ? 'show' : '' }}">
                <li><a href="/pemesanan-barang" class="{{ Request::is('pemesanan-barang') ? 'active' : '' }}"><i
                            class="bi bi-circle"></i>Pemesanan Barang</a></li>
                <li><a href="/penjualan" class="{{ Request::is('penjualan') ? 'active' : '' }}"><i
                            class="bi bi-circle"></i>Penjualan</a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::is('laporan-*') ? '' : 'collapsed' }}" data-bs-target="#laporan-nav"
                data-bs-toggle="collapse" href="#">
                <i class="bi bi-file-text"></i><span>Laporan</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="laporan-nav" class="nav-content collapse {{ Request::is('laporan-*') ? 'show' : '' }}">
                <li><a href="/laporan-penjualan" class="{{ Request::is('laporan-penjualan') ? 'active' : '' }}"><i
                            class="bi bi-circle"></i>Laporan Penjualan</a></li>
                <li><a href="/laporan-stok" class="{{ Request::is('laporan-stok') ? 'active' : '' }}"><i
                            class="bi bi-circle"></i>Laporan Stok</a></li>
                <li><a href="/laporan-kadaluarsa" class="{{ Request::is('laporan-kadaluarsa') ? 'active' : '' }}"><i
                            class="bi bi-circle"></i>Laporan Kedaluwarsa</a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::is('profile', 'manajemen-user', 'logout') ? '' : 'collapsed' }}"
                data-bs-target="#pengaturan-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-gear"></i><span>Pengaturan</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="pengaturan-nav"
                class="nav-content collapse {{ Request::is('profile', 'manajemen-user', 'logout') ? 'show' : '' }}">
                <li><a href="/profile" class="{{ Request::is('profile') ? 'active' : '' }}"><i
                            class="bi bi-circle"></i>Profile</a></li>
                {{-- <li><a href="/manajemen-user" class="{{ Request::is('manajemen-user') ? 'active' : '' }}"><i
                            class="bi bi-circle"></i>Manajemen User</a></li> --}}
                <li><a href="{{ route('logout') }}" class="{{ Request::is('logout') ? 'active' : '' }}"><i
                            class="bi bi-circle"></i>Logout</a></li>
            </ul>
        </li>
    </ul>
</aside>
