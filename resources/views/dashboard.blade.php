@extends('layouts.app')

@php
    use App\Enums\MilestoneStatus;

    $seoTitle = $project->project_title.' — Project Dashboard';
    $noindex = true; // private client page

    $statusMeta = [
        'completed' => ['label' => 'Completed', 'dot' => 'bg-emerald-800', 'badge' => 'bg-emerald-900 text-yellow-400'],
        'in_progress' => ['label' => 'In Progress', 'dot' => 'bg-yellow-400', 'badge' => 'bg-yellow-400 text-slate-950'],
        'pending' => ['label' => 'Pending', 'dot' => 'bg-slate-300', 'badge' => 'bg-slate-200 text-slate-600'],
    ];

    $completedCount = $milestones->where('status', MilestoneStatus::Completed)->count();
    $totalCount = max($milestones->count(), 1);
    $pct = (int) round($completedCount / $totalCount * 100);

    // The step the client actually opened this page to check on. Falls back to
    // the first unfinished step so a project between stages still reads as
    // moving rather than showing nothing as current.
    $currentMilestone = $milestones->firstWhere('status', MilestoneStatus::InProgress)
        ?? $milestones->firstWhere('status', MilestoneStatus::Pending);

    $nextMilestone = $currentMilestone
        ? $milestones->skipUntil(fn ($m) => $m->is($currentMilestone))->skip(1)->first()
        : null;

    $timelineHeading = niche_label('timeline_heading', 'Project Timeline');
@endphp

@section('content')
    <section class="bg-brand-paper">
        <div class="mx-auto max-w-5xl px-6 py-14">            {{-- ============== PROJECT HEADER ============== --}}
            <div data-reveal class="box-brutal p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <span class="inline-block bg-emerald-900 px-3 py-1 text-xs font-black uppercase tracking-widest text-yellow-400">Private Project Dashboard</span>
                        <h1 class="mt-4 text-3xl font-black uppercase leading-tight tracking-tight text-slate-900 sm:text-4xl">{{ $project->project_title }}</h1>
                        <p class="mt-2 text-slate-600">
                            {{ $project->client_name }} · {{ $project->neighborhood }}
                            @if ($project->started_at) · Started {{ $project->started_at->format('M j, Y') }} @endif
                        </p>
                    </div>
                    <span @class([
                        'shrink-0 border-2 border-slate-950 px-3 py-1.5 text-xs font-black uppercase tracking-widest',
                        'bg-yellow-400 text-slate-950' => $project->status->value === 'active',
                        'bg-emerald-900 text-yellow-400' => $project->status->value === 'completed',
                        'bg-slate-200 text-slate-700' => $project->status->value === 'scheduled',
                    ])>{{ $project->status->getLabel() }}</span>
                </div>

                {{--
                    Progress bar + step track are one block on purpose. They tell
                    the same story, so splitting them (or letting anything sit
                    between them) makes the page read as a list of widgets rather
                    than a tracked journey.
                --}}
                <div class="mt-6">
                    <div class="flex items-center justify-between text-xs font-black uppercase tracking-widest text-slate-500">
                        <span>Overall Progress</span>
                        <span>{{ $completedCount }} / {{ $totalCount }} milestones · {{ $pct }}%</span>
                    </div>
                    <div class="mt-2 h-4 w-full border-2 border-slate-950 bg-white">
                        <div class="h-full bg-yellow-400 transition-[width] duration-700" style="width: {{ $pct }}%"></div>
                    </div>
                </div>

                {{-- ============== HORIZONTAL STEP TRACK ============== --}}
                <div class="mt-7 overflow-x-auto pb-1">
                    <div class="flex min-w-max items-start gap-0">
                        @foreach ($milestones as $i => $milestone)
                            @php
                                $meta = $statusMeta[$milestone->status->value] ?? $statusMeta['pending'];
                                $isCurrent = $currentMilestone && $milestone->is($currentMilestone);
                            @endphp
                            <div class="flex items-start">
                                <div @class(['flex flex-col items-center text-center', 'w-32 sm:w-36' => ! $isCurrent, 'w-36 sm:w-40' => $isCurrent])>
                                    {{--
                                        Every step gets the same h-14 slot even
                                        though the current marker is bigger, so
                                        the title/date/badge rows below line up
                                        straight across the whole track.
                                    --}}
                                    <div class="flex h-14 items-center justify-center">
                                        <div @class([
                                            'flex items-center justify-center border-2 border-slate-950 font-black',
                                            'h-11 w-11 text-sm' => ! $isCurrent,
                                            // The current step is deliberately larger and ringed — it is
                                            // the one thing the client came here to look at.
                                            'h-14 w-14 text-base ring-4 ring-yellow-400/40' => $isCurrent,
                                            'bg-emerald-900 text-yellow-400' => $milestone->status === MilestoneStatus::Completed,
                                            'bg-yellow-400 text-slate-950' => $milestone->status === MilestoneStatus::InProgress,
                                            'bg-white text-slate-400' => $milestone->status === MilestoneStatus::Pending,
                                        ])>
                                            @if ($milestone->status === MilestoneStatus::Completed)
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                            @else
                                                {{ $i + 1 }}
                                            @endif
                                        </div>
                                    </div>
                                    {{--
                                        Fixed two-line box: step titles wrap to
                                        different line counts ("Baths & Floors"
                                        vs "Arrival & Walkthrough"), which would
                                        otherwise leave the date/badge row below
                                        sitting at ragged heights across the track.
                                    --}}
                                    <span @class([
                                        'mt-2 flex min-h-[2.1rem] items-start justify-center px-1 text-[11px] font-black uppercase leading-tight tracking-wide',
                                        'text-slate-900' => ! $isCurrent,
                                        'text-slate-950' => $isCurrent,
                                    ])>{{ $milestone->title }}</span>
                                    @if ($milestone->completed_at)
                                        <span class="mt-1 text-[10px] font-bold uppercase tracking-widest text-slate-500">{{ $milestone->completed_at->format('M j') }}</span>
                                    @else
                                        <span @class(['mt-1 px-1.5 py-0.5 text-[9px] font-black uppercase tracking-widest', $meta['badge']])>{{ $meta['label'] }}</span>
                                    @endif
                                </div>
                                @unless ($loop->last)
                                    {{-- mt-7 = half the h-14 slot, so the connector meets every marker's centre line. --}}
                                    <div @class([
                                        'mt-7 h-1 w-8 sm:w-12',
                                        'bg-emerald-900' => $milestone->status === MilestoneStatus::Completed,
                                        'bg-slate-300' => $milestone->status !== MilestoneStatus::Completed,
                                    ])></div>
                                @endunless
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ============== CURRENT STATUS CALLOUT ============== --}}
            @if ($currentMilestone)
                <div data-reveal class="mt-8 box-brutal border-l-8 border-l-yellow-400 p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">
                                {{ $currentMilestone->status === MilestoneStatus::InProgress ? 'Happening now' : 'Up next' }}
                            </span>
                            <h2 class="mt-1 text-2xl font-black uppercase tracking-tight text-slate-900">{{ $currentMilestone->title }}</h2>
                            @if ($currentMilestone->description)
                                <p class="mt-1 text-sm text-slate-600">{{ $currentMilestone->description }}</p>
                            @endif
                        </div>
                        @if ($nextMilestone)
                            <div class="shrink-0 border-l-0 sm:border-l-2 sm:border-slate-200 sm:pl-5">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Then</span>
                                <p class="mt-1 text-sm font-bold text-slate-900">{{ $nextMilestone->title }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- ============== MILESTONE TIMELINE ============== --}}
            {{-- No space-y here: sibling margins would cut visible gaps into the rail. --}}
            <div class="mt-12"
                 x-data="{
                     open: false,
                     activeUrl: '',
                     activeCaption: '',
                     activeMilestone: '',
                     activeDate: '',
                     showPhoto(url, caption, milestone, date) {
                         this.activeUrl = url;
                         this.activeCaption = caption;
                         this.activeMilestone = milestone;
                         this.activeDate = date;
                         this.open = true;
                      }
                 }"
                 @keydown.escape.window="open = false">
                <h2 data-reveal class="mb-6 text-2xl font-black uppercase tracking-tight text-slate-900">{{ $timelineHeading }}</h2>

                {{--
                    A continuous rail threads every step so the page reads as one
                    journey. The rail is drawn per-row (not as a single absolute
                    element) so it survives cards of wildly different heights —
                    a step with six photos next to one with none.
                --}}
                @foreach ($milestones as $milestone)
                    @php
                        $meta = $statusMeta[$milestone->status->value] ?? $statusMeta['pending'];
                        $stepPhotos = $photosByStep->get($milestone->title, collect());
                        $isCurrent = $currentMilestone && $milestone->is($currentMilestone);
                    @endphp
                    <div data-stagger style="--stagger-i: {{ $loop->index }}" class="relative flex gap-4 sm:gap-6">
                        {{-- Rail column: node + connecting line to the next step --}}
                        <div class="flex w-8 shrink-0 flex-col items-center sm:w-10" aria-hidden="true">
                            <div @class([
                                'flex items-center justify-center border-2 border-slate-950 text-[11px] font-black',
                                'h-8 w-8' => ! $isCurrent,
                                'h-10 w-10 ring-4 ring-yellow-400/40' => $isCurrent,
                                'bg-emerald-900 text-yellow-400' => $milestone->status === MilestoneStatus::Completed,
                                'bg-yellow-400 text-slate-950' => $milestone->status === MilestoneStatus::InProgress,
                                'bg-white text-slate-400' => $milestone->status === MilestoneStatus::Pending,
                            ])>
                                @if ($milestone->status === MilestoneStatus::Completed)
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </div>
                            @unless ($loop->last)
                                <div @class([
                                    'w-1 flex-1',
                                    'bg-emerald-900' => $milestone->status === MilestoneStatus::Completed,
                                    'bg-slate-300' => $milestone->status !== MilestoneStatus::Completed,
                                ])></div>
                            @endunless
                        </div>

                        <div @class(['box-brutal mb-6 flex-1 p-6', 'border-l-4 border-l-yellow-400' => $isCurrent])>
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span @class(['h-3 w-3 border border-slate-950', $meta['dot']])></span>
                                    <h3 class="text-xl font-medium tracking-tighter text-slate-900">{{ $milestone->title }}</h3>
                                </div>
                                <div class="flex items-center gap-3">
                                    @if ($milestone->completed_at)
                                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">{{ $milestone->completed_at->format('M j, Y') }}</span>
                                    @endif
                                    <span @class(['border-2 border-slate-950 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest', $meta['badge']])>{{ $meta['label'] }}</span>
                                </div>
                            </div>

                            @if ($milestone->description)
                                <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $milestone->description }}</p>
                            @endif

                        @if ($stepPhotos->isNotEmpty())
                            <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3">
                                @foreach ($stepPhotos as $photo)
                                    @php
                                        $hasImage = filled($photo->image_path);
                                        // Root-relative so APP_URL host/port mismatches (e.g. :8123) do not break images.
                                        $photoUrl = $hasImage ? public_url($photo->image_path) : '';
                                    @endphp
                                    <figure @if ($hasImage) @click="showPhoto('{{ $photoUrl }}', @js($photo->caption), @js($milestone->title), '{{ $photo->created_at?->format('M j, Y') }}')" @endif
                                            @class([
                                                'border-2 border-slate-950',
                                                'cursor-pointer transition-transform hover:-translate-y-0.5 hover:shadow-md' => $hasImage,
                                            ])>
                                        @if ($hasImage)
                                            <img src="{{ $photoUrl }}" alt="{{ $photo->caption }}" class="aspect-square w-full object-cover">
                                        @else
                                            <div class="flex aspect-square w-full items-center justify-center bg-gradient-to-br from-emerald-700 to-emerald-900">
                                                <svg class="h-8 w-8 text-yellow-400/80" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/></svg>
                                            </div>
                                        @endif
                                        <figcaption class="border-t-2 border-slate-950 bg-white px-3 py-2">
                                            <p class="text-xs font-bold text-slate-900">{{ $photo->caption }}</p>
                                            <p class="mt-0.5 text-[10px] uppercase tracking-widest text-slate-600">{{ $photo->created_at?->format('M j, Y') }}</p>
                                        </figcaption>
                                    </figure>
                                @endforeach
                            </div>
                        @elseif ($milestone->status === MilestoneStatus::Pending)
                            <p class="mt-4 border-2 border-dashed border-slate-300 px-4 py-3 text-xs font-bold uppercase tracking-widest text-slate-400">Upcoming — progress media will appear here</p>
                        @endif
                        </div>
                    </div>
                @endforeach

                {{-- Photo Lightbox Overlay Modal --}}
                <div x-show="open" x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 p-4 sm:p-6 backdrop-blur-sm"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click.self="open = false">
                    
                    <div class="box-brutal w-full max-w-4xl overflow-hidden bg-white" @click.stop>
                        <div class="flex items-center justify-between border-b-2 border-slate-950 bg-slate-950 px-4 py-3 text-white">
                            <span class="text-xs font-black uppercase tracking-widest text-yellow-400" x-text="activeMilestone"></span>
                            <button type="button" @click="open = false" class="text-xs font-black uppercase tracking-widest text-slate-300 hover:text-white">✕ Close (Esc)</button>
                        </div>
                        <div class="flex max-h-[70vh] items-center justify-center bg-black">
                            <img :src="activeUrl" :alt="activeCaption" class="max-h-[70vh] w-auto object-contain">
                        </div>
                        <div class="flex flex-col justify-between gap-2 border-t-2 border-slate-950 bg-brand-paper p-4 sm:flex-row sm:items-center">
                            <p class="text-sm font-bold text-slate-900" x-text="activeCaption"></p>
                            <span class="text-xs font-bold uppercase tracking-widest text-emerald-800" x-text="activeDate"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{--
                ============== CLIENT REFERRAL CARD ==============
                Deliberately below the timeline. It used to sit between the
                progress bar and the step track, which split the status story in
                half and made the page read as stacked widgets. It stays above
                invoices so it is still well inside the fold on a normal project.
            --}}
            <div data-reveal class="mt-12 box-brutal" x-data="{ copied: false }">
                <div class="bg-slate-950 p-6 text-white">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="inline-block bg-yellow-400 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-slate-950">🎁 Client Referral Reward</span>
                            <h3 class="mt-2 text-xl font-black uppercase tracking-tight text-white">Refer a Neighbor & Get $100 Credit</h3>
                            <p class="mt-1 text-xs text-slate-300">Share your custom referral link with a neighbor. When they request an estimate, you both receive a $100 project credit.</p>
                        </div>
                        <div class="shrink-0">
                            @php $refUrl = url('/estimate?ref='.$project->referral_code); @endphp
                            <button type="button"
                                    @click="navigator.clipboard.writeText('{{ $refUrl }}'); copied = true; setTimeout(() => copied = false, 3000)"
                                    class="btn-brutal bg-yellow-400 px-5 py-3 text-xs font-black uppercase tracking-wider text-slate-950 hover:bg-yellow-300">
                                <span x-text="copied ? '✓ Link Copied!' : '📋 Copy Referral Link'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============== INVOICES & BILLING SECTION ============== --}}
            @if ($project->invoices->isNotEmpty())
                <div class="mt-12">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-black uppercase tracking-tight text-slate-900">Invoices & Billing</h2>
                        <span class="text-xs font-bold uppercase tracking-widest text-slate-500">{{ $project->invoices->count() }} Invoice(s) Issued</span>
                    </div>

                    <div class="space-y-4">
                        @foreach ($project->invoices as $inv)
                            <div class="box-brutal p-5 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-3">
                                        <span class="font-mono text-sm font-black text-slate-950">#{{ $inv->invoice_number }}</span>
                                        @php
                                            $badgeClass = match ($inv->status?->value ?? 'draft') {
                                                'paid' => 'bg-emerald-900 text-yellow-400',
                                                'sent' => 'bg-yellow-400 text-slate-950',
                                                'overdue' => 'bg-rose-600 text-white',
                                                default => 'bg-slate-200 text-slate-700',
                                            };
                                        @endphp
                                        <span class="border-2 border-slate-950 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-widest {{ $badgeClass }}">
                                            {{ $inv->status?->getLabel() ?? '' }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-600">
                                        Issued: {{ $inv->issue_date?->format('M j, Y') }} · Due: {{ $inv->due_date?->format('M j, Y') ?? 'Upon Receipt' }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="font-mono text-xl font-black text-slate-950">${{ number_format($inv->total, 2) }}</span>
                                    <a href="{{ route('invoices.show', $inv->unique_access_token) }}" target="_blank" class="btn-brutal bg-yellow-400 px-4 py-2 text-xs font-black uppercase tracking-wider text-slate-950">
                                        View / Print ↗
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <p class="mt-10 text-center text-xs text-slate-400">
                This is your private project link. Questions? Call <a href="{{ 'tel:+1'.preg_replace('/\D/', '', (string) setting('primary_phone')) }}" class="font-bold text-slate-700 hover:text-slate-900">{{ setting('primary_phone') }}</a>.
            </p>
        </div>
    </section>
@endsection
