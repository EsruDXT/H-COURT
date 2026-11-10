@extends('layouts.admin')

@section('title', 'H-Court - Tambah Lapangan')

@section('content')
  <section class="mb-8">
    <p class="text-sm tracking-[0.15em] text-brand font-semibold mb-2">KELOLA LAPANGAN</p>
    <h2 class="font-serif text-4xl font-bold text-gray-900 mb-3">Tambah lapangan</h2>
    <p class="text-gray-500">Lengkapi data lapangan baru.</p>
  </section>

  @include('admin.courts._form', [
      'action' => route('admin.courts.store'),
      'submit' => 'Tambah Lapangan',
  ])
@endsection
