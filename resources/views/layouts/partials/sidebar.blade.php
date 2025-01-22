<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
    <li class="nav-item">
      <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="/dashboard">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#master-data-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-database"></i><span>Master Data</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="master-data-nav" class="nav-content collapse">
        <li><a href="/data-obat"><i class="bi bi-circle"></i>Data Obat</a></li>
        <li><a href="/data-supplier"><i class="bi bi-circle"></i>Data Supplier</a></li>
        <li><a href="/data-satuan"><i class="bi bi-circle"></i>Data Satuan</a></li>
        <li><a href="/data-kategori"><i class="bi bi-circle"></i>Data Kategori</a></li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#transaksi-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-cart"></i><span>Transaksi</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="transaksi-nav" class="nav-content collapse">
        <li><a href="/pemesanan-barang"><i class="bi bi-circle"></i>Pemesanan Barang</a></li>
        <li><a href="/penerimaan-barang"><i class="bi bi-circle"></i>Penerimaan Barang</a></li>
        <li><a href="/penjualan"><i class="bi bi-circle"></i>Penjualan</a></li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#laporan-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-file-text"></i><span>Laporan</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="laporan-nav" class="nav-content collapse">
        <li><a href="/laporan-penjualan"><i class="bi bi-circle"></i>Laporan Penjualan</a></li>
        <li><a href="/laporan-stok"><i class="bi bi-circle"></i>Laporan Stok</a></li>
        <li><a href="/laporan-kedaluarsa"><i class="bi bi-circle"></i>Laporan Kedaluwarsa</a></li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#pengaturan-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-gear"></i><span>Pengaturan</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="pengaturan-nav" class="nav-content collapse">
        <li><a href="/profile"><i class="bi bi-circle"></i>Profile</a></li>
        <li><a href="/manajemen-user"><i class="bi bi-circle"></i>Manajemen User</a></li>
        <li><a href="{{ route('logout') }}"><i class="bi bi-circle"></i>Logout</a></li>
      </ul>
    </li>
  </ul>
</aside>
