    @section('title', 'Edit Profile')

    @include('layouts.partials.head')
    @include('layouts.partials.navbar')
    @include('layouts.partials.sidebar')

    <main class="main" id="main">
        <div class="container mt-4">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">Edit Profil</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
    
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-4">
                            <div class="col-md-3 text-center">
                                <img src="{{ $user->photo ? asset('storage/' . $user->photo . '?v=' . time()) : asset('assets/img/profile-img.jpg') }}" 
                                     alt="Profile Picture" class="rounded-circle mb-3" 
                                     id="preview-photo" width="150" height="150" style="object-fit: cover;">
                                
                                <div class="mb-3">
                                    <label for="photo" class="form-label">Foto Profil</label>
                                    <input type="file" name="photo" id="photo" class="form-control" 
                                           accept="image/jpeg,image/png,image/jpg">
                                    <small class="text-muted">Maksimal ukuran 2MB</small>
                                </div>
                            </div>
                            
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama</label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $user->name) }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profile') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    @include('layouts.partials.footer')

    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const photoInput = document.getElementById('photo');
            const previewPhoto = document.getElementById('preview-photo');
    
            photoInput.addEventListener('change', function (event) {
                const file = event.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewPhoto.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
    
    
