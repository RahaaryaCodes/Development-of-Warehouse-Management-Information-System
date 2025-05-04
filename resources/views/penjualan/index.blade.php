@section('title', 'Daftar Obat')

@include('layouts.partials.head')
<style>
    th {
        white-space: nowrap;
    }
</style>
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

<main id="main" class="main">
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="mb-4 card-header">
                <h5 class="card-title">Penjualan Obat</h5>
            </div>

            <div class="card-body">
                <form action="{{ route('penjualan.store') }}" method="POST">
                    @csrf
                    <div id="penjualan-container">
                        <div class="mb-3 penjualan-item">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="obat_id" class="form-label">Pilih Obat</label>
                                    <select class="form-select obat_id" name="obat_id[]" required>
                                        <option value="">-- Pilih Obat --</option>
                                        @foreach ($drugs as $drug)
                                            <option value="{{ $drug->id }}">{{ $drug->nama_obat }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="satuan" class="form-label">Pilih Satuan</label>
                                    <select class="form-select satuan" name="satuan[]" required disabled>
                                        <option value="">-- Pilih Satuan --</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="jumlah" class="form-label">Jumlah</label>
                                    <input type="number" class="form-control jumlah" name="jumlah[]" min="1"
                                        required>
                                </div>
                                <div class="col-md-2">
                                    <label for="harga" class="form-label">Harga Jual</label>
                                    <input type="number" class="form-control harga_jual" name="harga_jual[]"
                                        min="1" required>
                                </div>

                            </div>

                            <div class="mt-2 col-md-2">
                                <button type="button" class="btn btn-danger remove-item">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        <h4>Total Harga:
                            <span id="total-harga" class="px-3 py-2 badge bg-success fs-4">0</span>
                        </h4>
                    </div>

                    <button type="button" class="btn btn-success" id="add-item">Tambah Item</button>
                    <button type="submit" class="btn btn-primary">Jual Obat</button>
                </form>
            </div>
        </div>
    </div>
</main>

@include('layouts.partials.footer')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Event listener untuk menambahkan item
        document.getElementById('add-item').addEventListener('click', function() {
            const container = document.getElementById('penjualan-container');
            const newItem = container.firstElementChild.cloneNode(true);
            container.appendChild(newItem);

            // Reset nilai input
            newItem.querySelector('.obat_id').value = "";
            newItem.querySelector('.satuan').innerHTML = '<option value="">-- Pilih Satuan --</option>';
            newItem.querySelector('.satuan').disabled = true;
            newItem.querySelector('.jumlah').value = "";
            newItem.querySelector('.harga_jual').value = "";
        });

        // Event listener untuk menghapus item
        document.getElementById('penjualan-container').addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-item')) {
                const items = document.querySelectorAll('.penjualan-item');
                if (items.length > 1) {
                    event.target.closest('.penjualan-item').remove();
                }
            }
        });

        // Event listener untuk mengganti satuan berdasarkan obat yang dipilih
        document.getElementById('penjualan-container').addEventListener('change', function(event) {
            if (event.target.classList.contains('obat_id')) {
                const obatId = event.target.value;
                const satuanSelect = event.target.closest('.penjualan-item').querySelector('.satuan');

                satuanSelect.innerHTML = '<option value="">Loading...</option>';
                satuanSelect.disabled = true;

                if (!obatId) {
                    satuanSelect.innerHTML = '<option value="">-- Pilih Satuan --</option>';
                    return;
                }

                fetch(`/get-satuan/${obatId}`)
                    .then(response => response.json())
                    .then(satuans => {
                        satuanSelect.innerHTML = '<option value="">-- Pilih Satuan --</option>';
                        if (satuans.length > 0) {
                            satuans.forEach(satuan => {
                                const option = document.createElement('option');
                                option.value = satuan.id;
                                option.textContent = satuan.nama_satuan;
                                satuanSelect.appendChild(option);
                            });
                            satuanSelect.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching satuan:", error);
                        satuanSelect.innerHTML = '<option value="">-- Pilih Satuan --</option>';
                    });
            }
        });

        function updateTotalHarga() {
            let totalHarga = 0;

            document.querySelectorAll('.penjualan-item').forEach(item => {
                let jumlah = item.querySelector('.jumlah').value || 0;
                let hargaJual = item.querySelector('.harga_jual').value || 0;
                totalHarga += jumlah * hargaJual;
            });

            document.getElementById('total-harga').textContent = totalHarga.toLocaleString('id-ID');
        }

        // Event listener untuk setiap perubahan input jumlah atau harga jual
        document.getElementById('penjualan-container').addEventListener('input', function(event) {
            if (event.target.classList.contains('jumlah') || event.target.classList.contains(
                    'harga_jual')) {
                updateTotalHarga();
            }
        });

        // Update total harga saat item dihapus
        document.getElementById('penjualan-container').addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-item')) {
                setTimeout(updateTotalHarga, 100);
            }
        });

        // Update total harga saat item baru ditambahkan
        document.getElementById('add-item').addEventListener('click', function() {
            setTimeout(updateTotalHarga, 100);
        });
    });
</script>
