<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory ANAMTA</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ✅ Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- ✅ DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <script src="https://unpkg.com/lucide-icons@latest"></script>

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            background: #f5f6fa;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            backdrop-filter: blur(14px);
            border-right: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 25px 18px;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            transition: all 0.3s ease;
        }

        .sidebar.admin {
            background: linear-gradient(180deg, #002B5Bcc 0%, #003B7Acc 100%);
        }

        .sidebar.manager {
            background: linear-gradient(180deg, #0F766Ecc 0%, #22C55Ecc 100%);
        }

        .sidebar.staff {
            background: linear-gradient(180deg, #CA8A04cc 0%, #FACC15cc 100%);
        }

        .sidebar-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar-header img {
            width: 160px;
            margin-bottom: 10px;
        }

        .sidebar-header h5 {
            color: #fff;
            font-weight: 600;
            margin: 0;
        }

        .menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .menu li {
            margin-bottom: 6px;
        }

        .menu li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: #fff;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.25s ease;
            position: relative;
        }

        .menu li a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(4px);
        }

        .menu li a.active {
            background: rgba(255, 255, 255, 0.25);
            font-weight: 600;
        }

        .menu li a.active::before {
            content: '';
            position: absolute;
            left: 6px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            border-radius: 4px;
            background: #fff;
        }

        [data-lucide],
        .sidebar svg {
            width: 18px !important;
            height: 18px !important;
            stroke-width: 2.2 !important;
        }

        .logout {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 20px;
        }

        .logout a,
        .logout button {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.15);
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            transition: all 0.25s ease;
        }

        .logout a:hover,
        .logout button:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 30px;
            background: linear-gradient(180deg, #ffffff 0%, rgba(245, 246, 250, 0.9) 100%);
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .toggle-btn {
                position: fixed;
                top: 15px;
                left: 15px;
                background: #002B5B;
                color: #fff;
                border: none;
                border-radius: 8px;
                padding: 8px 10px;
                z-index: 1100;
            }

            .main-content {
                margin-left: 0;
            }
        }

        .pagination .page-link {
            transition: all 0.2s ease-in-out;
            border-radius: 8px;
        }

        .pagination .page-link:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        .pagination .active .page-link {
            color: #fff !important;
        }
    </style>

    @php
        $role = auth()->user()->role ?? 'guest';
        $themeColor = match ($role) {
            'admin' => '#003B7A',
            'manager' => '#0F766E',
            'staff' => '#CA8A04',
            default => '#1E293B',
        };
    @endphp

    <style>
        :root {
            --theme-color: {{ $themeColor }};
        }

        .text-theme {
            color: var(--theme-color) !important;
        }

        .bg-theme {
            background-color: var(--theme-color) !important;
        }

        .border-theme {
            border-color: var(--theme-color) !important;
        }
    </style>
</head>

<body>
    {{-- ===== SIDEBAR ===== --}}
    @php $role = auth()->user()->role ?? 'guest'; @endphp
    <button class="toggle-btn d-md-none"><i data-lucide="menu"></i></button>

    <aside class="sidebar {{ $role }}">
        <div>
            <div class="sidebar-header">
                <img src="{{ asset('images/Logo Anamta Memanjang.avif') }}" alt="Logo ANAMTA">
                <h5>
                    @if ($role === 'admin')
                        Admin Panel
                    @elseif ($role === 'manager')
                        Manager Panel
                    @elseif ($role === 'staff')
                        Staff Panel
                    @else
                        Inventory ANAMTA
                    @endif
                </h5>
            </div>

            <ul class="menu">
                <li><a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                        <i data-lucide="home"></i> Dashboard</a></li>

                @if ($role === 'admin')
                    <li><a href="{{ route('categories.index') }}"
                            class="{{ request()->is('categories*') ? 'active' : '' }}">
                            <i data-lucide="layers"></i> Kategori</a></li>
                    <li><a href="{{ route('items.index') }}" class="{{ request()->is('items*') ? 'active' : '' }}">
                            <i data-lucide="box"></i> Barang</a></li>
                    <li><a href="{{ route('activity.index') }}"
                            class="{{ request()->is('activity-log') ? 'active' : '' }}">
                            <i data-lucide="file-text"></i> Activity Log</a></li>
                @endif

                @if (in_array($role, ['admin', 'staff']))
                    <li><a href="{{ route('in-transactions.index') }}"
                            class="{{ request()->is('in-transactions*') ? 'active' : '' }}">
                            <i data-lucide="arrow-down-circle"></i> Transaksi Masuk</a></li>
                    <li><a href="{{ route('out-transactions.index') }}"
                            class="{{ request()->is('out-transactions*') ? 'active' : '' }}">
                            <i data-lucide="arrow-up-circle"></i> Transaksi Keluar</a></li>
                @endif

                @if (in_array($role, ['admin', 'manager']))
                    <li><a href="{{ route('reports.distribusi.index') }}"
                            class="{{ request()->is('reports*') ? 'active' : '' }}">
                            <i data-lucide="bar-chart-3"></i> Laporan</a></li>
                @endif

                @if ($role === 'manager')
                    @php
                        $notifCount = \App\Models\Notification::forManager()->where('status', 'new')->count();
                    @endphp
                    <li>
                        <a href="{{ route('notifications.index') }}"
                            class="{{ request()->is('notifications*') ? 'active' : '' }} d-flex align-items-center gap-2">
                            <i data-lucide="bell"></i> <span>Notifikasi</span>
                            <span id="notifBadge" class="badge bg-danger ms-auto {{ $notifCount ? '' : 'd-none' }}">{{ $notifCount }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="logout">
            <a href="{{ route('profile.edit') }}"><i data-lucide="user"></i> Profil</a>
            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit"><i data-lucide="log-out"></i> Logout</button>
            </form>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="main-content">@yield('content')</main>

    {{-- ✅ SCRIPT ORDER FIXED --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            document.querySelector('.toggle-btn')?.addEventListener('click', () => {
                document.querySelector('.sidebar').classList.toggle('show');
            });

            // ===== Realtime Notif Count (Manager) =====
            const badge = document.getElementById('notifBadge');
            async function refreshNotifCount() {
                try {
                    const res = await fetch('{{ route('notifications.count') }}', { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;
                    const data = await res.json();
                    const count = Number(data.count || 0);
                    if (badge) {
                        badge.textContent = count;
                        if (count > 0) {
                            badge.classList.remove('d-none');
                        } else {
                            badge.classList.add('d-none');
                        }
                    }
                } catch (e) { /* no-op */ }
            }

            // Jalankan saat load dan setiap 10 detik
            refreshNotifCount();
            setInterval(refreshNotifCount, 10000);
        });
    </script>

    {{-- ✅ Script tambahan halaman --}}
    @stack('scripts')
</body>

</html>
