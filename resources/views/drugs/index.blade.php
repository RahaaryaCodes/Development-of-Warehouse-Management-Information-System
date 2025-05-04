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
        <!-- Search Section -->
        <form action="{{ route('data-obat.index') }}" method="GET">
            <div class="row mb-4">
                <!-- Input Pencarian -->
                <div class="col-lg-2 col-md-6 mb-2">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari obat..." name="search"
                            value="{{ request('search') }}">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                    </div>
                </div>
        
                <!-- Filter Kategori -->
                <div class="col-lg-3 col-md-6 mb-2">
                    <select id="kategoriFilter" class="form-select" name="kategori">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                                {{ $kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
        
                <!-- Filter Golongan -->
                <div class="col-lg-3 col-md-6 mb-2">
                    <select id="golonganFilter" class="form-select" name="golongan">
                        <option value="">Semua Golongan</option>
                        @foreach ($golongans as $golongan)
                            <option value="{{ $golongan }}" 
                                {{ request('golongan') == $golongan ? 'selected' : '' }}>
                                {{ $golongan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-2">
                <!-- Tombol Import & Tambah -->
                <div class="text-md-end text-center">
                    <button id="openImportModal" class="btn btn-success me-2" type="button">
                        <i class="bi bi-upload me-1"></i> Import
                    </button>
                    <button id="openExportModal" class="btn btn-info me-2" type="button">
                        <i class="bi bi-download me-1"></i> Export
                    </button>
                    <a href="{{ route('data-obat.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah
                    </a>
                </div>
            </div>
            </div>
        </form>
        

        <!-- Table Section -->
        <div class="table">
            <div class="table-responsive">
                <table class="table align-middle table-hover table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Obat</th>
                            <th>Kategori</th>
                            <th>Jenis</th>
                            <th>Golongan</th>
                            <th>Satuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="drugsTableBody">
                        <!-- Data akan dimuat melalui AJAX -->
                    </tbody>
                </table>

                <!-- Pagination -->
                <div id="pagination" class="mt-3 d-flex justify-content-center">
                    <!-- Pagination buttons akan dimuat melalui AJAX -->
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Modal Import Excel -->
<div id="importModal" class="modal fade" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Data Obat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="importForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div>
                        <label for="file" class="form-label">Pilih File Excel</label>
                        <input type="file" id="file" name="file" class="form-control" required
                            accept=".xlsx,.xls">
                    </div>
                    <div class="mb-3">
                        <a href="{{ route('download.template') }}" class="btn text-primary" download>
                            <i class="bi bi-download"></i> Download Template Excel
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Export Excel -->
<div id="exportModal" class="modal fade" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Data Obat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exportForm" method="GET" action="{{ route('export.excel') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih format:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="format" id="formatExcel" value="xlsx" checked>
                            <label class="form-check-label" for="formatExcel">
                                Excel (.xlsx)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="format" id="formatCsv" value="csv">
                            <label class="form-check-label" for="formatCsv">
                                CSV (.csv)
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="useFilters" name="use_filters" value="1">
                            <label class="form-check-label" for="useFilters">
                                Gunakan filter yang aktif saat ini
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data obat ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Hapus</button>
            </div>
        </div>
    </div>
</div>

@include('layouts.partials.footer')

<script>
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 1000, // Waktu dalam milidetik
            showConfirmButton: false
        });
    @endif

    @if (session('added'))
        Swal.fire({
            icon: 'success',
            title: 'Data Berhasil Ditambahkan!',
            text: '{{ session('added') }}',
            timer: 1000, // Waktu dalam milidetik
            showConfirmButton: false
        });
    @endif

    // Helper untuk format harga
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    // Helper untuk format tanggal
    function formatDate(dateString) {
        const options = {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    }

    // Fungsi untuk memuat data tabel dengan AJAX
    function fetchDrugs(page = 1) {
    const searchInput = $('#searchInput').val(); // Ambil nilai dari input pencarian
    const kategoriFilter = $('#kategoriFilter').val(); // Ambil nilai dari filter kategori
    const golonganFilter = $('#golonganFilter').val(); // Ambil nilai dari filter golongan  

    $.ajax({
        url: "{{ route('data-obat.index') }}", // Ubah URL ke rute index
        type: "GET",
        data: {
            search: searchInput, // Kirim nilai pencarian ke server
            kategori: kategoriFilter, // Kirim nilai filter kategori
            golongan: golonganFilter, // Kirim nilai filter golongan
            page: page // Kirim nomor halaman
        },
        success: function(response) {

            if (response.data && response.data.length > 0) {
                let rows = '';
                $.each(response.data, function(index, drug) {
                    rows += `
                        <tr>
                            <td>${index + 1 + (page - 1) * 10}</td>
                            <td>${drug.nama_obat}</td>
                            <td>${drug.kategori_obat}</td>
                            <td>${drug.jenis_obat}</td>
                            <td>${drug.golongan_obat}</td>
                            <td>${drug.satuan_dasar} - ${drug.satuan.keterangan}</td> <!-- Menambahkan keterangan satuan -->
                            <td>
                                <div class="gap-1 d-flex">
                                    <a href="/data-obat/${drug.id}/edit" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="openDeleteModal(${drug.id})">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>`;
                });
                $('#drugsTableBody').html(rows);

                // Update pagination buttons
                let paginationButtons = `<div class="btn-group" role="group">`;

                if (response.pagination.prev_page_url) {
                    paginationButtons += `
                        <button class="btn btn-outline-primary" onclick="fetchDrugs(${page - 1})">
                            <i class="bi bi-arrow-left"></i> Prev
                        </button>`;
                }

                for (let i = 1; i <= response.pagination.last_page; i++) {
                    paginationButtons += `
                        <button class="btn ${i === page ? 'btn-primary' : 'btn-outline-primary'}" onclick="fetchDrugs(${i})">
                            ${i}
                        </button>`;
                }

                if (response.pagination.next_page_url) {
                    paginationButtons += `
                        <button class="btn btn-outline-primary" onclick="fetchDrugs(${page + 1})">
                            Next <i class="bi bi-arrow-right"></i>
                        </button>`;
                }

                paginationButtons += `</div>`;
                $('#pagination').html(paginationButtons);

                // Update URL tanpa reload halaman
                history.pushState(null, '',
                `?search=${searchInput}&kategori=${kategoriFilter}&golongan=${golonganFilter}&page=${page}`);

                
            } else {
                $('#drugsTableBody').html(
                    '<tr><td colspan="11" class="text-center">Data tidak ditemukan</td></tr>');
                $('#pagination').html('');
            }
        }
    });
}


    // Fungsi untuk membuka modal delete
    let drugIdToDelete;

    function openDeleteModal(drugId) {
        drugIdToDelete = drugId;
        $('#deleteModal').modal('show');
    }

    // Fungsi untuk menghapus data obat
    $('#confirmDeleteButton').on('click', function() {
        $.ajax({
            url: `/data-obat/${drugIdToDelete}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}', // Pastikan token CSRF ada
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                Swal.fire('Berhasil!', response.message, 'success');
                fetchDrugs(); // Panggil ulang fetchDrugs untuk memperbarui tabel
            }
        });

    });

    // Inisialisasi tabel saat halaman dimuat
    $(document).ready(function() {
    fetchDrugs();

    $('#searchInput').on('keyup', function() {
        fetchDrugs();
    });

    $('#kategoriFilter').on('change', function() {
        fetchDrugs(1);
    });

    // Add this event listener
    $('#golonganFilter').on('change', function() {
        fetchDrugs(1);
    });
});

    document.addEventListener("DOMContentLoaded", function() {
        let importModal = new bootstrap.Modal(document.getElementById('importModal'));

        document.getElementById("openImportModal").addEventListener("click", function() {
            importModal.show();
        });

        document.getElementById("importForm").addEventListener("submit", function(event) {
            event.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('import.excel') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire('Berhasil!', 'Data obat berhasil diimport!', 'success');
                    $('#importModal').modal('hide');
                    fetchDrugs();
                },
                error: function(xhr) {
    let errorMessage = 'Gagal mengimpor data!';

    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
        errorMessage = xhr.responseJSON.errors.join('<br>');
    } else if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMessage = xhr.responseJSON.message;
    }

    Swal.fire({
        icon: 'error',
        title: 'Error!',
        html: errorMessage
    });
}

            });

        });
        
    });

    // Modal Export Excel
document.addEventListener("DOMContentLoaded", function() {
    let exportModal = new bootstrap.Modal(document.getElementById('exportModal'));

    document.getElementById("openExportModal").addEventListener("click", function() {
        exportModal.show();
    });

    document.getElementById("exportForm").addEventListener("submit", function(event) {
        const useFilters = document.getElementById('useFilters').checked;
        
        if (useFilters) {
            // Append current filters to the form
            const searchInput = document.getElementById('searchInput').value;
            const kategoriFilter = document.getElementById('kategoriFilter').value;
            const golonganFilter = document.getElementById('golonganFilter').value;
            
            // Create hidden inputs
            const searchField = document.createElement('input');
            searchField.type = 'hidden';
            searchField.name = 'search';
            searchField.value = searchInput;
            
            const kategoriField = document.createElement('input');
            kategoriField.type = 'hidden';
            kategoriField.name = 'kategori';
            kategoriField.value = kategoriFilter;
            
            const golonganField = document.createElement('input');
            golonganField.type = 'hidden';
            golonganField.name = 'golongan';
            golonganField.value = golonganFilter;
            
            // Append to form
            this.appendChild(searchField);
            this.appendChild(kategoriField);
            this.appendChild(golonganField);
        }
    });
});
</script>


