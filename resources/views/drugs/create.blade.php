<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Tambah Obat - Cinta Sehat 24</title>
    
    <!-- Vendor CSS Files -->
    <link href="{{ asset('templates/NiceAdmin/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/NiceAdmin/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/NiceAdmin/assets/css/style.css') }}" rel="stylesheet">
</head>

<body>
    <!-- ======= Navbar ======= -->
    @include('layouts.partials.navbar')

    <!-- ======= Sidebar ======= -->
    @include('layouts.partials.sidebar')

    <main id="main" class="main">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-lg">
                        <div class="card-header bg-primary text-white">
                            <h4>Tambah Obat Baru</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('data-obat.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="batch" class="form-label">Batch</label>
                                    <input type="text" class="form-control" id="batch" name="batch" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nama_obat" class="form-label">Nama Obat</label>
                                    <input type="text" class="form-control" id="nama_obat" name="nama_obat" required>
                                </div>
                                <div class="mb-3">
                                    <label for="kategori_id" class="form-label">Kategori Obat</label>
                                    <select class="form-control" id="kategori_id" name="kategori_obat" required>
                                        <option value="" disabled selected>Pilih Kategori</option>
                                        @foreach($kategoris as $kategori)
                                            <option value="{{ $kategori->nama_kategori }}">{{ $kategori->nama_kategori }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="jenis_obat" class="form-label">Jenis Obat</label>
                                    <input type="text" class="form-control" id="jenis_obat" name="jenis_obat" required>
                                </div>
                                <div class="mb-3">
                                    <label for="satuan" class="form-label">Satuan</label>
                                    <input type="text" class="form-control" id="satuan" name="satuan" required>
                                </div>
                                <div class="mb-3">
                                    <label for="harga_beli" class="form-label">Harga Beli</label>
                                    <input type="number" step="0.01" class="form-control" id="harga_beli" name="harga_beli" required>
                                </div>
                                <div class="mb-3">
                                    <label for="harga_jual" class="form-label">Harga Jual</label>
                                    <input type="number" step="0.01" class="form-control" id="harga_jual" name="harga_jual" required>
                                </div>
                                <div class="mb-3">
                                    <label for="stok" class="form-label">Stok</label>
                                    <input type="number" class="form-control" id="stok" name="stok" required>
                                </div>
                                <div class="mb-3">
                                    <label for="stok_minimum" class="form-label">Stok Minimum</label>
                                    <input type="number" class="form-control" id="stok_minimum" name="stok_minimum" required>
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal_kadaluarsa" class="form-label">Tanggal Kadaluarsa</label>
                                    <input type="date" class="form-control" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa" required>
                                </div>

                                <button type="submit" class="btn btn-success btn-lg w-100">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Vendor JS Files -->
    <script src="{{ asset('templates/NiceAdmin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('templates/NiceAdmin/assets/js/main.js') }}"></script>
</body>

</html>
