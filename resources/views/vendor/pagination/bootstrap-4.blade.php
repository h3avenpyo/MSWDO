@if ($paginator->hasPages())
    <nav>
        <ul class="pagination" style="display: flex; justify-content: center; gap: 0.25rem; margin: 0; list-style: none; padding: 0;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')" style="margin: 0;">
                    <span class="page-link" aria-hidden="true" style="display: inline-block; border: 1px solid #E5E7EB; color: #6B7280; padding: 0.375rem 0.75rem; border-radius: 6px; text-decoration: none; background: #F8FAFC; min-width: 40px; text-align: center;">&lsaquo;</span>
                </li>
            @else
                <li class="page-item" style="margin: 0;">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')" style="display: inline-block; border: 1px solid #E5E7EB; color: #1F2937; padding: 0.375rem 0.75rem; border-radius: 6px; text-decoration: none; background: #FFFFFF; min-width: 40px; text-align: center;">&lsaquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true" style="margin: 0;"><span class="page-link" style="display: inline-block; border: 1px solid #E5E7EB; color: #6B7280; padding: 0.375rem 0.75rem; border-radius: 6px; text-decoration: none; background: #F8FAFC; min-width: 40px; text-align: center;">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page" style="margin: 0;"><span class="page-link" style="display: inline-block; border: 1px solid #1A237E; color: white; padding: 0.375rem 0.75rem; border-radius: 6px; text-decoration: none; background: #1A237E; min-width: 40px; text-align: center;">{{ $page }}</span></li>
                        @else
                            <li class="page-item" style="margin: 0;"><a class="page-link" href="{{ $url }}" style="display: inline-block; border: 1px solid #E5E7EB; color: #1F2937; padding: 0.375rem 0.75rem; border-radius: 6px; text-decoration: none; background: #FFFFFF; min-width: 40px; text-align: center;">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item" style="margin: 0;">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')" style="display: inline-block; border: 1px solid #E5E7EB; color: #1F2937; padding: 0.375rem 0.75rem; border-radius: 6px; text-decoration: none; background: #FFFFFF; min-width: 40px; text-align: center;">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')" style="margin: 0;">
                    <span class="page-link" aria-hidden="true" style="display: inline-block; border: 1px solid #E5E7EB; color: #6B7280; padding: 0.375rem 0.75rem; border-radius: 6px; text-decoration: none; background: #F8FAFC; min-width: 40px; text-align: center;">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
