@section('title', 'Daftar Golongan Obat')

@include('layouts.partials.head')

<!-- Navbar -->
@include('layouts.partials.navbar')

<!-- Sidebar -->
@include('layouts.partials.sidebar')

<main id="main" class="main">
    <div class="container">
        <!-- Search Section -->
        <form action="{{ route('data-golongan.index') }}" method="GET">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="search-box">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari golongan..." name="search" value="{{ request('search') }}">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 text-end">
                    <a href="{{ route('data-golongan.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Golongan
                    </a>
                </div>
            </div>
        </form>
        
        <!-- Table Section -->
        <div class="table">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Golongan</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="golonganTableBody">
                        <!-- Data Golongan akan dimuat di sini -->
                    </tbody>
                </table>

                <!-- Menampilkan pagination -->
                <div id="pagination" class="mt-3 d-flex justify-content-center">
                    <!-- Pagination akan dirender di sini -->
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Apakah Anda yakin ingin menghapus data ini?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('layouts.partials.footer') <!-- Footer -->

<script>
    function fetchGolongan(page = 1) {
        const searchInput = $('#searchInput').val();

        $.ajax({
            url: "{{ route('data-golongan.index') }}",
            type: "GET",
            data: { search: searchInput, page: page },
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    let rows = '';
                    $.each(response.data, function(index, golongan) {
                        rows += `
                            <tr>
                                <td>${index + 1 + (page - 1) * 10}</td>
                                <td>${golongan.nama_golongan}</td>
                                <td>${golongan.keterangan ?? '-'}</td>
                                <td>
                                    <div class="d-inline-flex gap-1">
                                        <a href="/data-golongan/${golongan.id}/edit" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="setDeleteForm('/data-golongan/${golongan.id}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>`;
                    });
                    $('#golonganTableBody').html(rows);
                    renderPagination(response.pagination, page);
                    history.pushState(null, '', `?search=${searchInput}&page=${page}`);
                } else {
                    $('#golonganTableBody').html('<tr><td colspan="4" class="text-center">Data tidak ditemukan</td></tr>');
                    $('#pagination').html('');
                }
            }
        });
    }

    function setDeleteForm(url) {
        $('#deleteForm').attr('action', url);
    }

    function renderPagination(pagination, currentPage) {
        let paginationButtons = `<div class="btn-group" role="group">`;
        if (pagination.prev_page_url) {
            paginationButtons += `<button class="btn btn-outline-primary" onclick="fetchGolongan(${currentPage - 1})"><i class="bi bi-arrow-left"></i> Prev</button>`;
        }
        for (let i = 1; i <= pagination.last_page; i++) {
            paginationButtons += `<button class="btn ${i === currentPage ? 'btn-primary' : 'btn-outline-primary'}" onclick="fetchGolongan(${i})">${i}</button>`;
        }
        if (pagination.next_page_url) {
            paginationButtons += `<button class="btn btn-outline-primary" onclick="fetchGolongan(${currentPage + 1})">Next <i class="bi bi-arrow-right"></i></button>`;
        }
        paginationButtons += `</div>`;
        $('#pagination').html(paginationButtons);
    }

    $(document).ready(function() {
        fetchGolongan(); // Menampilkan golongan pertama kali

        $('#searchInput').on('keyup', function() {
            fetchGolongan();
        });
    });
</script>
