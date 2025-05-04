@section('title', 'Laporan Penjualan')

@include('layouts.partials.head')
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

<main id="main" class="main">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Laporan Penjualan</h5>
            </div>

            <div class="card-body">
                <form id="filterForm">
                    <div class="mb-3 row">
                        <div class="col-md-3">
                            <input type="date" name="search" class="form-control" placeholder="Cari Tanggal">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>

                <table id="penjualanTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Penjualan</th>
                            <th>Jumlah Transaksi</th>
                            <th>Total Harga</th>
                            <th>Aksi</th>
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

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Obat</th>
                            <th>Satuan</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="detailTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


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
                url: "{{ route('laporan-penjualan') }}",
                data: filters,
                dataType: "json",
                success: function(response) {
                    let tableBody = $("#penjualanTable tbody");
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
                                    <td>${item.tanggal_penjualan}</td>
                                    <td>${item.jumlah_transaksi}</td>
                                    <td>Rp ${item.total_harga}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm" onclick="showDetail(${item.id})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
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

        // Event listener untuk filter form
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

    function showDetail(id) {
        $.ajax({
            url: `/detail-penjualan/${id}`,
            type: "GET",
            dataType: "json",
            success: function(response) {
                let tableBody = $("#detailTableBody");
                tableBody.empty();

                if (response.data.length === 0) {
                    tableBody.append(
                        `<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>`
                    );
                } else {
                    response.data.forEach((item) => {
                        tableBody.append(`
                        <tr>
                            <td>${item.nama_obat}</td>
                            <td>${item.satuan}</td>
                            <td>${item.jumlah}</td>
                            <td>${item.harga_satuan}</td>
                            <td>${item.subtotal}</td>
                        </tr>
                    `);
                    });
                }

                $("#detailModal").modal("show");
            }
        });
    }
</script>
