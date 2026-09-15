@if(isset($paginator) && $paginator->total() > 0)
<div class="d-flex justify-content-end align-items-center gap-3 py-2 flex-wrap text-muted" style="font-size: 0.88rem;">
    {{-- Thống kê số lượng: 1-16 trong 16 mục --}}
    <div class="text-dark">
        <span>{{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} trong {{ $paginator->total() }} mục</span>
    </div>

    {{-- Nút Previous (<) --}}
    <div>
        @if ($paginator->onFirstPage())
            <span class="custom-page-btn disabled" aria-disabled="true">
                <i class="bi bi-chevron-left"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="custom-page-btn" title="Trang trước">
                <i class="bi bi-chevron-left"></i>
            </a>
        @endif
    </div>

    {{-- Danh sách số trang --}}
    <div class="d-flex align-items-center gap-1">
        @php
            $currentPage = $paginator->currentPage();
            $lastPage = $paginator->lastPage();
            $start = max(1, $currentPage - 2);
            $end = min($lastPage, $currentPage + 2);
        @endphp

        @if ($start > 1)
            <a href="{{ $paginator->url(1) }}" class="custom-page-btn {{ $currentPage == 1 ? 'active' : '' }}">1</a>
            @if ($start > 2)
                <span class="px-1 text-muted">...</span>
            @endif
        @endif

        @for ($i = $start; $i <= $end; $i++)
            @if ($i == $currentPage)
                <span class="custom-page-btn active" aria-current="page">{{ $i }}</span>
            @else
                <a href="{{ $paginator->url($i) }}" class="custom-page-btn">{{ $i }}</a>
            @endif
        @endfor

        @if ($end < $lastPage)
            @if ($end < $lastPage - 1)
                <span class="px-1 text-muted">...</span>
            @endif
            <a href="{{ $paginator->url($lastPage) }}" class="custom-page-btn {{ $currentPage == $lastPage ? 'active' : '' }}">{{ $lastPage }}</a>
        @endif
    </div>

    {{-- Nút Next (>) --}}
    <div>
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="custom-page-btn" title="Trang tiếp">
                <i class="bi bi-chevron-right"></i>
            </a>
        @else
            <span class="custom-page-btn disabled" aria-disabled="true">
                <i class="bi bi-chevron-right"></i>
            </span>
        @endif
    </div>

    {{-- Dropdown chọn số mục / Trang --}}
    <div>
        <select class="form-select form-select-sm custom-per-page-select" onchange="window.location.href = this.value;">
            @php
                $currentPerPage = $paginator->perPage();
                $options = [10, 20, 50, 100];
                if (!in_array($currentPerPage, $options)) {
                    $options[] = $currentPerPage;
                    sort($options);
                }
            @endphp
            @foreach ($options as $opt)
                <option value="{{ $paginator->appends(array_merge(request()->query(), ['per_page' => $opt, 'page' => 1]))->url(1) }}" {{ $currentPerPage == $opt ? 'selected' : '' }}>
                    {{ $opt }} / Trang
                </option>
            @endforeach
        </select>
    </div>
</div>
@endif
