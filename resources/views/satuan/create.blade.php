@section('title', 'Tambah Satuan')

@include('layouts.partials.head')
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

<main id="main" class="main">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Tambah Satuan</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('data-satuan.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nama_satuan" class="form-label">Nama Satuan</label>
                                <input type="text" class="form-control @error('nama_satuan') is-invalid @enderror"
                                    id="nama_satuan" name="nama_satuan" value="{{ old('nama_satuan') }}" required>
                                @error('nama_satuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="konversi" class="form-label">Konversi</label>
                                <input type="text" class="form-control @error('konversi') is-invalid @enderror"
                                    id="konversi" name="konversi" value="{{ old('konversi') }}" required>
                                @error('konversi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan"
                                    rows="4">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('data-satuan.index') }}" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.partials.footer')
