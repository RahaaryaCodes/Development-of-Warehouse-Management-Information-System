@section('title', 'Tambah Konversi Satuan')

@include('layouts.partials.head')

<!-- Navbar -->
@include('layouts.partials.navbar')

<!-- Sidebar -->
@include('layouts.partials.sidebar')

<main id="main" class="main">
    <div class="container">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header">
                            <h4>Tambah Konversi Satuan</h4>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('konversi-satuan.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="satuan_dari" class="form-label">Dari Satuan</label>
                                    <select name="satuan_dari" id="satuan_dari" class="form-control">
                                        <option value="">Pilih Satuan</option>
                                        @foreach ($satuan as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->nama_satuan }}
                                                @php echo $item->keterangan ? "({$item->keterangan})" : '' @endphp
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="satuan_ke" class="form-label">Ke Satuan</label>
                                    <select name="satuan_ke" id="satuan_ke" class="form-control">
                                        <option value="">Pilih Satuan</option>
                                        @foreach ($satuan as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->nama_satuan }}
                                                @php echo $item->keterangan ? "({$item->keterangan})" : '' @endphp
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="jumlah" class="form-label">Nilai Konversi</label>
                                    <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                                </div>

                                <a href="{{ route('konversi-satuan.index') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.partials.footer')
