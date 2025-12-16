@extends('layouts.app')

@section('title', 'Too Many Requests')
@section('content')
    <div class="text-center py-16">
        <h1 class="text-6xl font-bold text-gray-300 mb-4">429</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Too Many Requests</h2>
        <p class="text-gray-600 mb-8">You've made too many requests. Please wait a moment and try again.</p>
        <a href="{{ route('home') }}" class="inline-block px-6 py-3 bg-black text-white hover:bg-gray-800 transition">
            Return Home
        </a>
    </div>
@endsection
