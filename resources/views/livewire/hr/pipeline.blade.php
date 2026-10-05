<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Recruitment Pipeline</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $totalCount }} candidates · drag a card or use its menu to move stages</p>
        </div>

        <a href="{{ route('hr.candidates.create') }}" wire:navigate
            class="inline-flex items-center gap-2 rounded-lg bg-brand-teal px-4 py-2 text-sm font-medium text-white hover:bg-brand-teal-dark">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add Candidate
        </a>
    </div>

    {{-- Search --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative max-w-sm flex-1">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                    fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search by candidate name..."
                    class="w-full rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/20">
            </div>

            @if ($this->hasActiveFilters())
                <button type="button" wire:click="clearFilters" class="text-sm font-medium text-slate-500 hover:text-slate-700">
                    Clear
                </button>
            @endif
        </div>
    </div>

    {{-- Board --}}
    <div id="pipeline-board" wire:loading.class="opacity-60" wire:target="moveToStage,search"
        class="flex gap-4 overflow-x-auto pb-2 transition-opacity duration-150">
        @foreach ($columns as $column)
            <div wire:key="column-{{ $column['stage'] }}" data-stage="{{ $column['stage'] }}"
                class="flex w-72 shrink-0 flex-col rounded-xl border border-slate-200 bg-slate-50">
                <div class="flex items-center justify-between px-3 py-2.5">
                    <h3 class="text-sm font-semibold text-slate-700">{{ $column['label'] }}</h3>
                    <span class="rounded-full bg-white px-1.5 py-0.5 text-[11px] font-medium text-slate-500 ring-1 ring-inset ring-slate-200">
                        {{ count($column['candidates']) }}
                    </span>
                </div>

                <div class="pipeline-column-body min-h-24 flex-1 space-y-2 overflow-y-auto px-2 pb-2" style="max-height: calc(100vh - 22rem)">
                    @forelse ($column['candidates'] as $candidate)
                        <div wire:key="pipeline-card-{{ $candidate['id'] }}" data-candidate-id="{{ $candidate['id'] }}"
                            @if (! $column['concluded']) draggable="true" @endif
                            @class([
                                'group rounded-lg border border-slate-200 bg-white p-3 shadow-sm',
                                'cursor-grab active:cursor-grabbing' => ! $column['concluded'],
                            ])>
                            <div class="flex items-start justify-between gap-2">
                                <a href="{{ route('hr.candidates.show', $candidate['id']) }}" wire:navigate
                                    class="flex min-w-0 items-center gap-2">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-teal-light text-[11px] font-semibold text-brand-teal">
                                        {{ $candidate['initials'] }}
                                    </span>
                                    <span class="truncate text-sm font-medium text-slate-900 hover:text-brand-teal">{{ $candidate['name'] }}</span>
                                </a>

                                <details class="pipeline-card-menu relative shrink-0">
                                    <summary class="flex h-6 w-6 list-none items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 [&::-webkit-details-marker]:hidden">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.75" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6.75h.007v.008H12V6.75Zm0 5.25h.007v.008H12V12Zm0 5.25h.007v.008H12v-.008Z" />
                                        </svg>
                                    </summary>

                                    <div class="pipeline-card-menu-panel fixed z-50 max-h-80 w-48 overflow-y-auto rounded-lg border border-slate-200 bg-white py-1 shadow-lg">
                                        <a href="{{ route('hr.candidates.show', $candidate['id']) }}" wire:navigate
                                            class="block px-3.5 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">
                                            View Profile
                                        </a>

                                        @unless ($column['concluded'])
                                            <div class="my-1 border-t border-slate-100"></div>

                                            @foreach ($this->stages() as $stageValue => $stageLabel)
                                                @continue($stageValue === $column['stage'])

                                                <button type="button"
                                                    wire:click="moveToStage({{ $candidate['id'] }}, '{{ $stageValue }}')"
                                                    @if (in_array($stageValue, ['Selected', 'Rejected'], true))
                                                        wire:confirm="Mark {{ $candidate['name'] }} as {{ $stageValue }}?"
                                                    @endif
                                                    @class([
                                                        'block w-full px-3.5 py-2 text-left text-sm hover:bg-slate-50',
                                                        'text-rose-600 hover:bg-rose-50' => $stageValue === 'Rejected',
                                                        'text-emerald-600' => $stageValue === 'Selected',
                                                        'text-slate-700' => ! in_array($stageValue, ['Selected', 'Rejected'], true),
                                                    ])>
                                                    Move to {{ $stageLabel }}
                                                </button>
                                            @endforeach
                                        @endunless
                                    </div>
                                </details>
                            </div>

                            <p class="mt-2 truncate text-xs text-slate-500">{{ $candidate['designation'] ?? 'Candidate' }}</p>

                            <div class="mt-2 flex items-center justify-between text-[11px] text-slate-400">
                                <span>{{ $candidate['experience'] }} yrs</span>
                                <span class="truncate">{{ $candidate['location'] ?? '—' }}</span>
                            </div>

                            @if ($candidate['missing_round'])
                                <div class="mt-2 flex items-center justify-between gap-2 rounded-md bg-amber-50 px-2 py-1 text-[11px] text-amber-700">
                                    <span class="flex items-center gap-1" title="This stage has no matching round scheduled yet">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                        </svg>
                                        No round scheduled
                                    </span>
                                    <a href="{{ route('hr.rounds.schedule', ['candidateId' => $candidate['id'], 'roundType' => $column['stage']]) }}" wire:navigate
                                        class="shrink-0 font-medium underline hover:text-amber-800">
                                        Schedule
                                    </a>
                                </div>
                            @elseif ($candidate['scheduled_label'])
                                <div class="mt-2 flex items-center gap-1.5 rounded-md bg-brand-teal-light px-2 py-1 text-[11px] text-brand-teal">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    {{ $candidate['scheduled_label'] }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="px-1 py-6 text-center text-xs text-slate-400">No candidates</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    {{-- Legend --}}
    <p class="text-xs text-slate-400">Tip: drag a card into another column to move that candidate's stage, or use the ⋮ menu on a card.</p>
</div>

@script
<script>
    const board = document.getElementById('pipeline-board');
    let activeColumn = null;

    // The card's ⋮ menu panel is `position: fixed` (not `absolute`) so it
    // can float above the column's own scrollbar instead of being clipped
    // by it — fixed positioning escapes an `overflow-y-auto` ancestor as
    // long as nothing in between sets a transform/filter/contain. Its
    // coordinates have to be computed in JS on open since fixed elements
    // no longer position relative to their trigger.
    const positionMenuPanel = (details) => {
        const summary = details.querySelector('summary');
        const panel = details.querySelector('.pipeline-card-menu-panel');

        if (!summary || !panel) {
            return;
        }

        const anchor = summary.getBoundingClientRect();
        const margin = 8;

        let left = anchor.right - panel.offsetWidth;
        left = Math.min(Math.max(left, margin), window.innerWidth - panel.offsetWidth - margin);

        let top = anchor.bottom + 4;
        if (top + panel.offsetHeight > window.innerHeight - margin) {
            top = anchor.top - panel.offsetHeight - 4;
        }

        panel.style.left = `${left}px`;
        panel.style.top = `${top}px`;
    };

    const closeOpenCardMenus = (except = null) => {
        if (!document.body.contains(board)) {
            return;
        }

        board.querySelectorAll('details.pipeline-card-menu[open]').forEach((details) => {
            if (details !== except) {
                details.open = false;
            }
        });
    };

    // 'toggle' doesn't bubble, but it still passes through the capture
    // phase on its way down to the <details> element, so a single
    // capture-phase listener on the board still catches every card's menu.
    board.addEventListener('toggle', (event) => {
        const details = event.target;

        if (!details.matches || !details.matches('details.pipeline-card-menu')) {
            return;
        }

        if (!details.open) {
            return;
        }

        closeOpenCardMenus(details);
        positionMenuPanel(details);
    }, true);

    // A fixed-position panel doesn't move with the column underneath it,
    // so once the column scrolls it would visually detach from its card —
    // simplest fix is to just close it, same as any dropdown would.
    board.addEventListener('scroll', (event) => {
        if (event.target.closest('.pipeline-column-body')) {
            closeOpenCardMenus();
        }
    }, true);

    // The same panel also won't track the whole page scrolling underneath
    // it (not just the column's own scroll), so cover that too.
    window.addEventListener('scroll', () => closeOpenCardMenus(), true);
    window.addEventListener('resize', () => closeOpenCardMenus());

    const clearActiveColumn = () => {
        activeColumn?.classList.remove('bg-brand-teal-light/40');
        activeColumn = null;
    };

    board.addEventListener('dragstart', (event) => {
        const card = event.target.closest('[data-candidate-id]');

        if (!card || card.getAttribute('draggable') !== 'true') {
            return;
        }

        event.dataTransfer.setData('text/plain', card.dataset.candidateId);
        card.classList.add('opacity-50');
    });

    board.addEventListener('dragend', (event) => {
        event.target.closest('[data-candidate-id]')?.classList.remove('opacity-50');
        clearActiveColumn();
    });

    board.addEventListener('dragover', (event) => {
        const column = event.target.closest('[data-stage]');

        if (!column) {
            return;
        }

        event.preventDefault();

        if (activeColumn !== column) {
            activeColumn?.classList.remove('bg-brand-teal-light/40');
            column.classList.add('bg-brand-teal-light/40');
            activeColumn = column;
        }
    });

    board.addEventListener('dragleave', (event) => {
        if (!board.contains(event.relatedTarget)) {
            clearActiveColumn();
        }
    });

    board.addEventListener('drop', (event) => {
        const column = event.target.closest('[data-stage]');
        clearActiveColumn();

        if (!column) {
            return;
        }

        event.preventDefault();

        const candidateId = event.dataTransfer.getData('text/plain');
        const stage = column.dataset.stage;

        if (!candidateId) {
            return;
        }

        if ((stage === 'Selected' || stage === 'Rejected') && !confirm(`Mark this candidate as ${stage}?`)) {
            return;
        }

        $wire.moveToStage(parseInt(candidateId, 10), stage);
    });
</script>
@endscript
