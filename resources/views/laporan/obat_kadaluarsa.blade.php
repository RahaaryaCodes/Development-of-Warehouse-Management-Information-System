@section('title', 'Laporan Obat Kadaluarsa')

@include('layouts.partials.head')
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

<main id="main" class="main">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Laporan Obat Kadaluarsa</h5>
            </div>

            <div class="card-body">
                <form id="filterForm">
                    <div class="mb-3 row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Cari Nama Obat">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>

                <table id="obatKadaluarsaTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Obat Kadaluarsa</th>
                            <th>Nama Obat</th>
                            <th>Batch</th>
                            <th>Satuan</th>
                            <th>Stok Etalase</th>
                            <th>Stok Gudang</th>
                            <th>Stok Sisa Eceran</th>
                            <th>Stok Apotik</th>
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

        function loadData(page = 1) {
            let filters = {
                ...currentFilters,
                page
            };

            $.ajax({
                url: "{{ route('laporan-obat-kadaluarsa') }}",
                data: filters,
                dataType: "json",
                success: function(response) {

                    let tableBody = $("#obatKadaluarsaTable tbody");
                    tableBody.empty();

                    if (response.data.length === 0) {
                        tableBody.append(
                            `<tr><td colspan="10" class="text-center">Tidak ada data</td></tr>`
                        );
                    } else {
                        response.data.forEach((item, index) => {
                            tableBody.append(`
                                <tr>
                                    <td>${(response.pagination.current_page - 1) * 10 + (index + 1)}</td>
                                    <td>${item.tanggal_kadaluarsa}</td>
                                    <td>${item.nama_obat}</td>
                                    <td>${item.batch}</td>
                                    <td>${item.satuan}</td>
                                    <td>${item.stok_etalase}</td>
                                    <td>${item.stok_gudang}</td>
                                    <td>${item.stok_sisa_eceran}</td>
                                    <td>${item.stok_apotik_cabang}</td>
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
</script>
