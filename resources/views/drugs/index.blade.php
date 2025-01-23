<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Obat</title>

    <!-- Existing CSS -->
    <link href="{{ asset('templates/NiceAdmin/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
        .pagination {
    margin-top: 20px;
        }
        .pagination .page-item.active .page-link {
        background-color: #4154f1;
        color: white;
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
                                    <td>{{ $startNumber + $index }}</td>
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
                                            <button 
                                            class="btn btn-danger btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal" 
                                            onclick="setDeleteForm('{{ route('data-obat.destroy', $drug->id) }}')">
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
                    <!-- Pagination -->
                    <nav aria-label="Page navigation" class="mt-3">
                        {{ $drugs->links('pagination::bootstrap-5') }}
                    </nav>
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
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini?
            </div>
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


    <!-- Scripts -->
    <script src="{{ asset('templates/NiceAdmin/assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('templates/NiceAdmin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SweetAlert untuk menampilkan notifikasi sukses -->
<script>
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        timer: 3000, // Waktu dalam milidetik
        showConfirmButton: false
    });
    @endif

    @if(session('added'))
    Swal.fire({
        icon: 'success',
        title: 'Data Berhasil Ditambahkan!',
        text: '{{ session('added') }}',
        timer: 3000, // Waktu dalam milidetik
        showConfirmButton: false
    });
    @endif
</script>

<!-- Script untuk mengatur form penghapusan -->
<script>
    function setDeleteForm(actionUrl) {
        const form = document.getElementById('deleteForm');
        form.action = actionUrl;
    }
</script>

<!-- Script utama untuk delete modal dan event handler -->
<script>
   $(document).ready(function() {
    let currentPage = 1;

    // Fungsi untuk mengambil dan menampilkan data
    function fetchDrugs(page = 1) {
        let searchInput = $('#searchInput').val();
        let kategoriFilter = $('#kategoriFilter').val();

        $.ajax({
            url: "{{ route('data-obat.search') }}",  // Pastikan URL ini benar
            type: "GET",
            data: {
                search: searchInput,
                kategori: kategoriFilter,
                page: page  // Kirim parameter halaman yang benar
            },
            success: function(response) {
                let rows = '';
                $.each(response.data, function(index, drug) {
                    rows += `<tr>
                                <td>${index + 1}</td>
                                <td>${drug.batch}</td>
                                <td>${drug.nama_obat}</td>
                                <td>${drug.kategori_obat}</td>
                                <td>${drug.jenis_obat}</td>
                                <td><span class="badge ${drug.stok <= drug.stok_minimum ? 'bg-danger' : 'bg-success'}">${drug.stok}</span></td>
                                <td>Rp ${new Intl.NumberFormat('id-ID').format(drug.harga_jual)}</td>
                               <td>${new Date(drug.tanggal_kadaluarsa).toLocaleDateString('id-ID')}</td>
                                <td>
                                    <a href="/data-obat/edit/${drug.id}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="setDeleteForm('{{ route('data-obat.destroy', '') }}/${drug.id}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>`;
                });

                // Update isi tabel
                $('#drugsTableBody').html(rows);

                // Update pagination (next/previous)
                let pagination = '';
                if (response.links.prev) {
                    pagination += `<a href="javascript:void(0);" class="btn btn-link" onclick="fetchDrugs(${currentPage - 1})">Prev</a>`;
                }
                if (response.links.next) {
                    pagination += `<a href="javascript:void(0);" class="btn btn-link" onclick="fetchDrugs(${currentPage + 1})">Next</a>`;
                }
                $('#pagination').html(pagination);
            }
        });
    }

    // Trigger pencarian setiap kali ada perubahan input
    $('#searchInput, #kategoriFilter').on('keyup change', function() {
        currentPage = 1; // Reset halaman ke 1 saat pencarian/filter berubah
        fetchDrugs(currentPage);
    });

    // Panggil fungsi pertama kali untuk menampilkan data
    fetchDrugs();
});


</script>

<!-- Script untuk memperbarui tabel data -->
{{-- <script>
    function updateTable(data, page) {
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
                    <td>${(page - 1) * 10 + index + 1}</td>
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
</script> --}}

</body>
</html>