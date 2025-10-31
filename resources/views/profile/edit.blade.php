@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 700px;">
            <div class="card-body">
                <h4 class="fw-bold text-theme mb-3">
                    <i data-lucide="user"></i> Profil Pengguna
                </h4>

                {{-- ✅ Update Informasi Profil --}}
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}"
                            required>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn text-white" style="background: var(--theme-color)">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                {{-- 🔒 Ganti Password --}}
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    <h6 class="fw-bold mb-3">Ganti Password</h6>

                    <div class="mb-3">
                        <label class="form-label">Password Lama</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-outline-theme w-100">
                        <i data-lucide="key"></i> Ubah Password
                    </button>
                </form>

                <hr class="my-4">

                {{-- 🗑️ Hapus Akun --}}
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')

                    <div class="text-center">
                        <h6 class="fw-bold text-danger">Hapus Akun</h6>
                        <p class="text-muted small mb-3">Akun Anda akan dihapus secara permanen.</p>

                        <input type="password" name="password" class="form-control mb-3"
                            placeholder="Masukkan password Anda untuk konfirmasi" required>

                        <button type="submit" class="btn btn-danger">
                            <i data-lucide="trash-2"></i> Hapus Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
    </script>
@endsection
