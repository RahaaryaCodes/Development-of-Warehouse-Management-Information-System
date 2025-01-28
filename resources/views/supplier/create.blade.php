    @section('title', 'Tambah Data Supplier')

    <!-- ======= Header ======= -->
    @include('layouts.partials.head')
    
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
                            <h4>Tambah Supplier Baru</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('data-supplier.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="nama_supplier" class="form-label">Nama Supplier</label>
                                    <input type="text" class="form-control @error('nama_supplier') is-invalid @enderror" id="nama_supplier" name="nama_supplier" required>
                                    @error('nama_supplier')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <input type="text" class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" required>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="telepon" class="form-label">Telepon</label>
                                    <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" required>
                                    @error('telepon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">Keterangan</label>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                

                                <button type="submit" class="btn btn-primary btn-lg w-100">Simpan</button>
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
