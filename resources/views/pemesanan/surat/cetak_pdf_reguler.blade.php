<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Pemesanan</title>
    <style>
        @page {
            size: A4;
            margin-bottom: 30px;
            header: html_header;
        }

        body {
            font-family: sans-serif;
        }

        /* Kop Surat */
        .kop-surat {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-surat td {
            border: none;
        }

        .kop-surat .logo {
            width: 50px;
        }

        .kop-surat .logo img {
            width: 95px;
            height: auto;
        }

        .kop-surat .text {
            padding-left: 10px;
        }

        .kop-surat .info {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-surat .info td {
            padding: 2px 5px;
            vertical-align: top;
            font-size: 14px;
        }

        .kop-surat .info td:nth-child(2) {
            width: 10px;
            text-align: center;
        }

        /* Kop Garis */
        .kop-garis {
            border-top: 0.5px solid black;
            margin: 0px;
        }

        /* Title Surat */
        .title-surat {
            margin: 10px 0px;
            text-align: center;
            font-size: 18px;
            text-transform: uppercase;
        }

        /* Surat Info */
        .surat-info {
            font-size: 14px;
        }

        .surat-info p {
            margin: 5px 0;
        }

        /* Tabel Pesanan */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            border: 1px solid black;
            padding: 8px;
            font-size: 14px;
            text-align: center;
        }

        td {
            border: 1px solid black;
            padding: 8px;
            font-size: 14px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Tanda Tangan */
        .tanda-tangan {
            width: 100%;
            margin-top: 30px;
        }

        .tanda-tangan td {
            width: 50%;
            border: none;
            vertical-align: top;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>

<body>
    <!-- Template Header -->
    <htmlpageheader name="header">
        <table class="kop-surat">
            <tr>
                <td class="logo">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(base_path('public/templates/NiceAdmin/assets/img/logo_apotik_baru.png'))) }}"
                        alt="Logo Apotek">
                </td>
                <td class="text">
                    <table class="info">
                        <tr>
                            <td colspan="3">
                                <strong style="font-size: 16px">APOTEK CINTA SEHAT 24</strong>
                            </td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>:</td>
                            <td>Jl. H. Amir Machmud No. 782 Cimahi Telp (022) 6626592</td>
                        </tr>
                        <tr>
                            <td>Izin Apotek</td>
                            <td>:</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Apoteker</td>
                            <td>:</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>SIPA</td>
                            <td>:</td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <hr class="kop-garis">
    </htmlpageheader>

    <!-- Isi Laporan -->
    <div class="content">
        <h2 class="title-surat">Surat Pesanan {{ ucfirst(strtolower($pemesanan->jenis_surat)) }}</h2>

        <div class="surat-info">
            <p>
                <strong>Nomor SP:</strong>
            </p>
            <p>
                <strong>Tanggal:</strong>
                {{ \Carbon\Carbon::parse($pemesanan->tanggal_pemesanan)->format('d/m/Y') }}
            </p>

            <br>

            <p>
                <strong>Kepada Yth,</strong>
                <br>
                {{ $pemesanan->supplier->nama_supplier }}
            </p>
            <p>
                <strong>Alamat:</strong>
                {{ $pemesanan->supplier->alamat }}
            </p>

            <br>

            <p>Dengan hormat,</p>
            <p>Bersama ini kami mengajukan pesanan obat dengan rincian sebagai berikut:</p>
        </div>

        {{-- Daftar Barang --}}
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    @if ($pemesanan->jenis_surat == 'Psikotropika')
                        <th>Nama Psikotropika</th>
                    @elseif(in_array($pemesanan->jenis_surat, ['OOT', 'Prekursor']))
                        <th>Nama Obat</th>
                    @else
                        <th>Nama Barang</th>
                    @endif

                    @if (in_array($pemesanan->jenis_surat, ['Psikotropika', 'OOT', 'Prekursor']))
                        <th>Zat Aktif</th>
                        <th>Bentuk Sediaan</th>
                    @endif

                    <th>Satuan</th>
                    <th>Jumlah</th>

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
    </div>

    <!-- Tanda Tangan -->
    <table class="tanda-tangan">
        <tr>
            <td class="kolom">
                <p><strong>Tanda Terima</strong></p>
                <br><br><br><br> <!-- Ruang untuk tanda tangan -->
                <p>(_________________)</p>
            </td>
            <td class="kolom">
                <p><strong>Hormat Kami</strong></p>
                <br><br><br><br> <!-- Ruang untuk tanda tangan -->
                <p>(_________________)</p>
            </td>
        </tr>
    </table>

</body>

</html>
