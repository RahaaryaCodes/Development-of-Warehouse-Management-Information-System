<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Default Title')</title>
  
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('templates/NiceAdmin/assets/img/cross.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('templates/NiceAdmin/assets/img/apple-touch-icon.png') }}">
  
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  
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
  
    <!-- Main CSS File -->
    <link href="{{ asset('templates/NiceAdmin/assets/css/style.css') }}" rel="stylesheet">
  
    <!-- Custom Styles -->
    <style>
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
  </head>
  