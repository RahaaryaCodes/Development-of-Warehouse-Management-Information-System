@include('layouts.partials.head')
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

<main class="main" id="main">
  <div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Daftar Pesanan Menunggu Penerimaan</h4>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Pesanan</th>
                        <th>Supplier</th>
                        <th>Jenis Surat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pemesanans as $index => $pemesanan)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $pemesanan->tanggal_pemesanan }}</td>
                        <td>{{ $pemesanan->supplier->nama_supplier }}</td>
                        <td>{{ $pemesanan->jenis_surat }}</td>
                        <td>{{ $pemesanan->status }}</td>
                        <td>
                            <a href="{{ route('penerimaan-barang.show', $pemesanan->id) }}" 
                               class="btn btn-primary btn-sm">Proses Penerimaan</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</main>