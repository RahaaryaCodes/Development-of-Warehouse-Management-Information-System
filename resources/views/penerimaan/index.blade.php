@section('title', 'Daftar Penerimaan')

@include('layouts.partials.head')

<!-- Navbar -->
@include('layouts.partials.navbar')

<!-- Sidebar -->
@include('layouts.partials.sidebar')

<main class="main" id="main">
  <div class="container">
    <!-- Bagian Form Pencarian -->
    <form action="{{ route('penerimaan-barang.index') }}" method="GET">
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="search-box">
            <div class="input-group">
              <input type="text" id="searchInput" class="form-control" placeholder="Cari supplier..." name="search" value="{{ request('search') }}">
              <span class="input-group-text"><i class="bi bi-search"></i></span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <select id="statusFilter" class="form-select" name="status">
            <option value="">Filter Status</option>
            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
            <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
          </select>
        </div>
        <div class="col-md-4 text-end">
          <a href="{{ route('penerimaan-barang.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Penerimaan
          </a>
        </div>
      </div>
    </form>

    <!-- Table Section -->
    <div class="table">
      <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
          <thead>
            <tr>
              <th>No</th>
              <th>No Faktur</th>
              <th>Tanggal Penerimaan</th>
              <th>Supplier</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="penerimaanTableBody">
            <!-- Data will be loaded via AJAX -->
          </tbody>
        </table>

        <!-- Pagination -->
        <div id="pagination" class="mt-3 d-flex justify-content-center">
          <!-- Pagination buttons will be loaded via AJAX -->
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
        <p>Apakah Anda yakin ingin menghapus data penerimaan ini?</p>
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
      timer: 1000,
      showConfirmButton: false
    });
  @endif

  // Fungsi untuk membuka modal delete
  let penerimaanIdToDelete;
  function openDeleteModal(penerimaanId) {
    penerimaanIdToDelete = penerimaanId;
    $('#deleteModal').modal('show');
  }

  // Fungsi untuk menghapus data penerimaan
  $('#confirmDeleteButton').on('click', function() {
    $.ajax({
      url: `/penerimaan-barang/${penerimaanIdToDelete}`,
      type: 'DELETE',
      data: {
        _token: '{{ csrf_token() }}',
      },
      success: function(response) {
        $('#deleteModal').modal('hide');
        Swal.fire('Berhasil!', response.message, 'success');
        fetchPenerimaan();
      },
      error: function() {
        Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data.', 'error');
      }
    });
  });

  // Function to load penerimaan data via AJAX
  function fetchPenerimaan(page = 1) {
    const searchInput = $('#searchInput').val();
    const statusFilter = $('#statusFilter').val();

    $.ajax({
      url: "{{ route('penerimaan-barang.index') }}",
      type: "GET",
      data: {
        search: searchInput,
        status: statusFilter,
        page: page
      },
      success: function(response) {
        if (response.data && response.data.length > 0) {
          let rows = '';
          $.each(response.data, function(index, penerimaan) {
            let statusClass = '';
            switch (penerimaan.status.toLowerCase()) {
              case 'pending':
                statusClass = 'badge bg-warning';
                break;
              case 'diterima':
                statusClass = 'badge bg-primary';
                break;
              case 'selesai':
                statusClass = 'badge bg-success';
                break;
              default:
                statusClass = 'badge bg-secondary';
            }

            rows += `
              <tr>
                <td>${index + 1 + (page - 1) * 10}</td>
                <td>${penerimaan.no_faktur}</td>
                <td>${penerimaan.tanggal_penerimaan}</td>
                <td>${penerimaan.supplier}</td>
                <td><span class="${statusClass}">${penerimaan.status}</span></td>
                <td>
                  <div class="d-flex gap-1">
                    ${penerimaan.status !== 'Selesai' ? `
                      <a href="/penerimaan-barang/${penerimaan.id}/edit" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i>
                      </a>
                    ` : ''}
                    <a href="/penerimaan-barang/${penerimaan.id}" class="btn btn-info btn-sm">
                      <i class="bi bi-eye"></i>
                    </a>
                    ${penerimaan.status === 'Pending' ? `
                      <a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="openDeleteModal(${penerimaan.id})">
                        <i class="bi bi-trash"></i>
                      </a>
                    ` : ''}
                  </div>
                </td>
              </tr>
            `;
          });

          $('#penerimaanTableBody').html(rows);

          // Pagination
          let paginationButtons = `<div class="btn-group" role="group">`;
          if (response.pagination.prev_page_url) {
            paginationButtons += `
              <button class="btn btn-outline-primary" onclick="fetchPenerimaan(${page - 1})">
                <i class="bi bi-arrow-left"></i> Prev
              </button>`;
          }

          for (let i = 1; i <= response.pagination.last_page; i++) {
            paginationButtons += `
              <button class="btn ${i === page ? 'btn-primary' : 'btn-outline-primary'}" onclick="fetchPenerimaan(${i})">
                ${i}
              </button>`;
          }

          if (response.pagination.next_page_url) {
            paginationButtons += `
              <button class="btn btn-outline-primary" onclick="fetchPenerimaan(${page + 1})">
                Next <i class="bi bi-arrow-right"></i>
              </button>`;
          }

          paginationButtons += `</div>`;
          $('#pagination').html(paginationButtons);

          history.pushState(null, '', `?search=${searchInput}&status=${statusFilter}&page=${page}`);
        } else {
          $('#penerimaanTableBody').html('<tr><td colspan="6" class="text-center">Data tidak ditemukan</td></tr>');
          $('#pagination').html('');
        }
      }
    });
  }

  // Event listeners
  $('#searchInput').on('keyup', function() {
    fetchPenerimaan();
  });

  $('#statusFilter').on('change', function() {
    fetchPenerimaan(1);
  });

  // Initialize table on page load
  $(document).ready(function() {
    fetchPenerimaan();
  });
</script>