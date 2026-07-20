<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} — {{ setting('site_name', 'Texas Lawn Legends') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; }
            .print-container { border: none !important; shadow: none !important; max-width: 100% !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900 antialiased min-h-screen py-10 px-4 sm:px-6">

    <div class="mx-auto max-w-3xl no-print mb-6 flex items-center justify-between">
        <a href="javascript:history.back()" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-600 hover:text-slate-900">
            ← Back
        </a>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 border-2 border-slate-950 bg-yellow-400 px-4 py-2 text-xs font-black uppercase tracking-wider text-slate-950 shadow-[2px_2px_0_0_#0f172a] hover:bg-yellow-300">
                🖨️ Print / Save as PDF
            </button>
        </div>
    </div>

    <div class="print-container mx-auto max-w-3xl border-4 border-slate-950 bg-white p-8 sm:p-12 shadow-[8px_8px_0_0_#0f172a]">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b-4 border-slate-950 pb-6 mb-8 gap-4">
            <div>
                <span class="inline-block bg-slate-950 px-3 py-1 text-xs font-black uppercase tracking-widest text-yellow-400 mb-2">
                    {{ setting('site_name', 'Texas Lawn Legends') }}
                </span>
                <h1 class="text-3xl font-black tracking-tight text-slate-950">INVOICE</h1>
                <p class="text-sm font-mono text-slate-600 mt-1">#{{ $invoice->invoice_number }}</p>
            </div>
            <div class="text-right sm:text-right">
                @php
                    $statusColor = match ($invoice->status?->value ?? 'draft') {
                        'paid' => 'bg-emerald-500 text-white',
                        'sent' => 'bg-yellow-400 text-slate-950',
                        'overdue' => 'bg-rose-600 text-white',
                        default => 'bg-slate-300 text-slate-800',
                    };
                @endphp
                <span class="inline-block px-3 py-1 text-xs font-black uppercase tracking-widest border-2 border-slate-950 {{ $statusColor }}">
                    {{ $invoice->status?->getLabel() ?? strtoupper($invoice->status) }}
                </span>
            </div>
        </div>

        <!-- Addresses / Metadata -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-8 text-sm">
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Billed To</p>
                <p class="font-bold text-slate-950 text-base">{{ $invoice->client_name }}</p>
                @if ($invoice->client_email)
                    <p class="text-slate-600">{{ $invoice->client_email }}</p>
                @endif
                @if ($invoice->project)
                    <p class="text-xs font-semibold text-slate-500 mt-2">Project: {{ $invoice->project->project_title }} ({{ $invoice->project->neighborhood }})</p>
                @endif
            </div>
            <div class="sm:text-right">
                <div class="space-y-1">
                    <p><span class="text-slate-500">Issue Date:</span> <strong class="font-mono">{{ $invoice->issue_date?->format('M j, Y') }}</strong></p>
                    <p><span class="text-slate-500">Due Date:</span> <strong class="font-mono text-slate-950">{{ $invoice->due_date?->format('M j, Y') ?? 'Upon Receipt' }}</strong></p>
                    <p><span class="text-slate-500">Contact Phone:</span> <strong>{{ setting('primary_phone', '(214) 617-7725') }}</strong></p>
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="border-2 border-slate-950 mb-8 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-950 text-white font-bold uppercase tracking-wider text-xs border-b-2 border-slate-950">
                    <tr>
                        <th class="py-3 px-4">Description</th>
                        <th class="py-3 px-4 text-center">Qty</th>
                        <th class="py-3 px-4 text-right">Unit Price</th>
                        <th class="py-3 px-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($invoice->items as $item)
                        <tr>
                            <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $item->description }}</td>
                            <td class="py-3.5 px-4 text-center font-mono">{{ number_format($item->quantity, 2) }}</td>
                            <td class="py-3.5 px-4 text-right font-mono">${{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-950">${{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 px-4 text-center text-slate-500 italic">No line items specified.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Totals Summary -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 border-t-2 border-slate-950 pt-6">
            <div class="max-w-md">
                @if ($invoice->notes)
                    <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Notes & Payment Instructions</p>
                    <p class="text-xs text-slate-700 whitespace-pre-line bg-slate-50 border border-slate-200 p-3">{{ $invoice->notes }}</p>
                @endif
            </div>
            <div class="w-full sm:w-64 space-y-2 text-sm">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-mono font-bold text-slate-900">${{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                @if ($invoice->tax > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Tax:</span>
                        <span class="font-mono font-bold text-slate-900">${{ number_format($invoice->tax, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-base font-black text-slate-950 border-t-2 border-slate-950 pt-2">
                    <span>TOTAL DUE:</span>
                    <span class="font-mono text-lg text-emerald-700">${{ number_format($invoice->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-12 border-t border-slate-200 pt-6 text-center text-xs text-slate-500">
            <p class="font-bold text-slate-800">{{ setting('site_name', 'Texas Lawn Legends LLC') }} · Premier Landscape Design & Operations</p>
            <p class="mt-1">Dallas, TX · {{ setting('primary_phone', '(214) 617-7725') }}</p>
        </div>

    </div>

</body>
</html>
