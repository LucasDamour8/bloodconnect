@extends('layouts.guest')

@section('content')
<div class="min-h-screen bg-red-50/40 flex flex-col items-center justify-center py-12 px-4">
    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center">
        <div class="mb-6 flex justify-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <h2 class="text-2xl font-black text-gray-900 mb-4">Verify Your Email</h2>
        <p class="text-sm text-gray-500 mb-8">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 bg-green-50 text-green-700 p-3 rounded-xl text-xs font-bold border border-green-100">
                A new verification link has been sent to your email address!
            </div>
        @endif

        <div class="space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-xl font-bold text-sm hover:bg-red-700 transition">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-400 hover:text-gray-600 underline">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</div>
@endsection