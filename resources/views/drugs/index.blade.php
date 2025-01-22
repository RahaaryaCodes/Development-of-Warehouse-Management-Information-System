<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Obat</title>

    <!-- Existing CSS -->
    <link href="{{ asset('templates/NiceAdmin/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/NiceAdmin/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/NiceAdmin/assets/css/style.css') }}" rel="stylesheet">

    <style>
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table thead th {
            background-color: #4154f1;
            color: white;
            font-weight: 500;
        }
        .search-box {
            margin-bottom: 20px;
        }
        .low-stock {
            color: #dc3545;
            font-weight: bold;
        }
        .badge-stock {
            padding: 5px 10px;
            border-radius: 20px;
        }
    </style>
</head>
<body>
  <!-- ======= Navbar ======= -->
  @include('layouts.partials.navbar')

  <!-- ======= Sidebar ======= -->
  @include('layouts.partials.sidebar')

    <main id="main" class="main">
        <div class="container">
            <!-- Search Section -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="search-box">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari obat...">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <select id="kategoriFilter" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori }}">{{ $kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('data-obat.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Obat
                    </a>
                </div>
            </div>

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
                                <th>Stok</th>
                                <th>Harga Jual</th>
                                <th>Kadaluarsa</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="drugsTableBody">
                            @forelse($drugs as $index => $drug)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $drug->batch }}</td>
                                    <td>{{ $drug->nama_obat }}</td>
                                    <td>{{ $drug->kategori_obat }}</td>
                                    <td>{{ $drug->jenis_obat }}</td>
                                    <td>
                                        @if($drug->stok <= $drug->stok_minimum)
                                            <span class="badge bg-danger badge-stock">{{ $drug->stok }}</span>
                                        @else
                                            <span class="badge bg-success badge-stock">{{ $drug->stok }}</span>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($drug->harga_jual, 0, ',', '.') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($drug->tanggal_kadaluarsa)->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('data-obat.edit', $drug->id) }}" 
                                               class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm delete-drug" 
                                                    data-id="{{ $drug->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data obat</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('templates/NiceAdmin/assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('templates/NiceAdmin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Delete functionality
            let deleteId = null;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

            $('.delete-drug').click(function() {
                deleteId = $(this).data('id');
                deleteModal.show();
            });

            $('#confirmDelete').click(function() {
                if (deleteId) {
                    $.ajax({
                        url: `/data-obat/${deleteId}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            location.reload();
                        },
                        error: function(xhr) {
                            alert('Terjadi kesalahan saat menghapus data');
                        }
                    });
                }
                deleteModal.hide();
            });

            // Search functionality
            let searchTimer;
            $('#searchInput, #kategoriFilter').on('input change', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    $.ajax({
                        url: '{{ route("data-obat.search") }}',
                        method: 'GET',
                        data: {
                            query: $('#searchInput').val(),
                            kategori: $('#kategoriFilter').val()
                        },
                        success: function(response) {
                            updateTable(response.data);
                        },
                        error: function(xhr) {
                            alert('Terjadi kesalahan saat mencari data');
                        }
                    });
                }, 500);
            });

            function updateTable(data) {
                const tbody = $('#drugsTableBody');
                tbody.empty();

                if (data.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data yang ditemukan</td>
                        </tr>
                    `);
                    return;
                }

                data.forEach((drug, index) => {
                    const stockBadgeClass = drug.stok <= drug.stok_minimum ? 'bg-danger' : 'bg-success';
                    tbody.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${drug.batch}</td>
                            <td>${drug.nama_obat}</td>
                            <td>${drug.kategori_obat}</td>
                            <td>${drug.jenis_obat}</td>
                            <td>
                                <span class="badge ${stockBadgeClass} badge-stock">${drug.stok}</span>
                            </td>
                            <td>Rp ${new Intl.NumberFormat('id-ID').format(drug.harga_jual)}</td>
                            <td>${new Date(drug.tanggal_kadaluarsa).toLocaleDateString('id-ID')}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="/data-obat/${drug.id}/edit" 
                                       class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-danger btn-sm delete-drug" 
                                            data-id="${drug.id}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `);
                });

                // Reattach delete event handlers
                $('.delete-drug').click(function() {
                    deleteId = $(this).data('id');
                    deleteModal.show();
                });
            }
        });
    </script>
</body>
</html>