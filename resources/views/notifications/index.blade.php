@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', 'Notifikasi - H-COURT')

@section('content')

@php
    $unreadCount = auth()->user()->unreadNotifications()->count();
    $totalCount = auth()->user()->notifications()->count();
    $readCount = $totalCount - $unreadCount;
@endphp

<div @class([
    'w-full',
    'mx-auto max-w-6xl px-4 py-10' => ! auth()->user()->isAdmin(),
])>

<section class="mb-8">
    <p class="mb-2 text-sm font-semibold tracking-widest text-brand">
        PUSAT INFORMASI
    </p>

    <h2 class="font-serif text-3xl font-bold text-gray-900">
        Notifikasi
    </h2>

    <p class="mt-2 text-sm text-gray-500">
        Pantau informasi terbaru mengenai aktivitas dan reservasi Anda.
    </p>
</section>

{{-- Statistics --}}
<div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="rounded-xl border border-stone-200 bg-white p-5">
        <p class="text-xs text-gray-500">Total notifikasi</p>
        <p class="mt-2 font-serif text-3xl font-bold text-gray-900">
            {{ $totalCount }}
        </p>
    </div>

    <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
        <p class="text-xs text-amber-800">Belum dibaca</p>
        <p class="mt-2 font-serif text-3xl font-bold text-amber-800">
            {{ $unreadCount }}
        </p>
    </div>

    <div class="rounded-xl border border-stone-200 bg-white p-5">
        <p class="text-xs text-gray-500">Sudah dibaca</p>
        <p class="mt-2 font-serif text-3xl font-bold text-gray-900">
            {{ $readCount }}
        </p>
    </div>
</div>

{{-- Notification List --}}
<section class="overflow-hidden rounded-xl border border-stone-200 bg-white">

    <div class="flex flex-col gap-3 border-b border-stone-200 p-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">
                Semua notifikasi
            </h3>

            <p class="mt-1 text-xs text-gray-500">
                Informasi aktivitas akun dan reservasi Anda.
            </p>
        </div>

        @if ($unreadCount > 0)
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf

                <button type="submit"
                    class="rounded-lg bg-navy px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700">
                    Tandai semua dibaca
                </button>
            </form>
        @endif
    </div>

    @forelse ($notifications as $notification)

        @php
            $isUnread = is_null($notification->read_at);
            $title = $notification->data['title'] ?? 'Informasi reservasi';
            $message = $notification->data['message'] ?? 'Anda memiliki notifikasi baru.';
            $url = $notification->data['url'] ?? null;
        @endphp

        <div class="border-b border-stone-100 p-5 last:border-b-0
            {{ $isUnread ? 'bg-amber-50/40' : 'bg-white' }}">

            <div class="flex items-start gap-4">

                {{-- Notification Icon --}}
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full
                    {{ $isUnread ? 'bg-amber-100 text-brand' : 'bg-stone-100 text-gray-500' }}">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">

                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>

                {{-- Notification Content --}}
                <div class="min-w-0 flex-1">

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">

                                <h4 class="text-sm font-semibold text-gray-900">
                                    {{ $title }}
                                </h4>

                                @if ($isUnread)
                                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800">
                                        Baru
                                    </span>
                                @endif

                            </div>

                            <p class="mt-2 text-sm leading-6 text-gray-600">
                                {{ $message }}
                            </p>

                            <p class="mt-3 text-xs text-gray-400">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>

                        {{-- Notification Actions --}}
                        <div class="flex shrink-0 items-center gap-3">

                            @if ($url && $isUnread)

                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <input type="hidden" name="open" value="1">

                                    <button type="submit"
                                        class="text-sm font-semibold text-brand hover:text-amber-800">
                                        Lihat detail
                                    </button>
                                </form>

                            @elseif ($url)

                                <a href="{{ $url }}"
                                    class="text-sm font-semibold text-brand hover:text-amber-800">
                                    Lihat detail
                                </a>

                            @elseif ($isUnread)

                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                        class="text-sm font-semibold text-brand hover:text-amber-800">
                                        Tandai dibaca
                                    </button>
                                </form>

                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>

    @empty

        <div class="px-6 py-16 text-center">

            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-stone-100 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-8 w-8"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5">

                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>

            <h3 class="mt-5 font-serif text-xl font-bold text-gray-900">
                Belum ada notifikasi
            </h3>

            <p class="mt-2 text-sm text-gray-500">
                Notifikasi mengenai aktivitas dan reservasi Anda akan muncul di sini.
            </p>

        </div>

    @endforelse

</section>

{{-- Pagination --}}
@if ($notifications->hasPages())
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
@endif

</div>

@endsection
