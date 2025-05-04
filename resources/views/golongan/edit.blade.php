@section('title', 'Edit Golongan Obat')

@include('layouts.partials.head')

@include('layouts.partials.navbar')

@include('layouts.partials.sidebar')

<main id="main" class="main">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Edit Golongan Obat</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('data-golongan.update', $golongan->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="nama_golongan" class="form-label">Nama Golongan</label>
                                <input type="text" class="form-control @error('nama_golongan') is-invalid @enderror" 
                                       id="nama_golongan" name="nama_golongan" 
                                       value="{{ old('nama_golongan', $golongan->nama_golongan) }}">
                                @error('nama_golongan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                          id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $golongan->keterangan) }}</textarea>
                                @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('data-golongan.index') }}" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.partials.footer')
