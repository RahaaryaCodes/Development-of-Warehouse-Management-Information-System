@section('title', 'Daftar Supplier')

@include('layouts.partials.head')
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

<main id="main" class="main">
    <!-- Bagian Form Pencarian -->
    <form action="{{ route('data-supplier.index') }}" method="GET">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="search-box">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari supplier..." name="search" value="{{ request('search') }}">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-md-8 text-end">
                <a href="{{ route('data-supplier.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Supplier
                </a>
            </div>
        </div>
    </form>

    <!-- Bagian Tabel Supplier -->
    <div class="table">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th style="width: 15%;">Nama Supplier</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="supplierTableBody">
                    <!-- Data akan dimuat di sini via AJAX -->
                </tbody>
            </table>
            
            <!-- Menampilkan pagination -->
            <div id="pagination" class="mt-3 d-flex justify-content-center">
                <!-- Pagination akan dirender di sini -->
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


     // Fungsi untuk membuka modal delete
     let supplierIdToDelete;
    function openDeleteModal(supplierId) {
        supplierIdToDelete = supplierId;
        $('#deleteModal').modal('show');
    }

    // Fungsi untuk menghapus data obat
    $('#confirmDeleteButton').on('click', function() {
                $.ajax({
            url: `/data-supplier/${supplierIdToDelete}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}',  // Pastikan token CSRF ada
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                Swal.fire('Berhasil!', response.message, 'success');
                fetchSupplier();  // Panggil ulang fetchDrugs untuk memperbarui tabel
            }
        });

    });

    

    // Fungsi untuk mengambil data supplier dan menampilkan tabel
    function fetchSupplier(page = 1) {
        const searchInput = $('#searchInput').val(); // Ambil nilai dari input pencarian

        $.ajax({
            url: "{{ route('data-supplier.index') }}",  // Ubah URL ke rute index
            type: "GET",
            data: {
                search: searchInput,  // Kirim nilai pencarian ke server
                page: page  // Kirim nomor halaman
            },
            success: function(response) {
                if (response.data && response.data.length > 0) {  // Pastikan ada data
                    let rows = '';
                    $.each(response.data, function(index, supplier) {
                        rows += `
                            <tr>
                                <td>${index + 1 + (page - 1) * 10}</td>
                                <td>${supplier.nama_supplier}</td>
                                <td>${supplier.alamat}</td>
                                <td>${supplier.telepon}</td>
                                <td>${supplier.email}</td>
                                <td>${supplier.keterangan ?? '-'}</td>
                               <td>
                                <div class="d-inline-flex gap-1">
                                    <a href="/data-supplier/${supplier.id}/edit" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="openDeleteModal(${supplier.id})">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                            </tr>`; 
                    });
                    $('#supplierTableBody').html(rows);

                    // Update pagination buttons
                    let paginationButtons = `<div class="btn-group" role="group">`;

                    if (response.pagination.prev_page_url) {
                        paginationButtons += `
                            <button class="btn btn-outline-primary" onclick="fetchSupplier(${page - 1})">
                                <i class="bi bi-arrow-left"></i> Prev
                            </button>`;
                    }

                    for (let i = 1; i <= response.pagination.last_page; i++) {
                        paginationButtons += `
                            <button class="btn ${i === page ? 'btn-primary' : 'btn-outline-primary'}" onclick="fetchSupplier(${i})">
                                ${i}
                            </button>`;
                    }

                    if (response.pagination.next_page_url) {
                        paginationButtons += `
                            <button class="btn btn-outline-primary" onclick="fetchSupplier(${page + 1})">
                                Next <i class="bi bi-arrow-right"></i>
                            </button>`;
                    }

                    paginationButtons += `</div>`;
                    $('#pagination').html(paginationButtons);

                    // Update URL tanpa reload halaman
                    history.pushState(null, '', `?search=${searchInput}&page=${page}`);
                } else {
                    // Tampilkan pesan jika tidak ada data
                    $('#supplierTableBody').html('<tr><td colspan="11" class="text-center">Data tidak ditemukan</td></tr>');
                    $('#pagination').html('');  // Hapus tombol pagination jika tidak ada data
                }
            }
        });
    }
               
    $(document).ready(function() {
        fetchSupplier();  // Panggil fungsi fetchSupplier() pertama kali

        // Event listener untuk live search
        $('#searchInput').on('keyup', function() {
            fetchSupplier();  // Panggil fungsi fetchSupplier() dengan parameter pencarian
        });
    });


</script>