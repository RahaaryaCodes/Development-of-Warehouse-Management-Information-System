@section('title', 'Konversi Satuan')

@include('layouts.partials.head')
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

<main class="main" id="main">
    <div class="container">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div class="mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari konversi...">
            </div>
            <a href="{{ route('konversi-satuan.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Tambah Konversi
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Dari Satuan</th>
                        <th>Ke Satuan</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="konversiTableBody"></tbody>
            </table>
        </div>

        <div id="pagination" class="mt-3 d-flex justify-content-center"></div>
    </div>
</main>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data konversi ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Hapus</button>
            </div>
        </div>
    </div>
</div>

@include('layouts.partials.footer')

<script>
    $(document).ready(function() {
        function fetchKonversi(page = 1) {
            const searchInput = $('#searchInput').val();

            $.ajax({
                url: "{{ route('konversi-satuan.index') }}",
                type: "GET",
                data: {
                    search: searchInput,
                    page: page
                },
                success: function(response) {
                    if (response.data && response.data.length > 0) {
                        let rows = '';
                        $.each(response.data, function(index, konversi) {
                            rows += `
                            <tr>
                                <td>${index + 1 + (page - 1) * 10}</td>
                                <td>${konversi.satuan_dari.nama_satuan}</td>
                                <td>${konversi.satuan_ke.nama_satuan}</td>
                                <td>${konversi.jumlah}</td>
                                <td>
                                    <div class="gap-1 d-inline-flex">
                                        <a href="/konversi-satuan/${konversi.id}/edit" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="openDeleteModal(${konversi.id})">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>`;
                        });
                        $('#konversiTableBody').html(rows);

                        let paginationButtons = `<div class="btn-group" role="group">`;
                        if (response.pagination.prev_page_url) {
                            paginationButtons +=
                                `<button class="btn btn-outline-primary" onclick="fetchKonversi(${page - 1})"><i class="bi bi-arrow-left"></i> Prev</button>`;
                        }

                        for (let i = 1; i <= response.pagination.last_page; i++) {
                            paginationButtons +=
                                `<button class="btn ${i === page ? 'btn-primary' : 'btn-outline-primary'}" onclick="fetchKonversi(${i})">${i}</button>`;
                        }

                        if (response.pagination.next_page_url) {
                            paginationButtons +=
                                `<button class="btn btn-outline-primary" onclick="fetchKonversi(${page + 1})">Next <i class="bi bi-arrow-right"></i></button>`;
                        }

                        paginationButtons += `</div>`;
                        $('#pagination').html(paginationButtons);

                        history.pushState(null, '', `?search=${searchInput}&page=${page}`);
                    } else {
                        $('#konversiTableBody').html(
                            '<tr><td colspan="5" class="text-center">Data tidak ditemukan</td></tr>'
                        );
                        $('#pagination').html('');
                    }
                }
            });
        }

        // Delete modal functionality
        let konversiIdToDelete;
        window.openDeleteModal = function(konversiId) {
            konversiIdToDelete = konversiId;
            $('#deleteModal').modal('show');
        };

        $('#confirmDeleteButton').on('click', function() {
            $.ajax({
                url: `/konversi-satuan/${konversiIdToDelete}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    Swal.fire('Berhasil!', response.message, 'success');
                    fetchKonversi();
                }
            });
        });

        // Initialize table and search functionality
        fetchKonversi();

        $('#searchInput').on('keyup', function() {
            fetchKonversi();
        });
    });
</script>
