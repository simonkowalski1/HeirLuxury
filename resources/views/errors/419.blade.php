@extends('layouts.app')

@section('title', 'Page Expired')
@section('content')
    <div class="text-center py-16">
        <h1 class="text-6xl font-bold text-gray-300 mb-4">419</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Page Expired</h2>
        <p class="text-gray-600 mb-8">Your session has expired. Please refresh the page and try again.</p>
        <a href="{{ url()->previous() }}" class="inline-block px-6 py-3 bg-black text-white hover:bg-gray-800 transition">
            Go Back
        </a>
    </div>
@endsection
