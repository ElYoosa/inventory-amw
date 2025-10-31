@if (auth()->user()->role === 'admin')
    <li class="mt-2">
        <a href="{{ route('activity.logs') }}" class="flex items-center gap-2 text-[#002B5B] hover:text-[#E5B80B]">
            📜 <span>Riwayat Login</span>
        </a>
    </li>
@endif
