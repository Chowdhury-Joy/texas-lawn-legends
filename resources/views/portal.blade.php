@extends('layouts.app')

@php $noindex = true; @endphp

@section('content')
    <section class="bg-brand-paper">
        <div class="layout-container space-inline py-16">
            <div class="mx-auto mb-10 max-w-2xl text-center">
                <span data-reveal class="inline-block bg-emerald-900 px-3 py-1 text-xs font-black uppercase tracking-widest text-yellow-400">Member Portal</span>
                <h1 data-reveal class="mt-5 text-4xl font-black uppercase leading-tight tracking-tight text-slate-900 sm:text-5xl">Seasonal Member Portal</h1>
                <p data-reveal class="mt-3 text-slate-600">Members unlock exclusive seasonal add-ons and one-click ordering.</p>
            </div>

            <livewire:portal-gate />
        </div>
    </section>
@endsection
