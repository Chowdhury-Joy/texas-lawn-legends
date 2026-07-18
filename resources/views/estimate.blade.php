@extends('layouts.app')

@section('content')
    <section class="bg-brand-paper">
        <div class="mx-auto max-w-7xl px-6 py-14">
            <div class="mx-auto mb-10 max-w-2xl text-center">
                <span class="inline-block bg-emerald-900 px-3 py-1 text-xs font-black uppercase tracking-widest text-yellow-400">Instant Estimate Engine</span>
                <h1 class="mt-5 text-4xl font-black uppercase leading-tight tracking-tight text-slate-900 sm:text-5xl">Get Your Digital Valuation</h1>
                <p class="mt-3 text-slate-600">Two minutes to a verified local price range — and a booked site visit if you want one.</p>
            </div>

            <livewire:estimator-wizard />
        </div>
    </section>
@endsection
