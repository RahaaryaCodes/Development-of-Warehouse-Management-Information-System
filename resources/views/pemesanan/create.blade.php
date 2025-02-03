@include('layouts.partials.head')
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

<main id="main" class="main">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h4>Buat Pesanan Baru</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pemesanan-barang.store') }}" method="POST">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="surat" class="form-label">Jenis Pesanan</label>
                                    <select class="form-control" id="surat" name="surat" required>
                                        <option value="Reguler">Reguler</option>
                                        <option value="Psikotropika">Psikotropika</option>
                                        <option value="OOT">OOT</option>
                                        <option value="Prekursor">Prekursor</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="supplier" class="form-label">Supplier</label>
                                    <select class="form-control" id="supplier" name="supplier" required>
                                        <option value="" disabled selected>Pilih Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="tanggal_pemesanan" class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" id="tanggal_pemesanan" name="tanggal_pemesanan" required>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success mb-3" id="tambah-item">+ Tambah Item</button>
                            <div id="tabel-container">
                                <!-- Table will be dynamically inserted here -->
                            </div>
                            <div class="mb-3">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea class="form-control" id="catatan" name="catatan"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Simpan Pesanan</button>
                            <a href="{{ route('pemesanan-barang.index') }}" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>  
                </div>
            </div>
        </div>
    </div>
</main>

<script>
const formTemplates = {
    Reguler: `
        <table class="table table-bordered">
            <thead class="text-center">
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Banyaknya</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="item-list">
            </tbody>
        </table>
    `,
    Psikotropika: `
        <table class="table table-bordered">
            <thead class="text-center">
                <tr>
                    <th>No</th>
                    <th>Nama Psikotropika</th>
                    <th>Zat Aktif Psikotropika</th>
                    <th>Bentuk dan Kekuatan Sediaan</th>
                    <th>Satuan</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="item-list">
            </tbody>
        </table>
     `,
    OOT: `
        <table class="table table-bordered">
            <thead class="text-center">
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th>Zat Aktif</th>
                    <th>Bentuk dan Kekuatan Sediaan</th>
                    <th>Satuan</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="item-list">
            </tbody>
        </table>
    `,
    Prekursor: `
        <table class="table table-bordered">
            <thead class="text-center">
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th>Zat Aktif</th>
                    <th>Bentuk dan Kekuatan Sediaan</th>
                    <th>Satuan</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="item-list">
            </tbody>
        </table>
    `
};



const rowTemplates = {
    Reguler: (index) => `
        <tr>
            <td>${index}</td>
            <td>
                <input type="text" class="form-control" name="items[${index}][nama]" required>
            </td>
            <td><input type="number" class="form-control" name="items[${index}][jumlah]" min="1" value="1" required></td>
            <td><input type="text" class="form-control" name="items[${index}][keterangan]"></td>
            <td><button type="button" class="btn btn-danger delete-row">❌</button></td>
        </tr>
    `,
    Psikotropika: (index) => `
        <tr>
            <td>${index}</td>
            <td>
                <input type="text" class="form-control" name="items[${index}][nama]" required>
            </td>
            <td><input type="text" class="form-control" name="items[${index}][zat_aktif]" required></td>
            <td><input type="text" class="form-control" name="items[${index}][bentuk_sediaan]" required></td>
            <td><input type="text" class="form-control" name="items[${index}][satuan]" required></td>
            <td><input type="number" class="form-control" name="items[${index}][jumlah]" min="1" value="1" required></td>
            <td><button type="button" class="btn btn-danger delete-row">❌</button></td>
        </tr>
    `,
    OOT: (index) => `
        <tr>
            <td>${index}</td>
            <td>
                <input type="text" class="form-control" name="items[${index}][nama]" required>
            </td>
            <td><input type="text" class="form-control" name="items[${index}][zat_aktif]" required></td>
            <td><input type="text" class="form-control" name="items[${index}][bentuk_sediaan]" required></td>
            <td><input type="text" class="form-control" name="items[${index}][satuan]" required></td>
            <td><input type="number" class="form-control" name="items[${index}][jumlah]" min="1" value="1" required></td>
            <td><button type="button" class="btn btn-danger delete-row">❌</button></td>
        </tr>
    `,
    Prekursor: (index) => `
        <tr>
            <td>${index}</td>
            <td>
                <input type="text" class="form-control" name="items[${index}][nama]" required>
            </td>
            <td><input type="text" class="form-control" name="items[${index}][zat_aktif]" required></td>
            <td><input type="text" class="form-control" name="items[${index}][bentuk_sediaan]" required></td>
            <td><input type="text" class="form-control" name="items[${index}][satuan]" required></td>
            <td><input type="number" class="form-control" name="items[${index}][jumlah]" min="1" value="1" required></td>
            <td><button type="button" class="btn btn-danger delete-row">❌</button></td>
        </tr>
    `
};


document.addEventListener("DOMContentLoaded", function() {
    const suratSelect = document.getElementById("surat");
    const tabelContainer = document.getElementById("tabel-container");
    const tambahItemBtn = document.getElementById("tambah-item");

    // Initial form setup
    updateForm(suratSelect.value);

    // Update form when order type changes
    suratSelect.addEventListener("change", function() {
        updateForm(this.value);
    });

    // Add new item row
    tambahItemBtn.addEventListener("click", function() {
        const itemList = document.querySelector(".item-list");
        const newIndex = itemList.children.length + 1;
        const orderType = suratSelect.value;
        
        const template = document.createElement('template');
        template.innerHTML = rowTemplates[orderType](newIndex).trim();
        itemList.appendChild(template.content.firstElementChild);
    });

    // Delete row
    document.addEventListener("click", function(event) {
        if (event.target.classList.contains("delete-row")) {
            event.target.closest("tr").remove();
            renumberRows();
        }
    });

    function updateForm(orderType) {
        tabelContainer.innerHTML = formTemplates[orderType];
        // Add initial row
        const itemList = document.querySelector(".item-list");
        const template = document.createElement('template');
        template.innerHTML = rowTemplates[orderType](1).trim();
        itemList.appendChild(template.content.firstElementChild);
    }

    function renumberRows() {
        const rows = document.querySelectorAll(".item-list tr");
        rows.forEach((row, index) => {
            row.querySelector("td").textContent = index + 1;
        });
    }
});
</script>

@include('layouts.partials.footer')