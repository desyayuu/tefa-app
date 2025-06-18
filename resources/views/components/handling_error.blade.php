{{-- resources/views/components/handling_error.blade.php --}}

@php
    // Cek apakah parameter section dilewatkan ke component
    $currentSection = $section ?? null;
    $targetSection = session('section_error') ?? null;
    
    // Hanya tampilkan error jika section cocok atau jika tidak ada section yang ditentukan
    $showError = ($currentSection == $targetSection) || ($currentSection == null && $targetSection == null);
@endphp

@if($showError)
    {{-- Success Message --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Warning Message --}}
    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
@endif