@section('title', 'Laporan Stok')

@include('layouts.partials.head')
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

<main id="main" class="main">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Laporan Stok</h5>
            </div>

            <div class="card-body">
                <form id="filterForm" class="mb-3 row">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Cari Nama Obat">
                    </div>
                    <div class="col-md-4">
                        <select name="jenis_mutasi" class="form-control">
                            <option value="">Semua Mutasi</option>
                            <option value="pembelian">Pembelian</option>
                            <option value="penjualan">Penjualan</option>
                            <option value="gudang-ke-etalase">Gudang ke Etalase</option>
                            <option value="etalase-ke-gudang">Etalase ke Gudang</option>
                            <option value="gudang-ke-apotik_cabang">Gudang ke Apotik Cabang</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>

                <table id="laporanTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Batch</th>
                            <th>Nama Obat</th>
                            <th>Satuan</th>
                            <th>Jenis Mutasi</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
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
    $(document).ready(function() {
        let currentFilters = {};

        window.loadData = function(page = 1) {
            let filters = {
                ...currentFilters,
                page
            };

            $.ajax({
                url: "{{ route('laporan-stok') }}",
                data: filters,
                dataType: "json",
                success: function(response) {
                    let tableBody = $("#laporanTable tbody");
                    tableBody.empty();

                    if (response.data.length === 0) {
                        tableBody.append(
                            `<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>`
                        );
                    } else {
                        response.data.forEach((item, index) => {
                            tableBody.append(`
                                <tr>
                                    <td>${(response.pagination.current_page - 1) * 10 + (index + 1)}</td>
                                    <td>${item.tanggal}</td>
                                    <td>${item.batch}</td>
                                    <td>${item.nama_obat}</td>
                                    <td>${item.satuan}</td>
                                    <td>${toTitleCase(item.jenis_mutasi)}</td>
                                    <td>${item.jumlah}</td>
                                </tr>
                            `);
                        });
                    }

                    let paginationHtml = `<ul class="pagination">`;

                    if (response.pagination.prev_page_url) {
                        paginationHtml += `<li class="page-item">
                            <button class="page-link" onclick="loadData(${response.pagination.current_page - 1})">« Prev</button>
                        </li>`;
                    }

                    for (let i = 1; i <= response.pagination.last_page; i++) {
                        paginationHtml += `<li class="page-item ${i === response.pagination.current_page ? 'active' : ''}">
                            <button class="page-link" onclick="loadData(${i})">${i}</button>
                        </li>`;
                    }

                    if (response.pagination.next_page_url) {
                        paginationHtml += `<li class="page-item">
                            <button class="page-link" onclick="loadData(${response.pagination.current_page + 1})">Next »</button>
                        </li>`;
                    }

                    paginationHtml += `</ul>`;
                    $('#pagination').html(paginationHtml);
                }
            });
        }

        $("#filterForm").on("submit", function(e) {
            e.preventDefault();
            currentFilters = $(this).serializeArray().reduce((obj, item) => {
                obj[item.name] = item.value;
                return obj;
            }, {});
            loadData(1);
        });

        loadData();
    });

    function toTitleCase(str) {
        return str
            .toLowerCase()
            .split(/[-_ ]+/)
            .map(word => word.charAt(0) + word.slice(1))
            .join(' ');
    }
</script>
