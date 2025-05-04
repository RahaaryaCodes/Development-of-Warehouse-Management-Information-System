@section('title', 'Stok Etalase')

@include('layouts.partials.head')
<style>
    th {
        white-space: nowrap;
    }
</style>
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

<main id="main" class="main">
    <div class="container">

        <!-- Search Section -->
        <form action="{{ route('stok-etalase') }}" method="GET">
            <div class="mb-4 row">
                <div class="col-md-4">
                    <div class="search-box">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari obat..."
                                name="search" value="{{ request('search') }}">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <select id="kategoriFilter" class="form-select" name="kategori">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori }}"
                                {{ request('kategori') == $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <!-- Table Section -->
        <div class="table">
            <div class="table-responsive">
                <table class="table align-middle table-hover table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Batch</th>
                            <th>Nama Obat</th>
                            <th>Kategori</th>
                            <th>Jenis</th>
                            <th>Satuan</th>
                            <th>Stok</th>
                            <th>Sisa Stok Eceran</th>
                            <th>Tanggal Kadaluarsa</th>
                        </tr>
                    </thead>
                    <tbody id="stokEtalaseTableBody">
                        <!-- Data akan dimuat melalui AJAX -->
                    </tbody>
                </table>

                <!-- Pagination -->
                <div id="pagination" class="mt-3 d-flex justify-content-center">
                    <!-- Pagination buttons akan dimuat melalui AJAX -->
                </div>

            </div>
        </div>

    </div>
</main>

@include('layouts.partials.footer')

<script>
    // Fungsi untuk memuat data tabel dengan AJAX
    function fetchStokEtalase(page = 1) {
        const searchInput = $('#searchInput').val();
        const kategoriFilter = $('#kategoriFilter').val();

        $.ajax({
            url: "{{ route('stok-etalase') }}",
            type: "GET",
            data: {
                search: searchInput,
                kategori: kategoriFilter,
                page: page
            },
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    let rows = '';
                    $.each(response.data, function(index, stok) {
                        let stokEtalaseDalamSatuanDasar = stok.stok_etalase / stok.konversi_satuan
                            .jumlah_satuan_terkecil;
                        rows += `
                            <tr>
                                <td>${index + 1 + (page - 1) * 10}</td>
                                <td>${stok.batch}</td>
                                <td>${stok.drug.nama_obat}</td>
                                <td>${stok.drug.kategori_obat}</td>
                                <td>${stok.drug.jenis_obat}</td>
                                <td>${stok.konversi_satuan.nama_satuan}</td>
                               <td>
                                    <span class="badge ${stokEtalaseDalamSatuanDasar == 0 ? 'bg-danger' : 'bg-primary'}">
                                        ${stokEtalaseDalamSatuanDasar}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge ${stok.stok_sisa_eceran == 0 ? 'bg-danger' : 'bg-primary'}">
                                        ${stok.stok_sisa_eceran}
                                    </span>
                                </td>
                                <td>${stok.tanggal_kadaluarsa}</td>
                            </tr>`;
                    });
                    $('#stokEtalaseTableBody').html(rows);

                    // Update pagination buttons
                    let paginationButtons = `<div class="btn-group" role="group">`;

                    if (response.pagination.prev_page_url) {
                        paginationButtons += `
                            <button class="btn btn-outline-primary" onclick="fetchStokEtalase(${page - 1})">
                                <i class="bi bi-arrow-left"></i> Prev
                            </button>`;
                    }

                    for (let i = 1; i <= response.pagination.last_page; i++) {
                        paginationButtons += `
                            <button class="btn ${i === page ? 'btn-primary' : 'btn-outline-primary'}" onclick="fetchStokEtalase(${i})">
                                ${i}
                            </button>`;
                    }

                    if (response.pagination.next_page_url) {
                        paginationButtons += `
                            <button class="btn btn-outline-primary" onclick="fetchStokEtalase(${page + 1})">
                                Next <i class="bi bi-arrow-right"></i>
                            </button>`;
                    }

                    paginationButtons += `</div>`;
                    $('#pagination').html(paginationButtons);

                    // Update URL tanpa reload halaman
                    history.pushState(null, '',
                        `?search=${searchInput}&kategori=${kategoriFilter}&page=${page}`);
                } else {
                    // Tampilkan pesan jika tidak ada data
                    $('#stokEtalaseTableBody').html(
                        '<tr><td colspan="11" class="text-center">Data tidak ditemukan</td></tr>');
                    $('#pagination').html('');
                }
            }
        });
    }

    // Inisialisasi tabel saat halaman dimuat
    $(document).ready(function() {
        // Panggil fungsi fetchStokEtalase() pertama kali
        fetchStokEtalase();

        // Event listener untuk live search
        $('#searchInput').on('keyup', function() {
            fetchStokEtalase();
        });

        // Event untuk filter kategori live
        $('#kategoriFilter').on('change', function() {
            fetchStokEtalase(1);
        });
    });
</script>
