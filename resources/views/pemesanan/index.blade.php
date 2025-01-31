@section('title', 'Daftar Obat')

@include('layouts.partials.head')

<!-- Navbar -->
@include('layouts.partials.navbar')

<!-- Sidebar -->
@include('layouts.partials.sidebar')

<main class="main" id="main">
  <div class="container">
    <!-- Bagian Form Pencarian -->
    <form action="{{ route('pemesanan-barang.index') }}" method="GET">
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
          <select id="jenisSuratFilter" class="form-select" name="jenis_surat">
            <option value="">Filter Jenis Surat</option>
            <option value="Reguler" {{ request('jenis_surat') == 'Reguler' ? 'selected' : '' }}>Reguler</option>
            <option value="Psikotropika" {{ request('jenis_surat') == 'Psikotropika' ? 'selected' : '' }}>Psikotropika</option>
            <option value="OOT" {{ request('jenis_surat') == 'OOT' ? 'selected' : '' }}>OOT</option>
            <option value="Prekursor" {{ request('jenis_surat') == 'Prekursor' ? 'selected' : '' }}>Prekursor</option>
          </select>
        </div>
        <div class="col-md-4 text-end">
          <a href="{{ route('pemesanan-barang.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Pemesanan
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
              <th>Tanggal Pemesanan</th>
              <th>Supplier</th>
              <th>Jenis Surat</th>
              <th>Total Harga</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="pemesananTableBody">
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

@include('layouts.partials.footer')

<script>
  // Function to load pemesanan data via AJAX
  function fetchPemesanan(page = 1) {
    const searchInput = $('#searchInput').val(); // Get the search input value
    const jenisSuratFilter = $('#jenisSuratFilter').val(); // Get the jenis_surat filter value

    $.ajax({
    url: "{{ route('pemesanan-barang.index') }}",  // Adjust the route as needed
    type: "GET",
    data: {
        search: searchInput,  // Send the search value
        jenis_surat: jenisSuratFilter,  // Send the jenis_surat filter value
        page: page  // Send the current page number
    },
    success: function(response) {
        if (response.data && response.data.length > 0) {
            let rows = '';
            $.each(response.data, function(index, order) {
                // Add the status styling here
                let status = order.status.trim().toLowerCase();
                  switch (status) {
                      case 'menunggu konfirmasi':
                          statusClass = 'badge bg-secondary';
                          break;
                      case 'dikirim':
                          statusClass = 'badge bg-primary';
                          break;
                      case 'selesai':
                          statusClass = 'badge bg-success';
                          break;
                      case 'dibatalkan':
                          statusClass = 'badge bg-danger';
                          break;
                      default:
                          statusClass = 'badge bg-secondary';
                  }


                rows += `
                    <tr>
                        <td>${index + 1 + (page - 1) * 10}</td>
                        <td>${order.tanggal_pemesanan}</td>
                        <td>${order.supplier}</td>
                        <td>${order.jenis_surat}</td>
                        <td>${formatCurrency(order.total_harga)}</td>
                        <td><span class="${statusClass}">${order.status}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="/pemesanan-barang/${order.id}/edit" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="openDeleteModal(${order.id})">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            });
            $('#pemesananTableBody').html(rows);

            // Pagination
            let paginationButtons = `<div class="btn-group" role="group">`;
            if (response.pagination.prev_page_url) {
                paginationButtons += `
                    <button class="btn btn-outline-primary" onclick="fetchPemesanan(${page - 1})">
                        <i class="bi bi-arrow-left"></i> Prev
                    </button>`;
            }

            for (let i = 1; i <= response.pagination.last_page; i++) {
                paginationButtons += `
                    <button class="btn ${i === page ? 'btn-primary' : 'btn-outline-primary'}" onclick="fetchPemesanan(${i})">
                        ${i}
                    </button>`;
            }

            if (response.pagination.next_page_url) {
                paginationButtons += `
                    <button class="btn btn-outline-primary" onclick="fetchPemesanan(${page + 1})">
                        Next <i class="bi bi-arrow-right"></i>
                    </button>`;
            }

            paginationButtons += `</div>`;
            $('#pagination').html(paginationButtons);

            // Update URL without reloading the page
            history.pushState(null, '', `?search=${searchInput}&jenis_surat=${jenisSuratFilter}&page=${page}`);
        } else {
            // Show no data message if there's no data
            $('#pemesananTableBody').html('<tr><td colspan="7" class="text-center">Data tidak ditemukan</td></tr>');
            $('#pagination').html('');
        }
    }
});

  }

  // Event listener for search input
  $('#searchInput').on('keyup', function() {
    fetchPemesanan(); // Fetch pemesanan data based on search input
  });

  // Event listener for jenis_surat filter
  $('#jenisSuratFilter').on('change', function() {
    fetchPemesanan(1); // Fetch pemesanan data based on filter
  });

  // Initialize table on page load
  $(document).ready(function() {
    fetchPemesanan(); // Initial call to fetch data

    // Trigger search
    $('#searchInput').on('keyup', function() {
      fetchPemesanan();
    });

    // Trigger filter
    $('#jenisSuratFilter').on('change', function() {
      fetchPemesanan();
    });
  });

  // Helper function to format currency (Total Harga)
  function formatCurrency(amount) {
    return 'Rp ' + amount.toLocaleString('id-ID');
  }
</script>
