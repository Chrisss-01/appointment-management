@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <div class="flex items-center justify-between gap-3 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center rounded-xl border border-white/5 bg-white/[0.04] px-4 py-2 text-sm font-medium text-gray-500 cursor-not-allowed">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center rounded-xl border border-white/5 bg-[#141414] px-4 py-2 text-sm font-medium text-gray-200 transition-all duration-150 hover:border-[#1392EC]/30 hover:bg-white/[0.06] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#1392EC]/30">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center rounded-xl border border-white/5 bg-[#141414] px-4 py-2 text-sm font-medium text-gray-200 transition-all duration-150 hover:border-[#1392EC]/30 hover:bg-white/[0.06] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#1392EC]/30">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="inline-flex items-center rounded-xl border border-white/5 bg-white/[0.04] px-4 py-2 text-sm font-medium text-gray-500 cursor-not-allowed">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden items-center justify-between gap-4 sm:flex">
            <div>
                <p class="text-sm text-gray-400">
                    {!! __('Showing') !!}
                    @if ($paginator->firstItem())
                        <span class="font-semibold text-white">{{ $paginator->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-semibold text-white">{{ $paginator->lastItem() }}</span>
                    @else
                        <span class="font-semibold text-white">{{ $paginator->count() }}</span>
                    @endif
                    {!! __('of') !!}
                    <span class="font-semibold text-white">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <span class="inline-flex items-center gap-2">
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/5 bg-white/[0.04] text-gray-500 cursor-not-allowed" aria-hidden="true">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/5 bg-[#141414] text-gray-300 transition-all duration-150 hover:border-[#1392EC]/30 hover:bg-white/[0.06] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#1392EC]/30" aria-label="{{ __('pagination.previous') }}">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="inline-flex min-w-10 items-center justify-center rounded-xl border border-white/5 bg-white/[0.04] px-3 py-2 text-sm font-medium text-gray-500">
                                    {{ $element }}
                                </span>
                            </span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="inline-flex min-w-10 items-center justify-center rounded-xl border border-[#1392EC]/30 bg-[#1392EC] px-3 py-2 text-sm font-semibold text-white shadow-lg shadow-[#1392EC]/20">
                                            {{ $page }}
                                        </span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex min-w-10 items-center justify-center rounded-xl border border-white/5 bg-[#141414] px-3 py-2 text-sm font-medium text-gray-300 transition-all duration-150 hover:border-[#1392EC]/30 hover:bg-white/[0.06] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#1392EC]/30" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/5 bg-[#141414] text-gray-300 transition-all duration-150 hover:border-[#1392EC]/30 hover:bg-white/[0.06] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#1392EC]/30" aria-label="{{ __('pagination.next') }}">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/5 bg-white/[0.04] text-gray-500 cursor-not-allowed" aria-hidden="true">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
