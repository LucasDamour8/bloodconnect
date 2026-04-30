@extends('layouts.guest')
@section('content')
<div class="min-h-screen bg-red-50/40 flex flex-col items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 w-full max-w-md p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Reset Password</h2>
        <p class="text-gray-500 text-sm mb-6">Enter your email to receive a secure reset link.</p>

        @if (session('status'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none @error('email') border-red-400 @enderror" required>
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }} </p> @enderror
            </div>
            <button type="submit" class="w-full bg-red-600 text-white py-3.5 rounded-xl font-semibold text-sm hover:bg-red-700 transition">
                Send Link
            </button>
        </form>
    </div>
</div>
@endsection