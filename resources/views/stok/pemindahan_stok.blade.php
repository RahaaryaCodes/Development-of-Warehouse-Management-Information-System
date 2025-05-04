@section('title', 'Daftar Obat')

@include('layouts.partials.head')
<style>
    th {
        white-space: nowrap;
    }
</style>
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

<div id="main" class="main">
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="mb-4 card-header">
                <h5 class="card-title">Pemindahan Stok Obat</h5>
            </div>

            <div class="card-body">
                <form action="{{ route('pemindahan-stok.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="obat_id" class="form-label">Pilih Obat</label>
                        <select class="form-select" name="obat_id" id="obat_id" required>
                            <option value="">-- Pilih Obat --</option>
                            @foreach ($drugs as $drug)
                                <option value="{{ $drug->id }}">{{ $drug->nama_obat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="batch" class="form-label">Pilih Batch</label>
                        <select class="form-select" name="batch" id="batch" required disabled>
                            <option value="">-- Pilih Batch --</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" name="amount" id="amount"
                            placeholder="Pilih batch terlebih dahulu" min="1" required disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Arah Pemindahan</label>
                        <select class="form-select" name="direction" required>
                            <option value="gudang_to_etalase">Gudang → Etalase</option>
                            <option value="etalase_to_gudang">Etalase → Gudang</option>
                            <option value="gudang_to_cabang">Gudang → Apotik Cabang</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Pindahkan Stok</button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('layouts.partials.footer')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const obatSelect = document.getElementById('obat_id');
        const batchSelect = document.getElementById('batch');
        const amountInput = document.getElementById('amount');
        const directionSelect = document.querySelector('[name="direction"]');

        // Reset Form
        function resetForm() {
            batchSelect.innerHTML = '<option value="">-- Pilih Batch --</option>';
            batchSelect.disabled = true;
            amountInput.value = "";
            amountInput.placeholder = "Pilih batch terlebih dahulu";
            amountInput.disabled = true;
        }

        // Ambil batch berdasarkan obat_id yang dipilih
        obatSelect.addEventListener('change', function() {
            const obatId = obatSelect.value;
            resetForm();

            if (!obatId) return;

            fetch(`/batches/${obatId}`)
                .then(response => response.json())
                .then(batches => {
                    batchSelect.disabled = false;
                    batches.forEach(batch => {
                        const konversiSatuanId = batch
                            .konversi_satuan_id;
                        const konversi = batch.drug?.konversi_satuan || [];

                        const konversiTerpilih = konversi.find(k => k.id ===
                            konversiSatuanId);
                        const jumlahSatuanTerkecil = konversiTerpilih ? konversiTerpilih
                            .jumlah_satuan_terkecil : 1;

                        const option = document.createElement('option');
                        option.value = batch.batch;
                        option.setAttribute('data-stok-gudang', batch.stok_gudang);
                        option.setAttribute('data-stok-etalase', batch.stok_etalase);
                        option.setAttribute('data-konversi',
                            jumlahSatuanTerkecil);
                        option.textContent =
                            `${batch.batch} (Exp: ${batch.tanggal_kadaluarsa})`;
                        batchSelect.appendChild(option);
                    });
                });
        });

        // Update Maksimum Stok
        function updateMaxAmount() {
            const selectedBatch = batchSelect.options[batchSelect.selectedIndex];
            if (!selectedBatch || !selectedBatch.value) return;

            const stokGudang = parseInt(selectedBatch.getAttribute('data-stok-gudang')) || 0;
            const stokEtalase = parseInt(selectedBatch.getAttribute('data-stok-etalase')) || 0;
            const konversiSatuan = parseInt(selectedBatch.getAttribute('data-konversi')) ||
                1;

            let maxStok = 0;

            if (directionSelect.value === 'gudang_to_etalase') {
                maxStok = Math.floor(stokGudang / konversiSatuan);
            } else if (directionSelect.value === 'etalase_to_gudang') {
                maxStok = Math.floor(stokEtalase / konversiSatuan);
            } else if (directionSelect.value === 'gudang_to_cabang') {
                maxStok = Math.floor(stokGudang / konversiSatuan);
            }

            amountInput.max = maxStok;
            amountInput.placeholder = `Maksimal: ${maxStok}`;
            amountInput.value = "";
            amountInput.disabled = maxStok === 0;
        }

        // Validasi jumlah yang diinput
        amountInput.addEventListener('input', function() {
            let maxValue = parseInt(amountInput.max) || 0;
            if (parseInt(amountInput.value) > maxValue) {
                amountInput.value = maxValue;
            }
        });

        // Event Listener
        batchSelect.addEventListener('change', function() {
            amountInput.disabled = false;
            updateMaxAmount();
        });
        directionSelect.addEventListener('change', updateMaxAmount);
    });
</script>
