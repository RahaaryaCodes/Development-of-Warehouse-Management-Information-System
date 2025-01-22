<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Edit Obat - Cinta Sehat 24</title>

  <!-- Vendor CSS Files -->
  <link href="{{ asset('templates/NiceAdmin/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('templates/NiceAdmin/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('templates/NiceAdmin/assets/css/style.css') }}" rel="stylesheet">
</head>

<body>
  <!-- ======= Navbar ======= -->
  @include('layouts.partials.navbar')

  <!-- ======= Sidebar ======= -->
  @include('layouts.partials.sidebar')

  <main id="main" class="main">
    <!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Edit Obat - Cinta Sehat 24</title>

  <!-- Vendor CSS Files -->
  <link href="{{ asset('templates/NiceAdmin/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('templates/NiceAdmin/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('templates/NiceAdmin/assets/css/style.css') }}" rel="stylesheet">
</head>

<body>
  <!-- ======= Navbar ======= -->
  @include('layouts.partials.navbar')

  <!-- ======= Sidebar ======= -->
  @include('layouts.partials.sidebar')

  <main id="main" class="main">
    <div class="container">
      <div class="row justify-content-center">
          <div class="col-md-8">
              <div class="card">
                  <div class="card-header">
                      <h5 class="card-title">Edit Kategori</h5>
                  </div>
                  <div class="card-body">
                    <form action="{{ route('data-kategori.update', $kategori->id) }}" method="POST">
                      @csrf
                      @method('PUT')
                      <div class="mb-3">
                          <label for="nama_kategori" class="form-label">Nama Kategori</label>
                          <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}">
                      </div>
                      <div class="mb-3">
                          <label for="keterangan" class="form-label">Keterangan</label>
                          <textarea class="form-control" id="keterangan" name="keterangan">{{ old('keterangan', $kategori->keterangan) }}</textarea>
                      </div>
                      <button type="submit" class="btn btn-primary">Update</button>
                  </form>
                  
                  </div>
              </div>
          </div>
      </div>
  </div>
  </main>

  <!-- Vendor JS Files -->
  <script src="{{ asset('templates/NiceAdmin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('templates/NiceAdmin/assets/js/main.js') }}"></script>
</body>

</html>


