@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination"
        class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-6">

        <p class="text-sm text-slate-500">
            Menampilkan
            <span class="font-semibold text-slate-700">{{ $paginator->firstItem() }}</span>
            &ndash;
            <span class="font-semibold text-slate-700">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-semibold text-slate-700">{{ $paginator->total() }}</span>
            data
        </p>

        <ul class="flex items-center gap-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span
                        class="inline-flex items-center justify-center h-9 min-w-9 px-3 rounded-lg bg-slate-100 text-slate-400 text-sm font-medium cursor-not-allowed select-none">
                        Sebelumnya
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                        class="inline-flex items-center justify-center h-9 min-w-9 px-3 rounded-lg bg-white border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 hover:border-slate-300 hover:text-slate-900 transition">
                        Sebelumnya
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li>
                        <span
                            class="inline-flex items-center justify-center h-9 min-w-9 px-2 text-slate-400 text-sm select-none">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span aria-current="page"
                                    class="inline-flex items-center justify-center h-9 min-w-9 px-3 rounded-lg bg-red-600 text-white text-sm font-semibold shadow-sm">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}"
                                    class="inline-flex items-center justify-center h-9 min-w-9 px-3 rounded-lg bg-white border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 hover:border-slate-300 hover:text-slate-900 transition">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                        class="inline-flex items-center justify-center h-9 min-w-9 px-3 rounded-lg bg-white border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 hover:border-slate-300 hover:text-slate-900 transition">
                        Berikutnya
                    </a>
                </li>
            @else
                <li>
                    <span
                        class="inline-flex items-center justify-center h-9 min-w-9 px-3 rounded-lg bg-slate-100 text-slate-400 text-sm font-medium cursor-not-allowed select-none">
                        Berikutnya
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
