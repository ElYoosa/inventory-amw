@extends('layouts.guest')

@section('content')
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-[#002B5B] via-[#003B7A] to-[#E5B80B] animate-gradient"></div>
        <div class="absolute inset-0 bg-white/10 backdrop-blur-[2px]"></div>

        <div class="relative w-full max-w-md bg-white/95 rounded-2xl shadow-2xl p-8 border border-[#E5B80B]/30 z-10">
            <div class="relative flex flex-col items-center mb-6">
                <img src="{{ asset('images/Logo Anamta Memanjang.avif') }}" alt="Logo ANAMTA" class="w-44 mb-4 drop-shadow-md">
                <h1 class="text-xl font-bold text-[#002B5B]">Reset Password Akun ANAMTA</h1>
                <p class="text-gray-500 text-sm text-center mt-1">Masukkan <strong>username</strong> untuk akun yang ingin
                    direset</p>
            </div>

            @if (session('status'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-4">
                    <label for="username" class="block text-sm font-semibold text-[#002B5B]">Username</label>
                    <input id="username" type="text" name="username" required autofocus
                        class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#E5B80B] focus:border-[#002B5B] transition">
                </div>

                <button id="resetButton" type="submit"
                    class="w-full bg-gradient-to-r from-[#002B5B] to-[#003B7A] text-white py-2 rounded-lg font-semibold tracking-wide shadow-md hover:opacity-90 transition">
                    <span id="buttonText">Kirim Link Reset</span>
                </button>

                <script>
                    document.querySelector('form').addEventListener('submit', () => {
                        document.getElementById('buttonText').textContent = 'Mengirim...';
                        document.getElementById('resetButton').disabled = true;
                    });
                </script>

                <div class="text-center mt-6">
                    <a href="{{ route('login') }}" class="text-sm text-[#002B5B] hover:text-[#E5B80B]">
                        ← Kembali ke halaman login
                    </a>
                </div>
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
