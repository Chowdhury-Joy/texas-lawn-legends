<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proposal - {{ $proposal->lead?->name ?? (setting('site_name') ?: 'Proposal') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans text-slate-900 antialiased min-h-screen py-10 px-4 sm:px-6">
    <div class="mx-auto max-w-3xl border-4 border-slate-950 bg-white p-8 sm:p-12 shadow-[8px_8px_0_0_#0f172a]">
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b-4 border-slate-950 pb-6 mb-8 gap-4">
            <div>
                <span class="inline-block bg-slate-950 px-3 py-1 text-xs font-black uppercase tracking-widest text-yellow-400 mb-2">
                    {{ setting('site_name') ?: 'Proposal' }}
                </span>
                <h1 class="text-3xl font-black tracking-tight text-slate-950">PROPOSAL</h1>
                <p class="text-sm text-slate-600 mt-1">{{ setting('header_tagline') ?: niche_label('tagline_fallback') }}</p>
            </div>
            <div class="text-right sm:text-right">
                @php
                    $statusColor = match ($proposal->status->value) {
                        'accepted' => 'bg-emerald-500 text-white',
                        'sent' => 'bg-yellow-400 text-slate-950',
                        'declined' => 'bg-rose-600 text-white',
                        default => 'bg-slate-300 text-slate-800',
                    };
                @endphp
                <span class="inline-block px-3 py-1 text-xs font-black uppercase tracking-widest border-2 border-slate-950 {{ $statusColor }}">
                    {{ $proposal->status->getLabel() }}
                </span>
                <p class="text-xs text-slate-500 mt-2">Proposal Date: {{ $proposal->created_at->format('M j, Y') }}</p>
                @if($proposal->expires_at)
                    <p class="text-xs font-bold text-rose-600 mt-1">Valid Until: {{ $proposal->expires_at->format('M j, Y') }}</p>
                @endif
            </div>
        </header>

        <section class="mb-10">
            <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Prepared For</p>
            <p class="text-lg font-bold text-slate-950">{{ $proposal->lead?->name ?? 'Valued Client' }}</p>
            @if($proposal->lead?->address)
                <p class="text-slate-500">{{ $proposal->lead->address }}</p>
            @endif
        </section>

        <!-- Builder Blocks -->
        <div class="space-y-12">
            @if($proposal->content)
                @foreach($proposal->content as $block)
                    @if($block['type'] === 'text_block')
                        <div class="prose prose-slate max-w-none">
                            {!! $block['data']['content'] !!}
                        </div>
                    @elseif($block['type'] === 'pricing_table')
                        <div class="border-2 border-slate-950 overflow-hidden">
                            <div class="bg-slate-950 px-4 py-3">
                                <h3 class="text-xs font-black uppercase tracking-widest text-yellow-400">{{ $block['data']['title'] ?? 'Itemized Costs' }}</h3>
                            </div>
                            <table class="w-full text-left text-sm">
                                <tbody class="divide-y divide-slate-200">
                                    @if(isset($block['data']['items']) && is_array($block['data']['items']))
                                        @foreach($block['data']['items'] as $itemBlock)
                                            @if($itemBlock['type'] === 'line_item')
                                                <tr>
                                                    <td class="px-4 py-3 text-slate-700">{{ $itemBlock['data']['description'] }}</td>
                                                    <td class="px-4 py-3 text-right font-mono font-bold text-slate-950">${{ number_format($itemBlock['data']['amount'], 2) }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @elseif($block['type'] === 'image_showcase')
                        <div class="space-y-4">
                            @if(isset($block['data']['images']) && is_array($block['data']['images']))
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($block['data']['images'] as $image)
                                        <img src="{{ public_url($image) }}" class="object-cover w-full h-64 border-2 border-slate-950" alt="Showcase Image">
                                    @endforeach
                                </div>
                            @endif
                            @if(!empty($block['data']['caption']))
                                <p class="text-sm text-center text-slate-500 italic">{{ $block['data']['caption'] }}</p>
                            @endif
                        </div>
                    @endif
                @endforeach
            @endif
        </div>

        <div class="mt-12 flex justify-between items-center border-t-2 border-slate-950 pt-8">
            <p class="text-sm text-slate-500">Thank you for considering {{ setting('site_name') ?: 'us' }}.</p>
            <div class="text-right">
                <p class="text-xs font-black uppercase tracking-widest text-slate-400">Total Investment</p>
                <p class="text-4xl font-black text-slate-950 mt-1">${{ number_format($proposal->total_amount, 2) }}</p>
            </div>
        </div>

        @if($proposal->status->value === 'sent')
            <div class="mt-12 p-6 bg-slate-50 border-2 border-slate-950 text-center">
                <h3 class="text-lg font-black uppercase tracking-tight mb-4 text-slate-950">Ready to move forward?</h3>
                <div class="flex justify-center gap-4">
                    <form method="POST" action="{{ route('proposals.accept', ['token' => $proposal->unique_token]) }}">
                        @csrf
                        <button type="submit" class="border-2 border-slate-950 bg-yellow-400 px-6 py-3 text-sm font-black uppercase tracking-wide text-slate-950 shadow-[2px_2px_0_0_#0f172a] hover:bg-yellow-300">Accept Proposal</button>
                    </form>
                    <form method="POST" action="{{ route('proposals.decline', ['token' => $proposal->unique_token]) }}">
                        @csrf
                        <button type="submit" class="border-2 border-slate-950 bg-white px-6 py-3 text-sm font-black uppercase tracking-wide text-slate-700 hover:bg-slate-100">Decline</button>
                    </form>
                </div>
                <p class="text-xs text-slate-400 mt-4">Accepting this proposal digitally acts as your signature.</p>
            </div>
        @endif
    </div>
</body>
</html>
