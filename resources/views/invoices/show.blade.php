<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} — {{ setting('site_name') ?: 'Invoice' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; }
            .print-container { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900 antialiased min-h-screen py-10 px-4 sm:px-6">

    <div class="mx-auto max-w-3xl no-print mb-6 flex items-center justify-between">
        <a href="javascript:history.back()" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-600 hover:text-slate-900">
            ← Back
        </a>
        <button
            type="button"
            onclick="window.print()"
            class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold uppercase tracking-wider focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900"
        >
            Print / Save as PDF
        </button>
    </div>

    <div class="print-container mx-auto max-w-3xl bg-white p-8 sm:p-12">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 mb-8 border-b border-slate-200">
            <div>
                <span class="inline-block bg-slate-950 px-3 py-1 text-xs font-black uppercase tracking-widest text-yellow-400 mb-2">
                    {{ setting('site_name') ?: 'Invoice' }}
                </span>
                <h1 class="text-3xl font-black tracking-tight text-slate-950">INVOICE</h1>
                <p class="text-sm font-mono text-slate-600 mt-1">#{{ $invoice->invoice_number }}</p>
            </div>
            <div class="text-right">
                @php
                    $statusColor = match ($invoice->status?->value ?? 'draft') {
                        'paid' => 'bg-emerald-100 text-emerald-800',
                        'sent' => 'bg-amber-100 text-amber-900',
                        'overdue' => 'bg-rose-100 text-rose-800',
                        default => 'bg-slate-100 text-slate-700',
                    };
                @endphp
                <span class="inline-block px-3 py-1 text-xs font-bold uppercase tracking-widest rounded-sm {{ $statusColor }}">
                    {{ $invoice->status?->getLabel() ?? '' }}
                </span>
            </div>
        </div>

        {{-- Billing details --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-10 text-sm">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Billed To</p>
                <p class="font-bold text-slate-950 text-base">{{ $invoice->client_name }}</p>
                @if ($invoice->client_email)
                    <p class="text-slate-600 mt-0.5">{{ $invoice->client_email }}</p>
                @endif
                @if ($invoice->project)
                    <p class="text-sm text-slate-500 mt-2">Project: {{ $invoice->project->project_title }} ({{ $invoice->project->neighborhood }})</p>
                @endif
            </div>
            <div class="sm:text-right space-y-1">
                <p><span class="text-slate-500">Issue Date:</span> <strong class="font-mono text-slate-900">{{ $invoice->issue_date?->format('M j, Y') }}</strong></p>
                <p><span class="text-slate-500">Due Date:</span> <strong class="font-mono text-slate-900">{{ $invoice->due_date?->format('M j, Y') ?? 'Upon Receipt' }}</strong></p>
                <p><span class="text-slate-500">Contact Phone:</span> <strong class="text-slate-900">{{ setting('primary_phone', '(214) 617-7725') }}</strong></p>
            </div>
        </div>

        {{-- Line items --}}
        <div class="mb-10 overflow-x-auto">
            <table class="w-full min-w-[32rem] text-left text-sm border-collapse">
                <thead>
                    <tr class="border-b border-slate-300 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3 pr-4">Description</th>
                        <th class="py-3 px-4 text-center">Qty</th>
                        <th class="py-3 px-4 text-right">Unit Price</th>
                        <th class="py-3 pl-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($invoice->items as $item)
                        <tr>
                            <td class="py-3.5 pr-4 font-medium text-slate-900">{{ $item->description }}</td>
                            <td class="py-3.5 px-4 text-center font-mono text-slate-700">{{ number_format($item->quantity, 2) }}</td>
                            <td class="py-3.5 px-4 text-right font-mono text-slate-700">${{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-3.5 pl-4 text-right font-mono font-semibold text-slate-950">${{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-slate-500 italic">No line items specified.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Totals --}}
        <div class="flex justify-end pt-6 border-t border-slate-200">
            <div class="w-full sm:w-64 space-y-2 text-sm">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal</span>
                    <span class="font-mono font-medium text-slate-900">${{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                @if ($invoice->tax > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Tax</span>
                        <span class="font-mono font-medium text-slate-900">${{ number_format($invoice->tax, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between items-baseline pt-3 mt-1 border-t border-slate-200 text-base font-bold text-slate-950">
                    <span>Total Due</span>
                    <span class="font-mono text-lg text-emerald-700">${{ number_format($invoice->total, 2) }}</span>
                </div>
            </div>
        </div>

        @if ($invoice->notes)
            <div class="mx-auto mt-10 max-w-lg rounded-sm border border-slate-200 bg-slate-50 px-5 py-4 text-center">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Notes & Payment Instructions</p>
                <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $invoice->notes }}</p>
            </div>
        @endif

        {{-- Footer --}}
        <div class="mt-12 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
            <p class="font-semibold text-slate-700">{{ setting('site_name') ?: 'Business' }}@if (setting('header_tagline')) · {{ setting('header_tagline') }}@endif</p>
            <p class="mt-1">@if (setting('header_location') || setting('business_city')){{ setting('header_location') ?: setting('business_city') }} · @endif{{ setting('primary_phone') }}</p>
        </div>

    </div>

</body>
</html>
