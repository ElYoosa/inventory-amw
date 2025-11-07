@foreach ($notifications as $notif)
    <tr class="{{ $notif->status === 'new' ? 'table-warning' : '' }}">
        <td>{{ $loop->iteration + ($notifications->perPage() * ($notifications->currentPage() - 1)) }}</td>
        <td>{{ optional($notif->notified_at)->format('d M Y H:i') ?? $notif->created_at->format('d M Y H:i') }}</td>
        <td>{{ $notif->item->name ?? '-' }}</td>
        <td>
            @php
                $level = $notif->level;
                $label = $notif->label;
                $icon = $notif->icon;
                $badgeClass = match($level) {
                    'danger' => 'bg-danger-subtle text-danger',
                    'warning' => 'bg-warning-subtle text-warning',
                    'info' => 'bg-info-subtle text-info',
                    default => 'bg-secondary-subtle text-secondary',
                };
            @endphp
            <span class="badge {{ $badgeClass }}"><i class="bi {{ $icon }}"></i> {{ $label }}</span>
        </td>
        <td>{{ $notif->message }}</td>
        <td>
            @if ($notif->status === 'new')
                <span class="badge bg-warning text-dark">Baru</span>
            @else
                <span class="badge bg-secondary">Dibaca</span>
            @endif
        </td>
        <td class="text-end">
            @if ($notif->status === 'new')
                <button type="button" class="btn btn-sm btn-outline-success btn-mark-read" data-id="{{ $notif->id }}">
                    <i data-lucide="check-circle"></i> Tandai Dibaca
                </button>
            @endif
            <button type="button" class="btn btn-sm btn-outline-danger btn-delete ms-1" data-id="{{ $notif->id }}">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
@endforeach
