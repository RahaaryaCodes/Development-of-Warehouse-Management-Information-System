  @include('layouts.partials.head')
  @include('layouts.partials.navbar')
  @include('layouts.partials.sidebar')

  <main id="main" class="main">
      <div class="container mt-5">
          <div class="row justify-content-center">
              <div class="col-md-10">
                  <div class="shadow-lg card">
                      <div class="text-white card-header bg-primary d-flex justify-content-between align-items-center">
                          <h4>Detail Pemesanan</h4>
                          <div>
                              <form action="{{ route('pemesanan-barang.cetak-pdf', $pemesanan->id) }}" method="GET"
                                  class="d-inline">
                                  <button type="submit" class="btn btn-success me-2">
                                      <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
                                  </button>
                              </form>

                              <a href="{{ route('pemesanan-barang.index') }}" class="btn btn-light">
                                  <i class="bi bi-arrow-left"></i> Kembali
                              </a>
                          </div>

                      </div>
                      <div class="card-body">
                          <div class="mb-3 row">
                              <div class="col-md-6">
                                  <h5 class="card-title">Informasi Pemesanan</h5>
                                  <table class="table table-borderless">
                                      <tr>
                                          <th>Nomor Pemesanan</th>
                                          <td>{{ $pemesanan->id }}</td>
                                      </tr>
                                      <tr>
                                          <th>Tanggal Pemesanan</th>
                                          <td>{{ \Carbon\Carbon::parse($pemesanan->tanggal_pemesanan)->format('d M Y') }}
                                          </td>
                                      </tr>
                                      <tr>
                                          <th>Jenis Surat</th>
                                          <td>{{ $pemesanan->jenis_surat }}</td>
                                      </tr>
                                      <tr>
                                          <th>Status</th>
                                          <td>
                                              @php
                                                  $statusClass = match ($pemesanan->status) {
                                                      'Menunggu Konfirmasi' => 'badge bg-secondary',
                                                      'Dikirim' => 'badge bg-primary',
                                                      'Selesai' => 'badge bg-success',
                                                      'Dibatalkan' => 'badge bg-danger',
                                                      default => 'badge bg-secondary',
                                                  };
                                              @endphp
                                              <span class="{{ $statusClass }}">{{ $pemesanan->status }}</span>
                                          </td>
                                      </tr>
                                  </table>
                              </div>
                              <div class="col-md-6">
                                  <h5 class="card-title">Informasi Supplier</h5>
                                  <table class="table table-borderless">
                                      <tr>
                                          <th>Nama Supplier</th>
                                          <td>{{ $pemesanan->supplier->nama_supplier }}</td>
                                      </tr>
                                      <tr>
                                          <th>Kontak</th>
                                          <td>{{ $pemesanan->supplier->telepon ?? 'Tidak tersedia' }}</td>
                                      </tr>
                                      <tr>
                                          <th>Alamat</th>
                                          <td>{{ $pemesanan->supplier->alamat ?? 'Tidak tersedia' }}</td>
                                      </tr>
                                  </table>
                              </div>
                          </div>

                          <h5 class="card-title">Daftar Item Pemesanan</h5>
                          <table class="table table-bordered table-striped">
                              <thead>
                                  <tr>
                                      <th>No</th>

                                      @if ($pemesanan->jenis_surat == 'Psikotropika')
                                          <th>Nama Psikotropika</th>
                                      @elseif(in_array($pemesanan->jenis_surat, ['OOT', 'Prekursor']))
                                          <th>Nama Obat</th>
                                      @else
                                          {{-- Reguler --}}
                                          <th>Nama Barang</th>
                                      @endif

                                      {{-- Kolom Zat Aktif, Bentuk Sediaan, Satuan hanya untuk Psikotropika, OOT, Prekursor --}}
                                      @if (in_array($pemesanan->jenis_surat, ['Psikotropika', 'OOT', 'Prekursor']))
                                          <th>Zat Aktif</th>
                                          <th>Bentuk Sediaan</th>
                                      @endif

                                      <th>Satuan</th>
                                      <th>Jumlah</th>

                                      {{-- Keterangan hanya untuk Reguler --}}
                                      @if ($pemesanan->jenis_surat == 'Reguler')
                                          <th>Keterangan</th>
                                      @endif
                                  </tr>
                              </thead>
                              <tbody>

                                  @if (is_array($obats) || is_object($obats))
                                      @foreach ($obats as $index => $obat)
                                          <tr>
                                              <td>{{ $loop->iteration }}</td>
                                              <td>{{ $obat['nama_obat'] }}</td>
                                              @if (in_array($pemesanan->jenis_surat, ['Psikotropika', 'OOT', 'Prekursor']))
                                                  <td>{{ $obat['zat_aktif'] ?? '-' }}</td>
                                                  <td>{{ $obat['bentuk_satuan'] ?? '-' }}</td>
                                              @endif
                                              <td>{{ $obat['satuan'] ?? '-' }}</td>
                                              <td>{{ $obat['jumlah'] }}</td>
                                              @if ($pemesanan->jenis_surat == 'Reguler')
                                                  <td>{{ $obat['keterangan'] ?? '-' }}</td>
                                              @endif
                                          </tr>
                                      @endforeach
                                  @else
                                      <tr>
                                          <td colspan="6" class="text-center">Tidak ada item pemesanan</td>
                                      </tr>
                                  @endif

                              </tbody>
                          </table>
                          <!-- Menampilkan catatan pada halaman show -->
                          <div class="col-md-6">
                              <div class="alert alert-info">
                                  <strong>Catatan:</strong> <br>
                                  <p>{{ $detail->catatan ?? 'Tidak ada catatan.' }}</p>
                              </div>
                          </div>

                      </div>
                  </div>
              </div>
          </div>
      </div>
  </main>

  @include('layouts.partials.footer')
