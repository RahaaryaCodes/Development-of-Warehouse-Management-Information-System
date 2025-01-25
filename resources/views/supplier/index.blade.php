@section('title', 'Data Supplier')
<!-- ======= Header ======= -->
@include('layouts.partials.head')
<!-- ======= Navbar ======= -->
@include('layouts.partials.navbar')

<!-- ======= Sidebar ======= -->
@include('layouts.partials.sidebar')

<main id="main" class="main">
    <div class="container">
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

        <!-- Section: Table -->
        <div class="table">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th style="width: 200px">Nama Supplier</th>
                            <th>Alamat</th>
                            <th>Telepon</th>
                            <th>Email</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="supplierTableBody">
                        @foreach($suppliers as $index => $supplier)
                            <tr>
                                <td>{{ $index + 1 + ($suppliers->currentPage() - 1) * $suppliers->perPage() }}</td>
                                <td>{{ $supplier->nama_supplier }}</td>
                                <td>{{ $supplier->alamat }}</td>
                                <td>{{ $supplier->telepon }}</td>
                                <td>{{ $supplier->email }}</td>
                                <td>{{ $supplier->keterangan ?? '-' }}</td>
                                <td>
                                    <div class="d-inline-flex gap-1">
                                        <!-- Tombol Edit -->
                                        <a href="{{ route('data-supplier.edit', $supplier->id) }}" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        
                                        <!-- Tombol Hapus -->
                                        <button 
                                            class="btn btn-danger btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal" 
                                            onclick="setDeleteForm('{{ route('data-supplier.destroy', $supplier->id) }}')">
                                            <i class="bi bi-trash"></i> 
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>                        
                </table>
                <nav aria-label="Page navigation" class="mt-3">
                    {{ $suppliers->appends(request()->query())->links('pagination::bootstrap-5') }}
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
                Apakah Anda yakin ingin menghapus supplier ini?
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

    function setDeleteForm(actionUrl) {
        const form = document.getElementById('deleteForm');
        form.action = actionUrl;
    }

    $(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    let currentPage = urlParams.get('page') || 1; // Ambil halaman saat ini

    // Fungsi untuk mengambil data dan menampilkan tabel
    function fetchSupplier(page = 1) {
        const searchInput = $('#searchInput').val(); // Ambil nilai pencarian

        $.ajax({
            url: "{{ route('supplier.search') }}",  // URL pencarian
            type: "GET",
            data: {
                search: searchInput,  // Kirim parameter pencarian
                page: page,           // Kirim parameter halaman
            },
            success: function(response) {
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
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="setDeleteForm('/data-supplier/${supplier.id}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                </div>
                            </td>
                        </tr>`;
                });

                // Update tabel
                $('#supplierTableBody').html(rows);

                // Update pagination
                let pagination = '';
                if (response.links.prev) {
                    pagination += `<a href="javascript:void(0);" class="btn btn-link" onclick="fetchSupplier(${page - 1})">Prev</a>`;
                }
                pagination += ` <span>Halaman ${response.current_page} dari ${response.total_pages}</span> `;
                if (response.links.next) {
                    pagination += `<a href="javascript:void(0);" class="btn btn-link" onclick="fetchSupplier(${page + 1})">Next</a>`;
                }

                // Update pagination container
                $('#pagination').html(pagination);

                // Update URL di browser tanpa refresh
                history.pushState(null, '', `?search=${searchInput}&page=${page}`);
            },
        });
    }

    // Event listener untuk live search
    $('#searchInput').on('keyup change', function () {
        fetchSupplier(1); // Reset ke halaman pertama saat pencarian berubah
    });

    // Panggil fungsi pertama kali untuk menampilkan data pada halaman pertama
    fetchSupplier(currentPage);
});

</script>
