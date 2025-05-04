@section('title', 'Tambah Satuan')

@include('layouts.partials.head')
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

<main class="main" id="main">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Satuan</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('data-satuan.update', $satuan->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="nama_satuan">Nama Satuan</label>
                                <input type="text" class="form-control" id="nama_satuan" name="nama_satuan"
                                    value="{{ old('nama_satuan', $satuan->nama_satuan) }}">
                            </div>

                            <div class="form-group">
                                <label for="konversi">Konversi</label>
                                <input type="text" class="form-control" id="konversi" name="konversi"
                                    value="{{ old('konversi', $satuan->konversi) }}">
                            </div>

                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan">{{ old('keterangan', $satuan->keterangan) }}</textarea>
                            </div>

                            <button type="submit" class="mt-3 btn btn-primary">Update Satuan</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.partials.footer')
