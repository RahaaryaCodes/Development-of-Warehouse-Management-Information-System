@section('title', 'Konversi Satuan')

@include('layouts.partials.head')
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

<main class="main" id="main">
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Konversi Satuan</h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('konversi-satuan.update', $konversi->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="satuan_dari" class="form-label">Dari Satuan</label>
                                <select id="satuan_dari" class="form-control" name="satuan_dari" required>
                                    @foreach ($satuan as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $item->id == $konversi->satuan_dari ? 'selected' : '' }}>
                                            {{ $item->nama_satuan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="satuan_ke" class="form-label">Ke Satuan</label>
                                <select id="satuan_ke" class="form-control" name="satuan_ke" required>
                                    @foreach ($satuan as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $item->id == $konversi->satuan_ke ? 'selected' : '' }}>
                                            {{ $item->nama_satuan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Nilai Konversi</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah"
                                    value="{{ $konversi->jumlah }}" required>
                            </div>

                            <a href="{{ route('konversi-satuan.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>
