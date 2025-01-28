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
                                    <input type="text" class="form-control @error('batch') is-invalid @enderror" id="batch" name="batch" value="{{ old('batch') }}" required>
                                    @error('batch')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="nama_obat" class="form-label">Nama Obat</label>
                                    <input type="text" class="form-control @error('nama_obat') is-invalid @enderror" id="nama_obat" name="nama_obat" value="{{ old('nama_obat') }}" required>
                                    @error('nama_obat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="kategori_id" class="form-label">Kategori Obat</label>
                                    <select class="form-control @error('kategori_obat') is-invalid @enderror" id="kategori_id" name="kategori_obat" required>
                                        <option value="" disabled selected>Pilih Kategori</option>
                                        @foreach($kategoris as $kategori)
                                            <option value="{{ $kategori->nama_kategori }}" {{ old('kategori_obat') == $kategori->nama_kategori ? 'selected' : '' }}>
                                                {{ $kategori->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kategori_obat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="jenis_obat" class="form-label">Jenis Obat</label>
                                    <input type="text" class="form-control @error('jenis_obat') is-invalid @enderror" id="jenis_obat" name="jenis_obat" value="{{ old('jenis_obat') }}" required>
                                    @error('jenis_obat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="satuan" class="form-label">Satuan</label>
                                    <select class="form-control @error('satuan') is-invalid @enderror" id="satuan" name="satuan" required>
                                        <option value="" disabled selected>Pilih Satuan</option>
                                        @foreach($satuans as $satuan)
                                            <option value="{{ $satuan->nama_satuan }}" {{ old('satuan') == $satuan->nama_satuan ? 'selected' : '' }}>
                                                {{ $satuan->nama_satuan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('satuan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="harga_beli" class="form-label">Harga Beli</label>
                                    <input type="number" step="0.01" class="form-control @error('harga_beli') is-invalid @enderror" id="harga_beli" name="harga_beli" value="{{ old('harga_beli') }}" required>
                                    @error('harga_beli')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="harga_jual" class="form-label">Harga Jual</label>
                                    <input type="number" step="0.01" class="form-control @error('harga_jual') is-invalid @enderror" id="harga_jual" name="harga_jual" value="{{ old('harga_jual') }}" required>
                                    @error('harga_jual')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="stok" class="form-label">Stok</label>
                                    <input type="number" class="form-control @error('stok') is-invalid @enderror" id="stok" name="stok" value="{{ old('stok') }}" required>
                                    @error('stok')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="stok_minimum" class="form-label">Stok Minimum</label>
                                    <input type="number" class="form-control @error('stok_minimum') is-invalid @enderror" id="stok_minimum" name="stok_minimum" value="{{ old('stok_minimum') }}" required>
                                    @error('stok_minimum')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal_kadaluarsa" class="form-label">Tanggal Kadaluarsa</label>
                                    <input type="date" class="form-control @error('tanggal_kadaluarsa') is-invalid @enderror" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa" value="{{ old('tanggal_kadaluarsa') }}" required>
                                    @error('tanggal_kadaluarsa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
