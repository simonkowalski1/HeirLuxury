@extends('layouts.app')

@section('title', 'Page Not Found')
@section('content')
    <h1 class="text-3xl font-bold mb-4">404 - Page Not Found</h1>
    <p class="text-gray-700 mb-4">Sorry, we couldn’t find the page you’re looking for.</p>
    <a href="{{ route('home') }}" class="text-blue-600 hover:underline">Go back home</a>
@endsection
