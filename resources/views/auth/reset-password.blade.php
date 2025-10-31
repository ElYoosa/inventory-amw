@extends('layouts.guest')

@section('content')
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-[#002B5B] via-[#003B7A] to-[#E5B80B] animate-gradient"></div>
        <div class="absolute inset-0 bg-white/10 backdrop-blur-[2px]"></div>

        <div class="relative w-full max-w-md bg-white/95 rounded-2xl shadow-2xl p-8 border border-[#E5B80B]/30 z-10">
            <div class="flex flex-col items-center mb-6">
                <img src="{{ asset('images/Logo Anamta Memanjang.avif') }}" alt="Logo ANAMTA" class="w-44 mb-4 drop-shadow-md">
                <h1 class="text-xl font-bold text-[#002B5B]">Atur Password Baru</h1>
            </div>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="mb-4">
                    <label for="email" class="block text-sm font-semibold text-[#002B5B]">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#E5B80B] focus:border-[#002B5B] transition">
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-semibold text-[#002B5B]">Password Baru</label>
                    <input id="password" type="password" name="password" required
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#E5B80B] focus:border-[#002B5B] transition">
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-semibold text-[#002B5B]">Konfirmasi
                        Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#E5B80B] focus:border-[#002B5B] transition">
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-[#002B5B] to-[#003B7A] text-white py-2 rounded-lg font-semibold tracking-wide shadow-md hover:opacity-90 transition">
                    Simpan Password
                </button>
            </form>
        </div>
    </div>

    <style>
        @keyframes gradientMove {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .animate-gradient {
            background-size: 300% 300%;
            animation: gradientMove 12s ease infinite;
        }
    </style>
@endsection
