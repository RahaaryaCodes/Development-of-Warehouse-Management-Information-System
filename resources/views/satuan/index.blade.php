@section('title', 'Daftar Satuan')

@include('layouts.partials.head')
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

<main id="main" class="main">
    <!-- Bagian Form Pencarian -->
    <form action="{{ route('data-satuan.index') }}" method="GET">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="search-box">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari satuan..." name="search" value="{{ request('search') }}">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-md-8 text-end">
                <a href="{{ route('data-satuan.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Satuan
                </a>
            </div>
        </div>
    </form>

    <!-- Bagian Tabel Satuan -->
    <div class="table">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Satuan</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="satuanTableBody">
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
              <p>Apakah Anda yakin ingin menghapus data satuan ini?</p>
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

// Memastikan bahwa penghapusan data memperbarui tampilan
function fetchSatuan(page = 1) {
    const searchInput = $('#searchInput').val();

    $.ajax({
        url: "{{ route('data-satuan.index') }}", // URL untuk mendapatkan data satuan
        type: "GET",
        data: {
            search: searchInput,
            page: page
        },
        success: function(response) {
            if (response.data && response.data.length > 0) {
                let rows = '';
                $.each(response.data, function(index, satuan) {
                    rows += `
                        <tr>
                            <td>${index + 1 + (page - 1) * 10}</td>
                            <td>${satuan.nama_satuan}</td>
                            <td>${satuan.keterangan ?? '-'}</td>
                            <td>
                                <div class="d-inline-flex gap-1">
                                    <a href="/data-satuan/${satuan.id}/edit" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                   <a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="openDeleteModal(${satuan.id})">
                                        <i class="bi bi-trash"></i>
                                    </a>

                                </div>
                            </td>
                        </tr>`;
                });
                $('#satuanTableBody').html(rows);

                // Pagination logic remains the same
                let paginationButtons = `<div class="btn-group" role="group">`;
                if (response.pagination.prev_page_url) {
                    paginationButtons += `<button class="btn btn-outline-primary" onclick="fetchSatuan(${page - 1})"><i class="bi bi-arrow-left"></i> Prev</button>`;
                }
                for (let i = 1; i <= response.pagination.last_page; i++) {
                    paginationButtons += `<button class="btn ${i === page ? 'btn-primary' : 'btn-outline-primary'}" onclick="fetchSatuan(${i})">${i}</button>`;
                }
                if (response.pagination.next_page_url) {
                    paginationButtons += `<button class="btn btn-outline-primary" onclick="fetchSatuan(${page + 1})">Next <i class="bi bi-arrow-right"></i></button>`;
                }
                paginationButtons += `</div>`;
                $('#pagination').html(paginationButtons);

                history.pushState(null, '', `?search=${searchInput}&page=${page}`);
            } else {
                $('#satuanTableBody').html('<tr><td colspan="4" class="text-center">Data tidak ditemukan</td></tr>');
                $('#pagination').html('');
            }
        }
    });
}

 // Fungsi untuk membuka modal delete
 let satuanIdToDelete;
    function openDeleteModal(satuanId) {
        satuanIdToDelete = satuanId;
        $('#deleteModal').modal('show');
    }

    // Fungsi untuk menghapus data obat
    $('#confirmDeleteButton').on('click', function() {
                $.ajax({
            url: `/data-satuan/${satuanIdToDelete}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}',  
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                Swal.fire('Berhasil!', response.message, 'success');
                fetchSatuan();  
            }
        });

    });

// Inisialisasi tabel saat halaman dimuat
$(document).ready(function() {
        fetchSatuan();  // Panggil fungsi fetchDrugs() pertama kali

        // Event listener untuk live search
        $('#searchInput').on('keyup', function() {
            fetchSatuan();  // Panggil fungsi fetchDrugs() dengan parameter pencarian
        });
    });

</script>
