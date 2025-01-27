


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
              <h4>Edit Obat</h4>
            </div>
            <div class="card-body">
              <!-- Form Edit Obat -->
              <form action="{{ route('data-obat.update', $drug->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                  <label for="batch" class="form-label">Batch</label>
                  <input type="text" class="form-control @error('batch') is-invalid @enderror" id="batch" name="batch" value="{{ old('batch', $drug->batch) }}" required>
                  @error('batch')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="nama_obat" class="form-label">Nama Obat</label>
                  <input type="text" class="form-control @error('nama_obat') is-invalid @enderror" id="nama_obat" name="nama_obat" value="{{ old('nama_obat', $drug->nama_obat) }}" required>
                  @error('nama_obat')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="kategori_obat" class="form-label">Kategori Obat</label>
                  <select class="form-select @error('kategori_obat') is-invalid @enderror" id="kategori_obat" name="kategori_obat" required>
                    <option value="" disabled>Pilih Kategori</option>
                    @foreach($kategoris as $kategori)
                      <option value="{{ $kategori->nama_kategori }}" {{ old('kategori_obat', $drug->kategori_obat) == $kategori->nama_kategori ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}  <!-- Pastikan field 'name' sesuai dengan nama kategori di database -->
                      </option>
                    @endforeach
                  </select>
                  @error('kategori_obat')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                

                <div class="mb-3">
                  <label for="jenis_obat" class="form-label">Jenis Obat</label>
                  <input type="text" class="form-control @error('jenis_obat') is-invalid @enderror" id="jenis_obat" name="jenis_obat" value="{{ old('jenis_obat', $drug->jenis_obat) }}" required>
                  @error('jenis_obat')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="satuan" class="form-label">Satuan</label>
                  <input type="text" class="form-control @error('satuan') is-invalid @enderror" id="satuan" name="satuan" value="{{ old('satuan', $drug->satuan) }}" required>
                  @error('satuan')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="harga_beli" class="form-label">Harga Beli</label>
                  <input type="number" step="0.01" class="form-control @error('harga_beli') is-invalid @enderror" id="harga_beli" name="harga_beli" value="{{ old('harga_beli', $drug->harga_beli) }}" required>
                  @error('harga_beli')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="harga_jual" class="form-label">Harga Jual</label>
                  <input type="number" step="0.01" class="form-control @error('harga_jual') is-invalid @enderror" id="harga_jual" name="harga_jual" value="{{ old('harga_jual', $drug->harga_jual) }}" required>
                  @error('harga_jual')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="stok" class="form-label">Stok</label>
                  <input type="number" class="form-control @error('stok') is-invalid @enderror" id="stok" name="stok" value="{{ old('stok', $drug->stok) }}" required>
                  @error('stok')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="stok_minimum" class="form-label">Stok Minimum</label>
                  <input type="number" class="form-control @error('stok_minimum') is-invalid @enderror" id="stok_minimum" name="stok_minimum" value="{{ old('stok_minimum', $drug->stok_minimum) }}" required>
                  @error('stok_minimum')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="tanggal_kadaluarsa" class="form-label">Tanggal Kadaluarsa</label>
                  <input type="date" class="form-control @error('tanggal_kadaluarsa') is-invalid @enderror" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa" value="{{ old('tanggal_kadaluarsa', $drug->tanggal_kadaluarsa->format('Y-m-d')) }}" required>
                  @error('tanggal_kadaluarsa')
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

@include('layouts.partials.footer')
