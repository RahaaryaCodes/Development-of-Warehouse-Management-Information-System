   <!-- ======= Header ======= -->
   @include('layouts.partials.head')

    <!-- ======= Navbar ======= -->
    @include('layouts.partials.navbar')

    <!-- ======= Sidebar ======= -->
    @include('layouts.partials.sidebar')

    <main id="main" class="main">
      <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Tambah Kategori</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('data-kategori.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nama_kategori" class="form-label">Nama Kategori</label>
                                <input type="text" class="form-control @error('nama_kategori') is-invalid @enderror" 
                                       id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori') }}">
                                @error('nama_kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
    
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                          id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
    
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('data-kategori.index') }}" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main>
   <!-- ======= Footer ======= -->
   @include('layouts.partials.footer')
