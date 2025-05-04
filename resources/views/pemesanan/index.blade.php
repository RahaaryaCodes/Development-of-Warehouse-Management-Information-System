@section('title', 'Daftar Pemesanan')

@include('layouts.partials.head')

<!-- Navbar -->
@include('layouts.partials.navbar')

<!-- Sidebar -->
@include('layouts.partials.sidebar')

<main class="main" id="main">
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Bagian Form Pencarian -->
        <form action="{{ route('pemesanan-barang.index') }}" method="GET">
            <div class="mb-4 row">
                <div class="col-md-4">
                    <div class="search-box">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari supplier..."
                                name="search" value="{{ request('search') }}">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <select id="jenisSuratFilter" class="form-select" name="jenis_surat">
                        <option value="">Filter Jenis Surat</option>
                        <option value="Reguler" {{ request('jenis_surat') == 'Reguler' ? 'selected' : '' }}>Reguler
                        </option>
                        <option value="Psikotropika" {{ request('jenis_surat') == 'Psikotropika' ? 'selected' : '' }}>
                            Psikotropika</option>
                        <option value="OOT" {{ request('jenis_surat') == 'OOT' ? 'selected' : '' }}>OOT</option>
                        <option value="Prekursor" {{ request('jenis_surat') == 'Prekursor' ? 'selected' : '' }}>
                            Prekursor</option>
                    </select>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('pemesanan-barang.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Pemesanan
                    </a>
                </div>
            </div>
        </form>

        <!-- Table Section -->
        <div class="table">
            <div class="table-responsive">
                <table class="table align-middle table-hover table-striped">
                    <thead class="text-center table-primary">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Pemesanan</th>
                            <th>Supplier</th>
                            <th>Jenis Surat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="pemesananTableBody">
                        <!-- Data akan dimuat via AJAX -->
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

<!-- Modal Input Detail Penerimaan -->
<div class="modal fade" id="detailPenerimaanModal" tabindex="-1" aria-labelledby="detailPenerimaanModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-fullscreen"> <!-- Changed from modal-xl to modal-fullscreen -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailPenerimaanModalLabel">Input Detail Penerimaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="detailPenerimaanForm">
                    @csrf
                    <input type="hidden" id="pemesananId" name="pemesanan_id">

                    <!-- Tabel Data Pemesanan -->
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="min-width: 50px;">No</th>
                                    <th style="min-width: 180px;">Nama Barang</th>
                                    <th style="min-width: 180px;">Obat</th>
                                    <th style="min-width: 100px;">Batch</th>
                                    <th style="min-width: 120px;">No Faktur</th>
                                    <th style="min-width: 80px;">Jumlah</th>
                                    <th style="min-width: 80px;">Satuan</th>
                                    <th style="min-width: 120px;">Harga Beli</th>
                                    <th style="min-width: 120px;">Harga Jual</th>
                                    <th style="min-width: 120px;">Total Harga</th>
                                    <th style="min-width: 80px;">Diskon</th>
                                    <th style="min-width: 80px;">PPN</th>
                                    <th style="min-width: 150px;">Tgl Kadaluarsa</th>
                                    <th style="min-width: 150px;">Tgl Diterima</th>
                                    <th style="min-width: 150px;">Lokasi Gudang</th>
                                    <th style="min-width: 150px;">Lokasi Etalase</th>
                                    <th style="min-width: 150px;">Zat Aktif</th>
                                    <th style="min-width: 100px;">Bentuk</th>
                                    <th style="min-width: 150px;">Keterangan</th>
                                    <th style="min-width: 150px;">Catatan</th>
                                </tr>
                            </thead>
                            <tbody id="detailPemesananTableBody">
                                <!-- Data diisi lewat JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">Simpan Detail</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data pemesanan ini?</p>
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
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 1000,
            showConfirmButton: false
        });
    @endif

    // Fungsi untuk membuka modal delete
    let pemesananIdToDelete;

    function openDeleteModal(pemesananId) {
        pemesananIdToDelete = pemesananId;
        $('#deleteModal').modal('show');
    }

    // Fungsi untuk menghapus data pemesanan
    $('#confirmDeleteButton').on('click', function() {
        $.ajax({
            url: `/pemesanan-barang/${pemesananIdToDelete}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}',
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                Swal.fire('Berhasil!', response.message, 'success');
                fetchPemesanan(); // Perbarui tabel setelah penghapusan
            },
            error: function() {
                Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data.', 'error');
            }
        });
    });

    // Function to load pemesanan data via AJAX
    function fetchPemesanan(page = 1) {
        const searchInput = $('#searchInput').val();
        const jenisSuratFilter = $('#jenisSuratFilter').val();

        $.ajax({
            url: "{{ route('pemesanan-barang.index') }}",
            type: "GET",
            data: {
                search: searchInput,
                jenis_surat: jenisSuratFilter,
                page: page
            },
            success: function(response) {

                if (response.data && response.data.length > 0) {
                    let rows = '';
                    $.each(response.data, function(index, pemesanan) {
                        let statusOptions = `
                        <select class="text-center form-select form-select-sm" onchange="updateStatus(${pemesanan.id}, this.value)">
                            <option value="Pending" ${pemesanan.status === 'Pending' ? 'selected' : ''}>Pending</option>
                            <option value="Diterima" ${pemesanan.status === 'Diterima' ? 'selected' : ''}>Diterima</option>
                            <option value="Dibatalkan" ${pemesanan.status === 'Dibatalkan' ? 'selected' : ''}>Dibatalkan</option>
                        </select>
                    `;

                        rows += `
                        <tr>
                            <td class="text-center">${index + 1 + (page - 1) * 10}</td>
                            <td class="text-center">${pemesanan.tanggal_pemesanan}</td>
                            <td>${pemesanan.supplier}</td>
                            <td class="text-center">${pemesanan.jenis_surat}</td>
                            <td class="text-center">${statusOptions}</td>
                            <td class="text-center" style="white-space: nowrap;">
                                <a href="/pemesanan-barang/${pemesanan.id}/edit" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="openDeleteModal(${pemesanan.id})">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <a href="/pemesanan-barang/${pemesanan.id}" class="btn btn-info btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                    });

                    $('#pemesananTableBody').html(rows);

                    // Pagination
                    let paginationButtons = `<div class="btn-group" role="group">`;

                    if (response.pagination.prev_page_url) {
                        paginationButtons += `
                        <button class="btn btn-outline-primary" onclick="fetchPemesanan(${page - 1})">
                            <i class="bi bi-arrow-left"></i> Prev
                        </button>`;
                    }

                    for (let i = 1; i <= response.pagination.last_page; i++) {
                        paginationButtons += `
                        <button class="btn ${i === page ? 'btn-primary' : 'btn-outline-primary'}" onclick="fetchPemesanan(${i})">
                            ${i}
                        </button>`;
                    }

                    if (response.pagination.next_page_url) {
                        paginationButtons += `
                        <button class="btn btn-outline-primary" onclick="fetchPemesanan(${page + 1})">
                            Next <i class="bi bi-arrow-right"></i>
                        </button>`;
                    }

                    paginationButtons += `</div>`;
                    $('#pagination').html(paginationButtons);

                    // Update URL without reloading the page
                    history.pushState(null, '',
                        `?search=${searchInput}&jenis_surat=${jenisSuratFilter}&page=${page}`);
                } else {
                    $('#pemesananTableBody').html(
                        '<tr><td colspan="6" class="text-center">Data tidak ditemukan</td></tr>');
                    $('#pagination').html('');
                }
            },
            error: function() {
                $('#pemesananTableBody').html(
                    '<tr><td colspan="6" class="text-center text-danger">Terjadi kesalahan saat memuat data</td></tr>'
                );
            }
        });
    }

    // Fungsi untuk memperbarui status pemesanan
    function updateStatus(id, status) {
        if (status === 'Diterima') {
            $('#pemesananId').val(id);
            $('#detailPenerimaanModal').modal('show');

            openDetailPenerimaanModal(id);
        } else {
            $.ajax({
                url: `/pemesanan-barang/${id}/update-status`,
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil",
                            text: "Status berhasil diperbarui!"
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal",
                            text: "Gagal memperbarui status, coba lagi."
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Terjadi kesalahan pada server!"
                    });
                    console.error(xhr.responseText);
                }
            });
        }
    }

    function openDetailPenerimaanModal(id) {
        $.ajax({
            url: `/pemesanan-barang/${id}/get-obat`,
            type: "GET",
            success: function(response) {

                let obatsArray = Object.values(response.obats);

                window.konversiObat = response.konversi || {};

                if (response.success && Array.isArray(obatsArray)) {
                    $('#pemesananId').val(id);

                    let tbody = $('#detailPemesananTableBody');
                    tbody.empty();

                    if (obatsArray.length > 0) {
                        obatsArray.forEach((obat, index) => {
                            tbody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${obat.nama_obat}</td>
                              <td>
                        <select name="obat_id[]" class="form-select obat-select" data-index="${index}">
                            <option value="">Pilih Obat</option>
                            ${response.drugs?.map(drug => `
                                <option value="${drug.id}">${drug.nama_obat}</option>
                            `).join('') || ''}
                        </select>
                    </td>
                                <td><input type="text" name="batch[]" class="form-control" required></td>
                                <td><input type="text" name="no_faktur[]" class="form-control" required></td>
                                <td><input type="number" name="jumlah_terima[]" class="form-control jumlah-terima" min="1" required></td>
                                <td>
                        <select name="nama_satuan[]" class="form-control satuan-select" required>
                            <option value="" disabled selected>Pilih Satuan</option>
                        </select>
                    </td>
                                <td><input type="number" name="harga_beli[]" class="form-control harga-beli" step="0.01" min="0" required></td>
                                <td><input type="number" name="harga_jual[]" class="form-control" step="0.01" min="0" required></td>
                                <td><input type="number" name="total_harga[]" class="form-control total-harga" readonly></td>
                                <td><input type="number" name="diskon[]" class="form-control" step="0.01" min="0"></td>
                                <td><input type="number" name="ppn[]" class="form-control" step="0.01" min="0"></td>
                                <td><input type="date" name="tanggal_kadaluarsa[]" class="form-control" required></td>
                                <td><input type="date" name="tanggal_diterima[]" class="form-control" required></td>
                                <td><input type="number" name="stok_gudang[]" class="form-control stok-gudang" min="0" required placeholder="Sisa: 0"></td>
                                <td><input type="number" name="stok_etalase[]" class="form-control stok-etalase" min="0" required placeholder="Sisa: 0"></td>
                                <td><input type="text" name="zat_aktif[]" class="form-control"></td>
                                <td><input type="text" name="bentuk_sediaan[]" class="form-control"></td>
                                <td><textarea name="keterangan[]" class="form-control"></textarea></td>
                                <td><textarea name="catatan[]" class="form-control"></textarea></td>
                            </tr>
                        `);
                        });

                        $(".obat-select").on("change", function() {
                            let selectedOption = $(this).find(":selected");
                            let index = $(this).data("index");
                            let satuanSelect = $(`.satuan-select:eq(${index})`);

                            let obatId = selectedOption.val();

                            let konversiData = window.konversiObat?.[obatId] || [];

                            satuanSelect.empty().append(
                                `<option value="" disabled selected>Pilih Satuan</option>`);

                            konversiData.forEach(konversi => {
                                satuanSelect.append(`
                                    <option value="${konversi.id}" data-konversi="${konversi.jumlah_satuan_terkecil}">
                                        ${konversi.nama_satuan}
                                    </option>
                                `);
                            });
                        });

                        // Hitung total harga otomatis
                        $(".jumlah-terima, .harga-beli").on("input", function() {
                            let tr = $(this).closest("tr");
                            let jumlah = parseFloat(tr.find(".jumlah-terima").val()) || 0;
                            let hargaBeli = parseFloat(tr.find(".harga-beli").val()) || 0;
                            let totalHarga = jumlah * hargaBeli;
                            tr.find(".total-harga").val(totalHarga.toFixed(0));
                        });

                        // Validasi stok gudang & etalase
                        $(".stok-gudang, .stok-etalase, .jumlah-terima").on("input", function() {
                            let tr = $(this).closest("tr");
                            let jumlah = parseInt(tr.find(".jumlah-terima").val()) || 0;
                            let gudang = parseInt(tr.find(".stok-gudang").val()) || 0;
                            let etalase = parseInt(tr.find(".stok-etalase").val()) || 0;
                            let sisa = jumlah - (gudang + etalase);

                            tr.find(".stok-gudang").attr("placeholder", "Sisa: " + sisa);
                            tr.find(".stok-etalase").attr("placeholder", "Sisa: " + sisa);

                            if (sisa < 0) {
                                alert(
                                    "Total stok di Gudang dan Etalase tidak boleh melebihi jumlah yang diterima!"
                                );
                                $(this).val("");
                            }
                        });

                        $('#detailPenerimaanModal').modal('show');
                    } else {
                        tbody.append(
                            `<tr><td colspan="19" class="text-center">Tidak ada data obat</td></tr>`);
                    }
                } else {
                    alert('Gagal mengambil data pemesanan.');
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat mengambil data.');
            }
        });
    }

    $('#detailPenerimaanForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: "/detail-penerimaan/store",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    $('#detailPenerimaanModal').modal('hide');
                    fetchPemesanan();
                    window.location.href = "/pemesanan-barang";
                });
            },
            error: function(xhr) {
                let errorMessage = "Terjadi kesalahan saat menyimpan detail penerimaan.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: errorMessage
                });
            }
        });
    });

    // Event listener for search input
    $('#searchInput').on('keyup', function() {
        fetchPemesanan();
    });

    // Event listener for jenis_surat filter
    $('#jenisSuratFilter').on('change', function() {
        fetchPemesanan(1);
    });

    // Initialize table on page load
    $(document).ready(function() {
        fetchPemesanan();
    });
</script>
