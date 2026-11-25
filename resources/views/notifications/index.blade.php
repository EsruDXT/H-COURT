@extends('layouts.app')

@section('title', 'Notifikasi - H-COURT')

@section('content')
<div class="min-h-screen bg-[#F7F3EC] py-10">
    <div class="mx-auto max-w-4xl px-4">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-amber-700">Pusat informasi</p>
                <h1 class="font-serif text-3xl font-semibold text-neutral-900">Notifikasi</h1>
                <p class="mt-2 text-sm text-neutral-500">Informasi terbaru mengenai aktivitas reservasi H-COURT.</p>
            </div>
            @if (auth()->user()->unreadNotifications()->exists())
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button class="rounded-full border border-neutral-300 bg-white px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Tandai semua dibaca</button>
                </form>
            @endif
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-xl bg-emerald-50 p-3 text-sm text-emerald-700">{{ session('status') }}</div>
        @endif

        <div class="space-y-3">
            @forelse ($notifications as $notification)
                <article class="rounded-2xl border {{ $notification->read_at ? 'border-neutral-200 bg-white' : 'border-amber-200 bg-amber-50/70' }} p-5 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                @if (!empty($notification->data['url']))
                                    <a href="{{ $notification->data['url'] }}" class="font-semibold text-neutral-900 underline decoration-neutral-300 underline-offset-4 hover:text-amber-800">
                                        {{ $notification->data['title'] ?? 'Notifikasi H-COURT' }}
                                    </a>
                                @else
                                    <h2 class="font-semibold text-neutral-900">{{ $notification->data['title'] ?? 'Notifikasi H-COURT' }}</h2>
                                @endif
                                @unless ($notification->read_at)
                                    <span class="rounded-full bg-amber-700 px-2 py-0.5 text-[10px] font-semibold text-white">BARU</span>
                                @endunless
                            </div>
                            <p class="mt-1 text-sm leading-6 text-neutral-600">{{ $notification->data['message'] ?? '' }}</p>
                            <p class="mt-2 text-xs text-neutral-400">{{ $notification->created_at->locale('id')->translatedFormat('d F Y, H:i') }}</p>
                        </div>
                        @unless ($notification->read_at)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                @method('PATCH')
                                <button class="whitespace-nowrap text-sm font-semibold text-amber-800 underline">Tandai dibaca</button>
                            </form>
                        @endunless
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-neutral-200 bg-white p-10 text-center text-sm text-neutral-500">Belum ada notifikasi.</div>
            @endforelse
        </div>
        <div class="mt-6">{{ $notifications->links() }}</div>
    </div>
</div>
@endsection
