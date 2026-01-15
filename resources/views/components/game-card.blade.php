@props(['cover', 'title', 'platform'])

<div class="card h-100 shadow-sm hover-shadow transition">
    <!--  Imagen -->
    <div class="position-relative bg-light" style="padding-top: 100%; overflow: hidden;">
        <img src="{{ $cover }}" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover" alt="{{ $title }}">
        @if(isset($topLeft))
            <div class="position-absolute top-0 start-0 p-2">
                {{ $topLeft }}
            </div>
        @endif
    </div>

    <!--  Titulo y plataforma -->
    <div class="card-body d-flex flex-column p-3">
        <h5 class="card-title text-center fw-bold mb-1" title="{{ $title }}">{{ $title }}</h5>
        
        @if(isset($platform))
            <div class="text-center mb-3">
                <span class="badge bg-secondary text-uppercase">{{ $platform }}</span>
            </div>
        @endif

        <div class="card-text flex-grow-1">
            {{ $slot }}
        </div>
    </div>

    @if(isset($actions))
        <div class="card-footer bg-white border-top-0 p-3 pt-0">
            <div class="d-flex justify-content-center gap-2">
                {{ $actions }}
            </div>
        </div>
    @endif
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .transition {
        transition: all .3s ease;
    }
</style>