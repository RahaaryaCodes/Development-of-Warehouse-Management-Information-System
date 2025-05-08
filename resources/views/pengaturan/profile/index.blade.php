@section('title', 'Profile')

@include('layouts.partials.head')
@include('layouts.partials.navbar')
@include('layouts.partials.sidebar')

<main class="main" id="main">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex flex-column flex-md-row align-items-center">
                        <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('assets/img/profile-img.jpg') }}"
                             alt="Profile Picture" class="rounded-circle me-md-4 mb-3 mb-md-0"
                             width="120" height="120" style="object-fit: cover;">

                        <div class="flex-grow-1">
                            <h4 class="fw-bold">{{ $user->name }}</h4>
                            <p class="mb-1"><i class="bi bi-envelope me-2"></i>{{ $user->email }}</p>
                            <p class="mb-1"><i class="bi bi-person-badge me-2"></i>{{ $user->role ?? 'User' }}</p>

                            <a href="{{ route('profile.edit') }}" class="btn btn-primary mt-3">
                                <i class="bi bi-pencil-square me-1"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</main>

@include('layouts.partials.footer')
