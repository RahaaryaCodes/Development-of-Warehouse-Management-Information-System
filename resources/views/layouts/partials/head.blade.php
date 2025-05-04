<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard')</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('templates/NiceAdmin/assets/img/cross.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('templates/NiceAdmin/assets/img/apple-touch-icon.png') }}">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('templates/NiceAdmin/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/NiceAdmin/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/NiceAdmin/assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/NiceAdmin/assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/NiceAdmin/assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/NiceAdmin/assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/NiceAdmin/assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Main CSS File -->
    <link href="{{ asset('templates/NiceAdmin/assets/css/style.css') }}" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table thead th {
            background-color: #4154f1;
            color: white;
            font-weight: 500;
        }

        .search-box {
            margin-bottom: 20px;
        }

        .low-stock {
            color: #dc3545;
            font-weight: bold;
        }

        .badge-stock {
            padding: 5px 10px;
            border-radius: 20px;
        }

        .pagination {
            margin-top: 20px;
        }

        .pagination .page-item.active .page-link {
            background-color: #4154f1;
            color: white;
        }
    </style>

    {{-- Modal Detail Penerimaan --}}
    <style>
        #detailPenerimaanModal .table-responsive {
            overflow-x: auto;
        }

        #detailPenerimaanModal .table {
            min-width: 1800px;
            table-layout: fixed;
        }

        /* Header dan sel tabel lebih luas */
        #detailPenerimaanModal .table th,
        #detailPenerimaanModal .table td {
            font-size: 14px;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }

        /* Atur lebar tiap kolom */
        #detailPenerimaanModal th:nth-child(1),
        #detailPenerimaanModal td:nth-child(1) {
            width: 50px;
        }

        /* No */

        #detailPenerimaanModal th:nth-child(2),
        #detailPenerimaanModal td:nth-child(2) {
            width: 200px;
        }

        /* Nama Barang */

        #detailPenerimaanModal th:nth-child(3),
        #detailPenerimaanModal td:nth-child(3) {
            width: 200px;
        }

        /* Obat */

        #detailPenerimaanModal th:nth-child(4),
        #detailPenerimaanModal td:nth-child(4) {
            width: 130px;
        }

        /* Batch */

        #detailPenerimaanModal th:nth-child(5),
        #detailPenerimaanModal td:nth-child(5) {
            width: 130px;
        }

        /* No Faktur */

        #detailPenerimaanModal th:nth-child(6),
        #detailPenerimaanModal td:nth-child(6) {
            width: 100px;
        }

        /* Jumlah */

        #detailPenerimaanModal th:nth-child(7),
        #detailPenerimaanModal td:nth-child(7) {
            width: 120px;
        }

        /* Satuan */

        #detailPenerimaanModal th:nth-child(8),
        #detailPenerimaanModal td:nth-child(8) {
            width: 140px;
        }

        /* Harga Beli */

        #detailPenerimaanModal th:nth-child(9),
        #detailPenerimaanModal td:nth-child(9) {
            width: 140px;
        }

        /* Harga Jual */

        #detailPenerimaanModal th:nth-child(10),
        #detailPenerimaanModal td:nth-child(10) {
            width: 160px;
        }

        /* Total Harga */

        #detailPenerimaanModal th:nth-child(11),
        #detailPenerimaanModal td:nth-child(11) {
            width: 120px;
        }

        /* Diskon */

        #detailPenerimaanModal th:nth-child(12),
        #detailPenerimaanModal td:nth-child(12) {
            width: 120px;
        }

        /* PPN */

        #detailPenerimaanModal th:nth-child(13),
        #detailPenerimaanModal td:nth-child(13) {
            width: 160px;
        }

        /* Tgl Kadaluarsa */

        #detailPenerimaanModal th:nth-child(14),
        #detailPenerimaanModal td:nth-child(14) {
            width: 160px;
        }

        /* Tgl Diterima */

        #detailPenerimaanModal th:nth-child(15),
        #detailPenerimaanModal td:nth-child(15) {
            width: 150px;
        }

        /* Lokasi Gudang*/

        #detailPenerimaanModal th:nth-child(16),
        #detailPenerimaanModal td:nth-child(16) {
            width: 150px;
        }

        /* Lokasi Etalase */

        #detailPenerimaanModal th:nth-child(17),
        #detailPenerimaanModal td:nth-child(17) {
            width: 200px;
        }

        /* Zat Aktif */

        #detailPenerimaanModal th:nth-child(18),
        #detailPenerimaanModal td:nth-child(18) {
            width: 180px;
        }

        /* Bentuk */

        #detailPenerimaanModal th:nth-child(19),
        #detailPenerimaanModal td:nth-child(19) {
            width: 200px;
        }

        /* Keterangan */

        #detailPenerimaanModal th:nth-child(20),
        #detailPenerimaanModal td:nth-child(20) {
            width: 200px;
        }

        /* Catatan */

        /* Lebarkan input dan select dalam modal ini */
        #detailPenerimaanModal .form-control,
        #detailPenerimaanModal .form-select {
            width: 100%;
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
