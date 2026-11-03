<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>H-Court - Kelola Lapangan</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          brand: '#A8722F',
          navy: '#1E2534',
        }
      }
    }
  }
</script>
</head>
<body class="bg-[#F4F1EA] min-h-screen text-gray-800">

<div class="max-w-6xl mx-auto px-6 py-10">

  <!-- Header -->
  @include('layouts.partials.admin-header')

  <!-- Judul -->
  <section class="mb-8">
    <p class="text-sm tracking-[0.15em] text-brand font-semibold mb-2">KELOLA LAPANGAN</p>
    <h2 class="font-serif text-4xl md:text-5xl font-bold text-gray-900 mb-3">Data Lapangan Olahraga</h2>
    <p class="text-gray-500 max-w-md mb-6">Tambah, ubah, atau nonaktifkan lapangan yang bisa direservasi siswa.</p>

    <div class="flex flex-wrap items-center justify-between gap-4">
      <p class="text-sm text-gray-500"><span id="lapanganCount" class="font-semibold text-gray-900">3</span> lapangan terdaftar</p>
      <button onclick="openLapangan()" class="px-6 py-3 rounded-lg bg-navy text-white font-semibold text-sm">+ Tambah Lapangan</button>
    </div>
  </section>

  <!-- Kartu lapangan -->
  <div id="lapanganGrid" class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <div class="bg-white rounded-xl border border-stone-200 overflow-hidden" data-id="futsal">
      <img src="{{ asset('images/Lap_Futsal.jpg') }}" class="w-full h-56 object-cover" alt="Lapangan Futsal">
      <div class="p-5">
        <h3 class="text-xl font-semibold text-gray-900 mb-3">Lapangan Futsal</h3>
        <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
          <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
          07:00-21:00
        </div>
        <div class="flex items-center gap-2 text-gray-500 text-sm mb-4">
          <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/><circle cx="12" cy="11" r="2"/></svg>
          Outdoor - Sintetis
        </div>
        <div class="flex gap-3">
          <button onclick="openLapangan(this)" data-nama="Lapangan Futsal" data-kategori="Futsal" data-buka="07:00 AM" data-tutup="09:00 PM" data-lokasi="Outdoor - Sintetis" class="flex-1 py-2.5 rounded-lg border border-stone-300 text-gray-800 font-medium text-sm">Edit</button>
          <button onclick="hapusLapangan(this)" class="flex-1 py-2.5 rounded-lg bg-red-100 text-red-500 font-medium text-sm">Hapus</button>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-stone-200 overflow-hidden" data-id="basket">
      <img src="{{ asset('images/Lap_Basket.jpg') }}" class="w-full h-56 object-cover" alt="Lapangan Basket">
      <div class="p-5">
        <h3 class="text-xl font-semibold text-gray-900 mb-3">Lapangan Basket</h3>
        <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
          <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
          07:00-21:00
        </div>
        <div class="flex items-center gap-2 text-gray-500 text-sm mb-4">
          <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/><circle cx="12" cy="11" r="2"/></svg>
          Outdoor - Sintetis
        </div>
        <div class="flex gap-3">
          <button onclick="openLapangan(this)" data-nama="Lapangan Basket" data-kategori="Basket" data-buka="07:00 AM" data-tutup="09:00 PM" data-lokasi="Outdoor - Sintetis" class="flex-1 py-2.5 rounded-lg border border-stone-300 text-gray-800 font-medium text-sm">Edit</button>
          <button onclick="hapusLapangan(this)" class="flex-1 py-2.5 rounded-lg bg-red-100 text-red-500 font-medium text-sm">Hapus</button>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-stone-200 overflow-hidden" data-id="badminton">
      <img src="{{ asset('images/Lap_Badminton.jpg') }}" class="w-full h-56 object-cover" alt="Lapangan Badminton">
      <div class="p-5">
        <h3 class="text-xl font-semibold text-gray-900 mb-3">Lapangan Badminton</h3>
        <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
          <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
          07:00-21:00
        </div>
        <div class="flex items-center gap-2 text-gray-500 text-sm mb-4">
          <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/><circle cx="12" cy="11" r="2"/></svg>
          Outdoor - Sintetis
        </div>
        <div class="flex gap-3">
          <button onclick="openLapangan(this)" data-nama="Lapangan Badminton" data-kategori="Badminton" data-buka="07:00 AM" data-tutup="09:00 PM" data-lokasi="Outdoor - Sintetis" class="flex-1 py-2.5 rounded-lg border border-stone-300 text-gray-800 font-medium text-sm">Edit</button>
          <button onclick="hapusLapangan(this)" class="flex-1 py-2.5 rounded-lg bg-red-100 text-red-500 font-medium text-sm">Hapus</button>
        </div>
      </div>
    </div>

  </div>

</div>

<!-- Modal Tambah / Edit Lapangan -->
<div id="lapanganModal" onclick="if(event.target===this) closeLapangan()" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
  <div class="bg-[#F4F1EA] rounded-2xl shadow-xl w-full max-w-lg p-8">
    <h3 id="lapanganTitle" class="font-serif text-2xl font-bold text-gray-900">Tambah lapangan</h3>
    <p id="lapanganSubtitle" class="text-gray-500 text-sm mt-1">Lengkapi data lapangan baru.</p>
    <hr class="border-stone-300 my-5">

    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700 mb-1">Nama lapangan</label>
      <input id="lapNama" type="text" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
        <select id="lapKategori" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
          <option>Futsal</option>
          <option>Basket</option>
          <option>Badminton</option>
          <option>Lainnya</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi / permukaan</label>
        <input id="lapLokasi" type="text" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Jam buka</label>
        <div class="relative">
          <input id="lapBuka" type="text" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
          <svg class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Jam tutup</label>
        <div class="relative">
          <input id="lapTutup" type="text" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
          <svg class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
        </div>
      </div>
    </div>

    <div class="flex gap-4">
      <button onclick="closeLapangan()" class="px-6 py-2.5 rounded-lg border border-stone-300 text-gray-500 font-medium">Batal</button>
      <button onclick="closeLapangan()" id="lapanganSaveBtn" class="px-6 py-2.5 rounded-lg bg-brand text-white font-semibold">Tambah Lapangan</button>
    </div>
  </div>
</div>

<script>
  // Modal Tambah / Edit Lapangan
  function openLapangan(btn) {
    if (btn) {
      document.getElementById('lapanganTitle').textContent = 'Edit lapangan';
      document.getElementById('lapanganSubtitle').textContent = 'Mengubah data ' + btn.dataset.nama + '.';
      document.getElementById('lapNama').value = btn.dataset.nama;
      document.getElementById('lapKategori').value = btn.dataset.kategori;
      document.getElementById('lapLokasi').value = btn.dataset.lokasi;
      document.getElementById('lapBuka').value = btn.dataset.buka;
      document.getElementById('lapTutup').value = btn.dataset.tutup;
      document.getElementById('lapanganSaveBtn').textContent = 'Simpan Perubahan';
    } else {
      document.getElementById('lapanganTitle').textContent = 'Tambah lapangan';
      document.getElementById('lapanganSubtitle').textContent = 'Lengkapi data lapangan baru.';
      document.getElementById('lapNama').value = '';
      document.getElementById('lapKategori').value = 'Futsal';
      document.getElementById('lapLokasi').value = '';
      document.getElementById('lapBuka').value = '';
      document.getElementById('lapTutup').value = '';
      document.getElementById('lapanganSaveBtn').textContent = 'Tambah Lapangan';
    }
    document.getElementById('lapanganModal').classList.remove('hidden');
  }

  function closeLapangan() {
    document.getElementById('lapanganModal').classList.add('hidden');
  }

  function hapusLapangan(btn) {
    if (confirm('Hapus lapangan ini?')) {
      btn.closest('[data-id]').remove();
      document.getElementById('lapanganCount').textContent = document.getElementById('lapanganGrid').children.length;
    }
  }
</script>

</body>
</html>