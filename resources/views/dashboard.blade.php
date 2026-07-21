@extends('layouts.app')

@php
    use App\Enums\MilestoneStatus;
    use Illuminate\Support\Facades\Storage;

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

                {{-- Overall progress bar --}}
                <div class="mt-6">
                    <div class="flex items-center justify-between text-xs font-black uppercase tracking-widest text-slate-500">
                        <span>Overall Progress</span>
                        <span>{{ $completedCount }} / {{ $totalCount }} milestones · {{ $pct }}%</span>
                    </div>
                    <div class="mt-2 h-4 w-full border-2 border-slate-950 bg-white">
                        <div class="h-full bg-yellow-400" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            </div>

            {{-- ============== CLIENT REFERRAL CARD ============== --}}
            <div data-reveal class="mt-8 box-brutal" x-data="{ copied: false }">
                <div class="bg-slate-950 p-6 text-white">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="inline-block bg-yellow-400 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-slate-950">🎁 Client Referral Reward</span>
                            <h3 class="mt-2 text-xl font-black uppercase tracking-tight text-white">Refer a Dallas Neighbor & Get $100 Credit</h3>
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


            {{-- ============== HORIZONTAL PROGRESS TRACK ============== --}}
            <div data-reveal class="mt-8 overflow-x-auto">
                <div class="flex min-w-max items-start gap-0">
                    @foreach ($milestones as $i => $milestone)
                        @php $meta = $statusMeta[$milestone->status->value] ?? $statusMeta['pending']; @endphp
                        <div class="flex items-start">
                            <div class="flex w-36 flex-col items-center text-center">
                                <div @class([
                                    'flex h-11 w-11 items-center justify-center border-2 border-slate-950 text-sm font-black',
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
                                <span class="mt-2 text-[11px] font-black uppercase leading-tight tracking-wide text-slate-900">{{ $milestone->title }}</span>
                                <span @class(['mt-1 px-1.5 py-0.5 text-[9px] font-black uppercase tracking-widest', $meta['badge']])>{{ $meta['label'] }}</span>
                            </div>
                            @unless ($loop->last)
                                <div @class([
                                    'mt-5 h-1 w-8 sm:w-12',
                                    'bg-emerald-900' => $milestone->status === MilestoneStatus::Completed,
                                    'bg-slate-300' => $milestone->status !== MilestoneStatus::Completed,
                                ])></div>
                            @endunless
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ============== MILESTONE TIMELINE ============== --}}
            <div class="mt-12 space-y-6"
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
                <h2 data-reveal class="text-2xl font-black uppercase tracking-tight text-slate-900">Build Timeline</h2>

                @foreach ($milestones as $milestone)
                    @php
                        $meta = $statusMeta[$milestone->status->value] ?? $statusMeta['pending'];
                        $stepPhotos = $photosByStep->get($milestone->title, collect());
                    @endphp
                    <div data-stagger style="--stagger-i: {{ $loop->index }}" class="box-brutal p-6">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span @class(['h-3 w-3 border border-slate-950', $meta['dot']])></span>
                                <h3 class="text-xl font-medium tracking-tighter text-slate-900">{{ $milestone->title }}</h3>
                            </div>
                            <span @class(['border-2 border-slate-950 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest', $meta['badge']])>{{ $meta['label'] }}</span>
                        </div>

                        @if ($milestone->description)
                            <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $milestone->description }}</p>
                        @endif

                        @if ($stepPhotos->isNotEmpty())
                            <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3">
                                @foreach ($stepPhotos as $photo)
                                    @php
                                        $hasImage = filled($photo->image_path);
                                        $photoUrl = $hasImage ? Storage::disk('public')->url($photo->image_path) : '';
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
                                            <p class="text-xs font-bold text-slate-800">{{ $photo->caption }}</p>
                                            <p class="mt-0.5 text-[10px] uppercase tracking-widest text-slate-400">{{ $photo->created_at?->format('M j, Y') }}</p>
                                        </figcaption>
                                    </figure>
                                @endforeach
                            </div>
                        @elseif ($milestone->status === MilestoneStatus::Pending)
                            <p class="mt-4 border-2 border-dashed border-slate-300 px-4 py-3 text-xs font-bold uppercase tracking-widest text-slate-400">Upcoming — progress media will appear here</p>
                        @endif
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
