@php
    $role = auth()->user()->role ?? 'guest';
    $themeColor = match ($role) {
        'admin' => '#003B7A',
        'manager' => '#0F766E',
        'staff' => '#CA8A04',
        default => '#1E293B',
    };
@endphp

@if ($paginator->hasPages())
    <nav class="mt-3">
        <ul class="pagination justify-content-center mb-0" style="gap: 5px;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link border-0 text-muted shadow-sm px-3 py-2">Previous</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link border-0 fw-semibold text-white shadow-sm px-3 py-2"
                        href="{{ $paginator->previousPageUrl() }}" rel="prev"
                        style="background: {{ $themeColor }}; border-radius: 8px;">Previous</a>
                </li>
            @endif

            {{-- Pagination Numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link border-0 text-muted shadow-sm px-3 py-2">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link fw-bold text-white shadow-sm border-0 px-3 py-2"
                                    style="background: {{ $themeColor }}; border-radius: 8px;">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link border-0 shadow-sm fw-semibold text-dark px-3 py-2"
                                    href="{{ $url }}"
                                    style="background: #f1f5f9; border-radius: 8px;">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link border-0 fw-semibold text-white shadow-sm px-3 py-2"
                        href="{{ $paginator->nextPageUrl() }}" rel="next"
                        style="background: {{ $themeColor }}; border-radius: 8px;">Next</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link border-0 text-muted shadow-sm px-3 py-2">Next</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
