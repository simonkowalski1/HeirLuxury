@extends('layouts.app')

@section('title', 'Server Error')
@section('content')
    <div class="text-center py-16">
        <h1 class="text-6xl font-bold text-gray-300 mb-4">500</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Server Error</h2>
        <p class="text-gray-600 mb-8">Something went wrong on our end. Please try again later.</p>
        <a href="{{ route('home') }}" class="inline-block px-6 py-3 bg-black text-white hover:bg-gray-800 transition">
            Return Home
        </a>
    </div>
@endsection
