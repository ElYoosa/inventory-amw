@extends('layouts.guest')

@section('content')
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-[#002B5B] via-[#003B7A] to-[#E5B80B] animate-gradient"></div>
        <div class="absolute inset-0 bg-white/10 backdrop-blur-[2px]"></div>

        <div class="relative w-full max-w-md bg-white/95 rounded-2xl shadow-2xl p-8 border border-[#E5B80B]/30 z-10">
            <div class="flex flex-col items-center mb-6">
                <img src="{{ asset('images/Logo Anamta Memanjang.avif') }}" alt="Logo ANAMTA"
                    class="w-44 mb-4 drop-shadow-md animate-fade-in">
                <h1 class="text-2xl font-bold text-[#002B5B] tracking-wide text-center animate-fade-in-delay">
                    Sistem Inventory ANAMTA
                </h1>
                <p class="text-gray-500 text-sm text-center animate-fade-in-delay2">
                    PT Annur Maknah Wisata
                </p>
            </div>

            {{-- 🔴 Tampilkan Pesan Error --}}
            @if ($errors->any())
                <div id="errorBox"
                    class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-md mb-4 animate-fade-in"
                    role="alert">
                    <strong class="font-semibold">Login gagal!</strong>
                    <p class="text-sm mt-1">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-5 mt-6">
                @csrf

                <div>
                    <label for="login" class="block text-sm font-semibold text-[#002B5B]">
                        Username atau Email
                    </label>
                    <input id="login" type="text" name="login" required autofocus value="{{ old('login') }}"
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2
                           focus:ring-[#E5B80B] focus:border-[#002B5B] transition"
                        placeholder="Masukkan username atau email perusahaan">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-[#002B5B]">Password</label>
                    <input id="password" type="password" name="password" required
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2
                           focus:ring-[#E5B80B] focus:border-[#002B5B] transition"
                        placeholder="Masukkan password Anda">
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="mr-2 text-[#002B5B] rounded focus:ring-[#E5B80B]">
                        <span class="text-gray-600">Ingat saya</span>
                    </label>

                    <a href="{{ route('password.request') }}" class="text-[#002B5B] hover:text-[#E5B80B] font-medium">
                        Lupa Password?
                    </a>
                </div>

                <button type="submit" id="loginButton"
                    class="w-full bg-gradient-to-r from-[#002B5B] to-[#003B7A]
                       text-white py-2 rounded-lg font-semibold tracking-wide shadow-md
                       hover:opacity-90 transition relative overflow-hidden">
                    <span id="loginText">Masuk</span>
                    <span id="loadingSpinner" class="hidden absolute inset-y-0 right-4 flex items-center">
                        <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                    </span>
                </button>
            </form>

            <p class="text-center text-xs text-gray-500 mt-8">
                © {{ date('Y') }} <span class="font-semibold text-[#002B5B]">ANAMTA</span> — PT Annur Maknah Wisata
            </p>
        </div>
    </div>

    <style>
        @keyframes gradientMove {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        .animate-gradient {
            background-size: 300% 300%;
            animation: gradientMove 12s ease infinite;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease forwards;
        }

        .animate-fade-in-delay {
            animation: fadeIn 1s ease forwards;
        }

        .animate-fade-in-delay2 {
            animation: fadeIn 1.2s ease forwards;
        }
    </style>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginButton');
            const txt = document.getElementById('loginText');
            const spinner = document.getElementById('loadingSpinner');
            btn.disabled = true;
            txt.textContent = 'Memproses...';
            spinner.classList.remove('hidden');
            btn.classList.add('opacity-70', 'cursor-not-allowed');
        });
    </script>
@endsection
