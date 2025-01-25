@section('title', 'Daftar Obat') 

@include('layouts.partials.head')

<!-- Navbar -->
@include('layouts.partials.navbar')

<!-- Sidebar -->
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
                        @foreach($drugs as $index => $drug)
                            <tr>
                                <td>{{ $index + 1 + ($drugs->currentPage() - 1) * $drugs->perPage() }}</td>
                                <td>{{ $drug->batch }}</td>
                                <td>{{ $drug->nama_obat }}</td>
                                <td>{{ $drug->kategori_obat }}</td>
                                <td>{{ $drug->jenis_obat }}</td>
                                <td>{{ $drug->satuan }}</td>
                                <td><span class="badge {{ $drug->stok <= $drug->stok_minimum ? 'bg-danger' : 'bg-success' }}">{{ $drug->stok }}</span></td>
                                <td>Rp {{ number_format($drug->harga_beli, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($drug->harga_jual, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($drug->tanggal_kadaluarsa)->format('d-m-Y') }}</td>
                                <td>
                                    <a href="{{ route('data-obat.edit', $drug->id) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i> 
                                    </a>
                                    <form action="{{ route('data-obat.destroy', $drug->id) }}" method="POST" style="display:inline;">
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
                    {{ $drugs->appends(request()->query())->links('pagination::bootstrap-5') }}
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

        function fetchDrugs(page = 1) {
            const searchInput = $('#searchInput').val();
            const kategoriFilter = $('#kategoriFilter').val();

            $.ajax({
                url: "{{ route('data-obat.search') }}",  // Memanggil endpoint pencarian
                type: "GET",
                data: {
                    search: searchInput,
                    kategori: kategoriFilter,
                    page: page
                },
                success: function(response) {
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
                                <td>Rp ${new Intl.NumberFormat('id-ID').format(drug.harga_beli)}</td>
                                <td>Rp ${new Intl.NumberFormat('id-ID').format(drug.harga_jual)}</td>
                                <td>${new Date(drug.tanggal_kadaluarsa).toLocaleDateString('id-ID')}</td>
                                <td>
                                    <a href="/data-obat/${drug.id}/edit" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('data-obat.destroy', '') }}/${drug.id}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>`;
                    });

                    $('#drugsTableBody').html(rows);

                    let pagination = '';
                    if (response.links.prev) {
                        pagination += `<a href="javascript:void(0);" class="btn btn-link" onclick="fetchDrugs(${page - 1})">Prev</a>`;
                    }
                    pagination += ` <span>Halaman ${response.current_page} dari ${response.total_pages}</span> `;
                    if (response.links.next) {
                        pagination += `<a href="javascript:void(0);" class="btn btn-link" onclick="fetchDrugs(${page + 1})">Next</a>`;
                    }

                    $('#pagination').html(pagination);

                    // Memperbarui URL dengan parameter pencarian dan kategori
                    history.pushState(null, '', `?search=${searchInput}&kategori=${kategoriFilter}&page=${page}`);
                },
            });
        }

        // Memanggil fungsi fetchDrugs saat ada perubahan pencarian atau kategori
        $('#searchInput, #kategoriFilter').on('keyup change', function () {
            fetchDrugs(1);
        });

        // Memanggil fetchDrugs pertama kali dengan halaman yang ada
        fetchDrugs(currentPage);
    });
</script>
