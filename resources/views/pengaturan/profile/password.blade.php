@include('layouts.partials.head')
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

@section('title', 'Ganti Password')

<main class="main" id="main">
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-body">
            <h5 class="mb-4">Ganti Password</h5>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('profile.password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="current_password" class="form-label">Password Saat Ini</label>
                    <input type="password" name="current_password" class="form-control" required>
                    @error('current_password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">Password Baru</label>
                    <input type="password" name="new_password" class="form-control" required>
                    @error('new_password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Password</button>
            </form>
        </div>
    </div>
</div>

</main>

@include('layouts.partials.footer')
