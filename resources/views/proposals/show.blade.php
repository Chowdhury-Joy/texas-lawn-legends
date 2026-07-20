<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proposal - {{ $proposal->lead?->name ?? 'Texas Lawn Legends' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100 min-h-screen flex flex-col items-center py-10">
    <div class="w-full max-w-4xl bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-8 md:p-12">
        <header class="flex justify-between items-start border-b border-gray-100 dark:border-gray-800 pb-8 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-primary-600 dark:text-primary-400">Texas Lawn Legends</h1>
                <p class="text-sm text-gray-500 mt-1">Dallas' Premier Landscaping Service</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                    @if($proposal->status->value === 'draft') bg-gray-100 text-gray-800
                    @elseif($proposal->status->value === 'sent') bg-blue-100 text-blue-800
                    @elseif($proposal->status->value === 'accepted') bg-emerald-100 text-emerald-800
                    @elseif($proposal->status->value === 'declined') bg-rose-100 text-rose-800
                    @endif
                ">
                    {{ $proposal->status->getLabel() }}
                </span>
                <p class="text-xs text-gray-400 mt-2">Proposal Date: {{ $proposal->created_at->format('M j, Y') }}</p>
                @if($proposal->expires_at)
                    <p class="text-xs text-rose-500 mt-1">Valid Until: {{ $proposal->expires_at->format('M j, Y') }}</p>
                @endif
            </div>
        </header>

        <section class="mb-10">
            <h2 class="text-xl font-bold mb-2">Prepared For:</h2>
            <p class="text-lg">{{ $proposal->lead?->name ?? 'Valued Client' }}</p>
            @if($proposal->lead?->address)
                <p class="text-gray-500">{{ $proposal->lead->address }}</p>
            @endif
        </section>

        <!-- Builder Blocks -->
        <div class="space-y-12">
            @if($proposal->content)
                @foreach($proposal->content as $block)
                    @if($block['type'] === 'text_block')
                        <div class="prose prose-primary dark:prose-invert max-w-none">
                            {!! $block['data']['content'] !!}
                        </div>
                    @elseif($block['type'] === 'pricing_table')
                        <div class="rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden">
                            <div class="bg-gray-50 dark:bg-gray-800/50 px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                                <h3 class="text-sm font-bold uppercase tracking-wider">{{ $block['data']['title'] ?? 'Itemized Costs' }}</h3>
                            </div>
                            <table class="w-full text-left text-sm">
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @if(isset($block['data']['items']) && is_array($block['data']['items']))
                                        @foreach($block['data']['items'] as $itemBlock)
                                            @if($itemBlock['type'] === 'line_item')
                                                <tr>
                                                    <td class="px-4 py-3">{{ $itemBlock['data']['description'] }}</td>
                                                    <td class="px-4 py-3 text-right font-mono text-gray-900 dark:text-gray-100">${{ number_format($itemBlock['data']['amount'], 2) }}</td>
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
                                        <img src="{{ Storage::url($image) }}" class="rounded-lg object-cover w-full h-64 border border-gray-200 dark:border-gray-800 shadow-sm" alt="Showcase Image">
                                    @endforeach
                                </div>
                            @endif
                            @if(!empty($block['data']['caption']))
                                <p class="text-sm text-center text-gray-500 italic">{{ $block['data']['caption'] }}</p>
                            @endif
                        </div>
                    @endif
                @endforeach
            @endif
        </div>

        <div class="mt-12 flex justify-between items-center border-t border-gray-100 dark:border-gray-800 pt-8">
            <p class="text-sm text-gray-500">Thank you for considering Texas Lawn Legends.</p>
            <div class="text-right">
                <p class="text-sm font-bold uppercase tracking-wider text-gray-500">Total Investment</p>
                <p class="text-4xl font-extrabold text-primary-600 dark:text-primary-400 mt-1">${{ number_format($proposal->total_amount, 2) }}</p>
            </div>
        </div>

        @if($proposal->status->value === 'sent')
            <div class="mt-12 p-6 bg-gray-50 dark:bg-gray-800/30 rounded-lg border border-gray-200 dark:border-gray-800 text-center">
                <h3 class="text-lg font-bold mb-4">Ready to move forward?</h3>
                <div class="flex justify-center gap-4">
                    <form method="POST" action="{{ route('proposals.accept', ['token' => $proposal->unique_token]) }}">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg transition-colors shadow-sm">Accept Proposal</button>
                    </form>
                    <form method="POST" action="{{ route('proposals.decline', ['token' => $proposal->unique_token]) }}">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-lg transition-colors shadow-sm">Decline</button>
                    </form>
                </div>
                <p class="text-xs text-gray-400 mt-4">Accepting this proposal digitally acts as your signature.</p>
            </div>
        @endif
    </div>
</body>
</html>
