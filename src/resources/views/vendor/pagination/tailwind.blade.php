@if ($paginator->hasPages())
    <nav class="paginate-nav" role="navigation">
        <div class="paginate-inner">

            {{-- 前へ --}}
            @if ($paginator->onFirstPage())
                <span class="paginate-btn paginate-btn--disabled">‹</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="paginate-btn">‹</a>
            @endif

            {{-- ページ番号 --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="paginate-btn paginate-btn--dots">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="paginate-btn paginate-btn--active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="paginate-btn">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- 次へ --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="paginate-btn">›</a>
            @else
                <span class="paginate-btn paginate-btn--disabled">›</span>
            @endif

        </div>
    </nav>
@endif
