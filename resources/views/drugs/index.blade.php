@section('title', 'Daftar Obat')

@include('layouts.partials.head')
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

<main id="main" class="main">
    <div class="container">
        <!-- Search Section -->
        <form action="{{ route('data-obat.index') }}" method="GET">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="search-box">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari obat..." name="search" value="{{ request('search') }}">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <select id="kategoriFilter" class="form-select" name="kategori">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('data-obat.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Obat
                    </a>
                </div>
            </div>
        </form>

        <!-- Table Section -->
        <div class="table">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Batch</th>
                            <th>Nama Obat</th>
                            <th>Kategori</th>
                            <th>Jenis</th>
                            <th>Satuan</th>
                            <th>Stok</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Kadaluarsa</th>
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
     @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        timer: 1000, // Waktu dalam milidetik
        showConfirmButton: false
    });
    @endif

    @if(session('added'))
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
        const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    }

    // Fungsi untuk memuat data tabel dengan AJAX
    function fetchDrugs(page = 1) {
        const searchInput = $('#searchInput').val(); // Ambil nilai dari input pencarian
        const kategoriFilter = $('#kategoriFilter').val(); // Ambil nilai dari filter kategori

        $.ajax({
            url: "{{ route('data-obat.index') }}",  // Ubah URL ke rute index
            type: "GET",
            data: {
                search: searchInput,  // Kirim nilai pencarian ke server
                kategori: kategoriFilter,  // Kirim nilai filter kategori
                page: page  // Kirim nomor halaman
            },
            success: function(response) {
                if (response.data && response.data.length > 0) {  // Pastikan ada data
                    let rows = '';
                    $.each(response.data, function(index, drug) {
                        rows += `
                            <tr>
                                <td>${index + 1 + (page - 1) * 10}</td>
                                <td>${drug.batch}</td>
                                <td>${drug.nama_obat}</td>
                                <td>${drug.kategori_obat}</td>
                                <td>${drug.jenis_obat}</td>
                                <td>${drug.satuan}</td>
                                <td><span class="badge ${drug.stok <= drug.stok_minimum ? 'bg-danger' : 'bg-success'}">${drug.stok}</span></td>
                                <td>${formatCurrency(drug.harga_beli)}</td>
                                <td>${formatCurrency(drug.harga_jual)}</td>
                                <td>${formatDate(drug.tanggal_kadaluarsa)}</td>
                                <td>
                                <a href="/data-obat/${drug.id}/edit" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="openDeleteModal(${drug.id})">
                                        <i class="bi bi-trash"></i>
                                    </a>
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
                    history.pushState(null, '', `?search=${searchInput}&kategori=${kategoriFilter}&page=${page}`);
                } else {
                    // Tampilkan pesan jika tidak ada data
                    $('#drugsTableBody').html('<tr><td colspan="11" class="text-center">Data tidak ditemukan</td></tr>');
                    $('#pagination').html('');  // Hapus tombol pagination jika tidak ada data
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
                _token: '{{ csrf_token() }}',  // Pastikan token CSRF ada
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                Swal.fire('Berhasil!', response.message, 'success');
                fetchDrugs();  // Panggil ulang fetchDrugs untuk memperbarui tabel
            }
        });

    });

    // Inisialisasi tabel saat halaman dimuat
    $(document).ready(function() {
        fetchDrugs();  // Panggil fungsi fetchDrugs() pertama kali

        // Event listener untuk live search
        $('#searchInput').on('keyup', function() {
            fetchDrugs();  // Panggil fungsi fetchDrugs() dengan parameter pencarian
        });

        // Event untuk filter kategori live
        $('#kategoriFilter').on('change', function () {
            fetchDrugs(1);
        });
    });
</script>
