@extends('layouts.app')

@section('content')
    <section class="mx-auto flex max-w-3xl flex-col items-center px-6 py-24 text-center">
        <span class="mb-6 inline-block bg-emerald-900 px-3 py-1 text-xs font-bold uppercase tracking-widest text-yellow-400">Coming Online</span>
        <h1 class="text-4xl font-black uppercase tracking-tight text-slate-900 sm:text-5xl">{{ $heading }}</h1>
        <p class="mt-6 max-w-xl text-lg text-slate-600">{{ $body }}</p>
        <a href="{{ url('/') }}" class="btn-brutal mt-10 bg-yellow-400 px-6 py-3 text-sm text-slate-950">← Back to Home</a>
    </section>
@endsection
