@include('layouts.partials.head')
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

<main class="main" id="main">
  <div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Penerimaan Barang</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('penerimaan-barang.process', $pemesanan->id) }}" method="POST">
                @csrf
                @foreach($pemesanan->details as $index => $detail)
                <div class="card mb-3">
                    <div class="card-body">
                        <input type="hidden" name="items[{{$index}}][nama_obat]" value="{{ $detail->nama_obat }}">
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nama Obat</label>
                                    <input type="text" class="form-control" value="{{ $detail->nama_obat }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jumlah Dipesan</label>
                                    <input type="number" class="form-control" value="{{ $detail->jumlah }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jumlah Diterima</label>
                                    <input type="number" class="form-control" name="items[{{$index}}][jumlah_diterima]" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>No. Batch</label>
                                    <input type="text" class="form-control" name="items[{{$index}}][batch]" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal Kadaluarsa</label>
                                    <input type="date" class="form-control" name="items[{{$index}}][tanggal_kadaluarsa]" required>
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="form-group">
                                      <label>Kategori Obat</label>
                                      <select class="form-control" name="items[{{$index}}][kategori_obat]" required>
                                          @foreach($kategoris as $kategori)
                                              <option value="{{ $kategori->nama_kategori }}">{{ $kategori->nama_kategori }}</option>
                                          @endforeach
                                      </select>
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="form-group">
                                      <label>Harga Beli</label>
                                      <input type="number" class="form-control" name="items[{{$index}}][harga_beli]" required>
                                  </div>
                              </div>
                          </div>
  
                          @if($pemesanan->jenis_surat != 'Reguler')
                          <div class="row mt-3">
                              <div class="col-md-4">
                                  <div class="form-group">
                                      <label>Zat Aktif</label>
                                      <input type="text" class="form-control" value="{{ $detail->zat_aktif }}" readonly>
                                  </div>
                              </div>
                              <div class="col-md-4">
                                  <div class="form-group">
                                      <label>Bentuk Sediaan</label>
                                      <input type="text" class="form-control" value="{{ $detail->bentuk_sediaan }}" readonly>
                                  </div>
                              </div>
                              <div class="col-md-4">
                                  <div class="form-group">
                                      <label>Satuan</label>
                                      <input type="text" class="form-control" value="{{ $detail->satuan }}" readonly>
                                  </div>
                              </div>
                          </div>
                          @endif
                      </div>
                  </div>
                  @endforeach
  
                  <button type="submit" class="btn btn-primary">Proses Penerimaan</button>
                  <a href="{{ route('penerimaan-barang.index') }}" class="btn btn-secondary">Kembali</a>
              </form>
          </div>
      </div>
  </div>
</main>