@if ($paginator->hasPages())
<nav>
    <ul class="pagination mb-0">

        {{-- Prev --}}
        <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}"
            wire:key="prev-{{ $paginator->getPageName() }}">
            <a class="page-link" href="#"
               wire:click.prevent="previousPage('{{ $paginator->getPageName() }}')"
               aria-label="Previous" rel="prev">&laquo;</a>
        </li>

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}"
                        wire:key="page-{{ $paginator->getPageName() }}-{{ $page }}">
                        <a class="page-link" href="#"
                           wire:click.prevent="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')">
                            {{ $page }}
                        </a>
                    </li>
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}"
            wire:key="next-{{ $paginator->getPageName() }}">
            <a class="page-link" href="#"
               wire:click.prevent="nextPage('{{ $paginator->getPageName() }}')"
               aria-label="Next" rel="next">&raquo;</a>
        </li>

    </ul>
</nav>
@endif
