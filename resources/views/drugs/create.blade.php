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
                    <div class="shadow-lg card">
                        <div class="text-white card-header bg-primary">
                            <h4>Tambah Obat Baru</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('data-obat.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="nama_obat" class="form-label">Nama Obat</label>
                                    <input type="text" class="form-control @error('nama_obat') is-invalid @enderror"
                                        id="nama_obat" name="nama_obat" value="{{ old('nama_obat') }}" required>
                                    @error('nama_obat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="kategori_id" class="form-label">Kategori Obat</label>
                                    <select class="form-control @error('kategori_obat') is-invalid @enderror"
                                        id="kategori_id" name="kategori_obat" required>
                                        <option value="" disabled selected>Pilih Kategori</option>
                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori->nama_kategori }}"
                                                {{ old('kategori_obat') == $kategori->nama_kategori ? 'selected' : '' }}>
                                                {{ $kategori->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kategori_obat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="golongan_id" class="form-label">Golongan Obat</label>
                                    <select class="form-control @error('golongan_obat') is-invalid @enderror" id="golongan_id" name="golongan_obat" required>
                                        <option value="" disabled selected>Pilih Golongan</option>
                                        @foreach ($golongans as $golongan)
                                            <option value="{{ $golongan->nama_golongan }}"
                                                {{ old('golongan_obat') == $golongan->nama_golongan ? 'selected' : '' }}>
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
                                        id="jenis_obat" name="jenis_obat" value="{{ old('jenis_obat') }}" required>
                                    @error('jenis_obat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="stock_minimum" class="form-label">Stok Minimum</label>
                                    <input type="number"
                                        class="form-control @error('stock_minimum') is-invalid @enderror"
                                        id="stock_minimum" name="stock_minimum" value="{{ old('stock_minimum') }}"
                                        required>
                                    @error('stock_minimum')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="satuan" class="form-label">Satuan</label>
                                    <select class="form-control @error('satuan_dasar') is-invalid @enderror"
                                        id="satuan" name="satuan_dasar" required>
                                        <option value="" disabled selected>Pilih Satuan</option>
                                        @foreach ($satuans as $satuan)
                                            <option value="{{ $satuan->nama_satuan }}">
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
                                <div id="satuan-container"></div>

                                <button type="button" class="mb-3 btn btn-secondary" onclick="tambahSatuan()">
                                    + Tambah Satuan
                                </button>

                                <button type="submit" class="btn btn-success btn-lg w-100">Simpan</button>
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
            newSatuanGroup.classList.add("row", "mb-2", "align-items-center");
            newSatuanGroup.innerHTML = `
                <div class="col-md-5">
                    <label class="form-label">Satuan</label>
                    <select name="nama_satuan[]" class="form-control satuan-select" onchange="updateKonversi(this)" required>
                        <option value="" disabled selected>Pilih Satuan</option>
                        @foreach ($satuans as $satuan)
                            <option value="{{ $satuan->nama_satuan }}" data-konversi="{{ $satuan->konversi }}">
                                {{ $satuan->nama_satuan }} ({{ $satuan->keterangan }})
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

            const jumlahInput = selectElement.parentElement.parentElement.querySelector(
                "input[name='jumlah_satuan_terkecil[]']");
            jumlahInput.value = jumlahSatuanTerkecil;
        }

        function hapusSatuan(button) {
            button.closest(".row").remove();
        }
    </script>

    <!-- Vendor JS Files -->
    <script src="{{ asset('templates/NiceAdmin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('templates/NiceAdmin/assets/js/main.js') }}"></script>
</body>

</html>
