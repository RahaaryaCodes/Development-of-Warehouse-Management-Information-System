@section('title', 'Edit Pemesanan')

@include('layouts.partials.head')

<!-- Navbar -->
@include('layouts.partials.navbar')

<!-- Sidebar -->
@include('layouts.partials.sidebar')

<main class="main" id="main">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Edit Pemesanan</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('pemesanan-barang.update', $pemesanan->id) }}" method="POST" id="editPemesananForm">
                    @csrf
                    @method('PUT')
                 
                    
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="tanggal_pemesanan" class="form-label">Tanggal Pemesanan</label>
                            <input type="date" class="form-control" id="tanggal_pemesanan" name="tanggal_pemesanan" 
                                value="{{ old('tanggal_pemesanan', $pemesanan->tanggal_pemesanan) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="supplier_id" class="form-label">Supplier</label>
                            <select class="form-select" id="supplier_id" name="supplier_id" required>
                                <option value="">Pilih Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $pemesanan->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->nama_supplier }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="jenis_surat" class="form-label">Jenis Surat</label>
                            <select class="form-select" id="jenis_surat" name="jenis_surat" required>
                                <option value="">Pilih Jenis Surat</option>
                                <option value="Reguler" {{ old('jenis_surat', $pemesanan->jenis_surat) == 'Reguler' ? 'selected' : '' }}>Reguler</option>
                                <option value="Psikotropika" {{ old('jenis_surat', $pemesanan->jenis_surat) == 'Psikotropika' ? 'selected' : '' }}>Psikotropika</option>
                                <option value="OOT" {{ old('jenis_surat', $pemesanan->jenis_surat) == 'OOT' ? 'selected' : '' }}>OOT</option>
                                <option value="Prekursor" {{ old('jenis_surat', $pemesanan->jenis_surat) == 'Prekursor' ? 'selected' : '' }}>Prekursor</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3">{{ old('catatan', $pemesanan->catatan) }}</textarea>
                    </div>

                    <!-- Detail Obat yang Dipesan -->
                    <h5 class="mt-4 mb-3">Detail Obat</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" id="detailTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Obat</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="detailTableBody">
                                @php
                                    $detailPemesanan = $pemesanan->detailPemesanan->first();
                                    $existingObats = $detailPemesanan ? json_decode($detailPemesanan->obats, true) : [];
                                @endphp
                                
                                @if(!empty($existingObats))
                                    @foreach($existingObats as $index => $obat)
                                    <tr class="detail-row">
                                        <td>
                                            <input type="text" name="obats[{{ $index }}][nama_obat]" class="form-control" 
                                                value="{{ $obat['nama_obat'] ?? '' }}" required>
                                        </td>
                                        <td>
                                            <input type="number" name="obats[{{ $index }}][jumlah]" class="form-control" 
                                                value="{{ $obat['jumlah'] ?? 1 }}" min="1" required>
                                        </td>
                                        <td>
                                            <input type="text" name="obats[{{ $index }}][satuan]" class="form-control" 
                                                value="{{ $obat['satuan'] ?? '' }}">
                                        </td>
                                        <td>
                                            <input type="text" name="obats[{{ $index }}][keterangan]" class="form-control" 
                                                value="{{ $obat['keterangan'] ?? '' }}">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr class="detail-row">
                                        <td>
                                            <input type="text" name="obats[0][nama_obat]" class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="number" name="obats[0][jumlah]" class="form-control" value="1" min="1" required>
                                        </td>
                                        <td>
                                            <input type="text" name="obats[0][satuan]" class="form-control">
                                        </td>
                                        <td>
                                            <input type="text" name="obats[0][keterangan]" class="form-control">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-info" id="addRowBtn">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Obat
                        </button>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('pemesanan-barang.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

@include('layouts.partials.footer')

<script>
    $(document).ready(function() {
        // Counter for adding new rows
        let rowCounter = $('.detail-row').length;
        
        // Tambah baris baru
        $('#addRowBtn').click(function() {
            let newIndex = rowCounter++;
            let rowHtml = `
                <tr class="detail-row">
                    <td>
                        <input type="text" name="obats[${newIndex}][nama_obat]" class="form-control" required>
                    </td>
                    <td>
                        <input type="number" name="obats[${newIndex}][jumlah]" class="form-control" value="1" min="1" required>
                    </td>
                    <td>
                        <input type="text" name="obats[${newIndex}][satuan]" class="form-control">
                    </td>
                    <td>
                        <input type="text" name="obats[${newIndex}][keterangan]" class="form-control">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            $('#detailTableBody').append(rowHtml);
        });
        
        // Hapus baris
        $(document).on('click', '.remove-row', function() {
            const rowCount = $('.detail-row').length;
            
            if (rowCount > 1) {
                $(this).closest('tr').remove();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Minimal harus ada satu item obat!'
                });
            }
        });
        
        // Form submit validation
        $('#editPemesananForm').submit(function(e) {
            const rowCount = $('.detail-row').length;
            
            if (rowCount === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Minimal harus ada satu item obat!'
                });
                return false;
            }
            
            // Validasi semua obat harus diisi
            let isValid = true;
            $('input[name$="[nama_obat]"]').each(function() {
                if ($(this).val() === '') {
                    isValid = false;
                    return false;
                }
            });
            
            $('input[name$="[jumlah]"]').each(function() {
                if ($(this).val() === '' || parseInt($(this).val()) < 1) {
                    isValid = false;
                    return false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Semua field obat dan jumlah harus diisi dengan benar!'
                });
                return false;
            }
        });
        
        // SweetAlert notification if exists
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 1500,
                showConfirmButton: false
            });
        @endif
        
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}'
            });
        @endif
    });
</script>