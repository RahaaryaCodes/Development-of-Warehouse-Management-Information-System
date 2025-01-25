@section('title', 'Daftar Kategori Obat') 

@include('layouts.partials.head')

<!-- Navbar -->
@include('layouts.partials.navbar')

<!-- Sidebar -->
@include('layouts.partials.sidebar')

<main id="main" class="main">
    <div class="container">
        <!-- Search Section -->
        <form action="{{ route('data-kategori.index') }}" method="GET">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="search-box">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari kategori..." name="search" value="{{ request('search') }}">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <select id="kategoriFilter" class="form-select" name="kategori">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->nama_kategori }}" {{ request('kategori') == $kategori->nama_kategori ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('data-kategori.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Kategori
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
                            <th>Nama Kategori</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="kategoriTableBody">
                        @foreach($kategoris as $index => $kategori)
                            <tr>
                                <td>{{ $index + 1 + ($kategoris->currentPage() - 1) * $kategoris->perPage() }}</td>
                                <td>{{ $kategori->nama_kategori }}</td>
                                <td>{{ $kategori->keterangan ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('data-kategori.edit', $kategori->id) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i> 
                                    </a>
                                    <form action="{{ route('data-kategori.destroy', $kategori->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> 
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>    

                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-3">
                    {{ $kategoris->appends(request()->query())->links('pagination::bootstrap-5') }}
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

@include('layouts.partials.footer') <!-- Footer -->

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
        let currentPage = urlParams.get('page') || 1;

        function fetchCategories(page = 1) {
            const searchInput = $('#searchInput').val();
            const kategoriFilter = $('#kategoriFilter').val();

            $.ajax({
                url: "{{ route('kategori.search') }}",  // Memanggil endpoint pencarian
                type: "GET",
                data: {
                    search: searchInput,
                    kategori: kategoriFilter,
                    page: page
                },
                success: function(response) {
                    let rows = '';
                    $.each(response.data, function(index, category) {
                        rows += `
                            <tr>
                                <td>${index + 1 + (page - 1) * 10}</td>
                                <td>${category.nama_kategori}</td>
                                <td>${category.keterangan || '-'}</td>
                                <td>
                                    <a href="/data-kategori/${category.id}/edit" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('data-kategori.destroy', '') }}/${category.id}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>`;
                    });

                    $('#kategoriTableBody').html(rows);

                    let pagination = '';
                    if (response.links.prev) {
                        pagination += `<a href="javascript:void(0);" class="btn btn-link" onclick="fetchCategories(${page - 1})">Prev</a>`;
                    }
                    pagination += ` <span>Halaman ${response.current_page} dari ${response.total_pages}</span> `;
                    if (response.links.next) {
                        pagination += `<a href="javascript:void(0);" class="btn btn-link" onclick="fetchCategories(${page + 1})">Next</a>`;
                    }

                    $('#pagination').html(pagination);

                    // Memperbarui URL dengan parameter pencarian dan kategori
                    history.pushState(null, '', `?search=${searchInput}&kategori=${kategoriFilter}&page=${page}`);
                },
            });
        }

        // Memanggil fungsi fetchCategories saat ada perubahan pencarian atau kategori
        $('#searchInput, #kategoriFilter').on('keyup change', function () {
            fetchCategories(1);
        });

        // Memanggil fetchCategories pertama kali dengan halaman yang ada
        fetchCategories(currentPage);
    });
</script>
