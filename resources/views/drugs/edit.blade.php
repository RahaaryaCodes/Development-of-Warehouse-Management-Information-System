@include('layouts.partials.head')

<!-- ======= Navbar ======= -->
@include('layouts.partials.navbar')

<!-- ======= Sidebar ======= -->
@include('layouts.partials.sidebar')

<main id="main" class="main">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="shadow-lg card">
                    <div class="text-white card-header bg-primary">
                        <h4>Edit Obat</h4>
                    </div>
                    <div class="card-body">
                        <!-- Form Edit Obat -->
                        <form action="{{ route('data-obat.update', $drug->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="nama_obat" class="form-label">Nama Obat</label>
                                <input type="text" class="form-control @error('nama_obat') is-invalid @enderror"
                                    id="nama_obat" name="nama_obat" value="{{ old('nama_obat', $drug->nama_obat) }}"
                                    required>
                                @error('nama_obat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="kategori_obat" class="form-label">Kategori Obat</label>
                                <select class="form-select @error('kategori_obat') is-invalid @enderror"
                                    id="kategori_obat" name="kategori_obat" required>
                                    <option value="" disabled>Pilih Kategori</option>
                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->nama_kategori }}"
                                            {{ old('kategori_obat', $drug->kategori_obat) == $kategori->nama_kategori ? 'selected' : '' }}>
                                            {{ $kategori->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori_obat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="golongan_obat" class="form-label">Golongan Obat</label>
                                <select class="form-select @error('golongan_obat') is-invalid @enderror"
                                    id="golongan_obat" name="golongan_obat" required>
                                    <option value="" disabled>Pilih Golongan</option>
                                    @foreach ($golongans as $golongan)
                                        <option value="{{ $golongan->nama_golongan }}"
                                            {{ old('golongan_obat', $drug->golongan_obat) == $golongan->nama_golongan ? 'selected' : '' }}>
                                            {{ $golongan->nama_golongan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('golongan_obat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            

                            <div class="mb-3">
                                <label for="jenis_obat" class="form-label">Jenis Obat</label>
                                <input type="text" class="form-control @error('jenis_obat') is-invalid @enderror"
                                    id="jenis_obat" name="jenis_obat" value="{{ old('jenis_obat', $drug->jenis_obat) }}"
                                    required>
                                @error('jenis_obat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="stock_minimum" class="form-label">Stok Minimum</label>
                                <input type="number" class="form-control @error('stock_minimum') is-invalid @enderror"
                                    id="stock_minimum" name="stock_minimum"
                                    value="{{ old('stock_minimum', $drug->stock_minimum) }}" required>
                                @error('stock_minimum')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="satuan" class="form-label">Satuan</label>
                                <select class="form-control @error('satuan_dasar') is-invalid @enderror" id="satuan" name="satuan_dasar" required>
                                    <option value="" disabled>Pilih Satuan</option>
                                    @foreach ($satuans as $satuan)
                                        <option value="{{ $satuan->nama_satuan }}" 
                                            {{ old('satuan_dasar', $drug->satuan_dasar) == $satuan->nama_satuan ? 'selected' : '' }}>
                                            {{ $satuan->nama_satuan }} - {{ $satuan->keterangan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('satuan_dasar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            

                            <hr>
                            <h5 class="mb-3">Tambahkan Satuan</h5>
                            <div id="satuan-container">
                                @foreach ($konversis as $konversi)
                                    <div class="mb-2 row align-items-center satuan-row">
                                        <div class="col-md-5">
                                            <label class="form-label">Satuan</label>
                                            <select name="nama_satuan[]" class="form-control satuan-select"
                                                onchange="updateKonversi(this)" required>
                                                <option value="" disabled>Pilih Satuan</option>
                                                  
                                            @foreach ($satuans as $satuan)
                                            <option value="{{ $satuan->nama_satuan }}"
                                                {{ old('satuan_dasar', $drug->satuan_dasar ?? '') == $satuan->nama_satuan ? 'selected' : '' }}>
                                                {{ $satuan->nama_satuan }} - {{ $satuan->keterangan }}
                                            </option>

                                            @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label">Jumlah dalam Satuan Terkecil</label>
                                            <input type="number" name="jumlah_satuan_terkecil[]" class="form-control"
                                                value="{{ $konversi->jumlah_satuan_terkecil }}" required readonly>
                                        </div>
                                        <div class="text-center col-md-2">
                                            <button type="button" class="mt-4 btn btn-danger"
                                                onclick="hapusSatuan(this)">Hapus</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="mb-3 btn btn-secondary" onclick="tambahSatuan()">
                                + Tambah Satuan
                            </button>

                            <button type="submit" class="btn btn-primary btn-lg w-100">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    function tambahSatuan() {
        const satuanContainer = document.getElementById("satuan-container");

        const newSatuanGroup = document.createElement("div");
        newSatuanGroup.classList.add("row", "mb-2", "align-items-center", "satuan-row");
        newSatuanGroup.innerHTML = `
            <div class="col-md-5">
                <label class="form-label">Satuan</label>
                <select name="nama_satuan[]" class="form-control satuan-select" onchange="updateKonversi(this)" required>
                    <option value="" disabled selected>Pilih Satuan</option>
                    @foreach ($satuans as $satuan)
                        <option value="{{ $satuan->nama_satuan }}" data-konversi="{{ $satuan->konversi }}">
                            {{ $satuan->nama_satuan }} - {{ $satuan->keterangan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Jumlah dalam Satuan Terkecil</label>
                <input type="number" name="jumlah_satuan_terkecil[]" class="form-control" placeholder="0" required readonly>
            </div>
            <div class="text-center col-md-2">
                <button type="button" class="mt-4 btn btn-danger" onclick="hapusSatuan(this)">Hapus</button>
            </div>
        `;


        satuanContainer.appendChild(newSatuanGroup);
    }

    function updateKonversi(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const jumlahSatuanTerkecil = selectedOption.getAttribute("data-konversi");

        const jumlahInput = selectElement.closest(".row").querySelector(
            "input[name='jumlah_satuan_terkecil[]']");
        jumlahInput.value = jumlahSatuanTerkecil;
    }

    function hapusSatuan(button) {
        button.closest(".satuan-row").remove();
    }
</script>

@include('layouts.partials.footer')
