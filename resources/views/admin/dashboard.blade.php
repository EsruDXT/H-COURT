<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>H-Court - Dashboard Pengelola</title>
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
  <header class="flex flex-wrap items-center justify-between gap-4 mb-12">
    <div>
      <h1 class="font-serif text-2xl font-bold text-navy leading-none">H-COURT</h1>
      <p class="text-[11px] tracking-[0.2em] text-gray-500 mt-1">RESERVASI LAPANGAN SEKOLAH</p>
    </div>

    <nav class="flex items-center bg-white rounded-full p-1 border border-stone-200">
      <span class="px-6 py-2.5 rounded-full bg-brand text-white font-semibold text-sm">Dashboard</span>
      <a href="{{ route('admin.courts') }}" class="px-6 py-2.5 rounded-full text-gray-500 font-semibold text-sm hover:text-gray-700">Kelola Lapangan</a>
    </nav>

    <button class="px-7 py-3 rounded-full bg-navy text-white font-semibold text-sm">Keluar</button>
  </header>

  <!-- Judul -->
  <section class="mb-8">
    <p class="text-sm tracking-[0.15em] text-brand font-medium mb-2">Dashboard pengelola</p>
    <h2 class="font-serif text-4xl md:text-5xl font-bold text-gray-900 mb-3">Kelola semua reservasi lapangan</h2>
    <p class="text-gray-500">Tinjau, terima, atau tolak reservasi yang masuk dari satu tempat.</p>
  </section>

  <!-- Stat cards -->
  <section class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white border border-stone-200 rounded-xl p-5">
      <p class="text-sm text-gray-500 mb-2">Menunggu persetujuan</p>
      <p class="font-serif text-3xl font-bold text-brand">5</p>
    </div>
    <div class="bg-white border border-stone-200 rounded-xl p-5">
      <p class="text-sm text-gray-500 mb-2">Diterima hari ini</p>
      <p class="font-serif text-3xl font-bold text-green-700">8</p>
    </div>
    <div class="bg-white border border-stone-200 rounded-xl p-5">
      <p class="text-sm text-gray-500 mb-2">Reservasi bulan ini</p>
      <p class="font-serif text-3xl font-bold text-gray-900">42</p>
    </div>
    <div class="bg-white border border-stone-200 rounded-xl p-5">
      <p class="text-sm text-gray-500 mb-2">Lapangan aktif</p>
      <p class="font-serif text-3xl font-bold text-gray-900">3</p>
    </div>
  </section>

  <!-- Filter tabs -->
  <div class="flex flex-wrap items-center gap-3 mb-6">
    <button data-filter="semua" class="filter-btn px-5 py-2 rounded-full text-sm font-medium bg-navy text-white">Semua</button>
    <button data-filter="menunggu" class="filter-btn px-5 py-2 rounded-full text-sm font-medium bg-white border border-stone-300 text-gray-500">Menunggu</button>
    <button data-filter="diterima" class="filter-btn px-5 py-2 rounded-full text-sm font-medium bg-white border border-stone-300 text-gray-500">Diterima</button>
    <button data-filter="ditolak" class="filter-btn px-5 py-2 rounded-full text-sm font-medium bg-white border border-stone-300 text-gray-500">Ditolak</button>
  </div>

  <!-- Tabel -->
  <div class="bg-white rounded-xl border border-stone-200 overflow-hidden overflow-x-auto">
    <table class="w-full text-left min-w-[800px]">
      <thead>
        <tr class="bg-navy text-white text-sm">
          <th class="px-6 py-4 font-medium">Peminjam</th>
          <th class="px-6 py-4 font-medium">Lapangan</th>
          <th class="px-6 py-4 font-medium">Tanggal & Jam</th>
          <th class="px-6 py-4 font-medium">Keperluan</th>
          <th class="px-6 py-4 font-medium">Status</th>
          <th class="px-6 py-4 font-medium">Tindakan</th>
        </tr>
      </thead>
      <tbody id="tableBody" class="divide-y divide-stone-100">

        <tr data-status="menunggu">
          <td class="px-6 py-5">
            <p class="font-semibold text-gray-900">Axel Lucius Efendi</p>
            <p class="text-sm text-gray-400">XII TKJ 3</p>
          </td>
          <td class="px-6 py-5">Futsal</td>
          <td class="px-6 py-5">20 Agu - 15:00-16:00</td>
          <td class="px-6 py-5">Latihan</td>
          <td class="px-6 py-5"><span class="text-xs font-semibold px-3 py-1 rounded-full bg-amber-100 text-amber-700">Menunggu</span></td>
          <td class="px-6 py-5">
            <div class="flex gap-2">
              <button class="text-xs font-semibold px-3 py-1.5 rounded-full bg-green-100 text-green-700">Terima</button>
              <button class="text-xs font-semibold px-3 py-1.5 rounded-full bg-red-100 text-red-500">Tolak</button>
              <button onclick="openEdit(this)" data-name="Axel Lucius Efendi" data-kelas="XII TKJ 3" data-lapangan="Futsal" data-tanggal="20/08/2026" data-status="Menunggu" data-jam-mulai="03:00 PM" data-jam-selesai="04:00 PM" data-keperluan="Latihan Rutin" class="text-xs font-semibold px-3 py-1.5 rounded-full bg-amber-100 text-amber-700">Edit</button>
            </div>
          </td>
        </tr>

        <tr data-status="diterima">
          <td class="px-6 py-5">
            <p class="font-semibold text-gray-900">Bryan Stevent</p>
            <p class="text-sm text-gray-400">XII TKJ 2</p>
          </td>
          <td class="px-6 py-5">Basket</td>
          <td class="px-6 py-5">20 Agu - 16:00-17:00</td>
          <td class="px-6 py-5">Lomba</td>
          <td class="px-6 py-5"><span class="text-xs font-semibold px-3 py-1 rounded-full bg-green-100 text-green-700">Diterima</span></td>
          <td class="px-6 py-5">
            <div class="flex gap-2">
              <button onclick="openEdit(this)" data-name="Bryan Stevent" data-kelas="XII TKJ 2" data-lapangan="Basket" data-tanggal="20/08/2026" data-status="Diterima" data-jam-mulai="04:00 PM" data-jam-selesai="05:00 PM" data-keperluan="Lomba" class="text-xs font-semibold px-3 py-1.5 rounded-full bg-amber-100 text-amber-700">Edit</button>
            </div>
          </td>
        </tr>

        <tr data-status="ditolak">
          <td class="px-6 py-5">
            <p class="font-semibold text-gray-900">Marvin Arif Pratama</p>
            <p class="text-sm text-gray-400">XII TKJ 1</p>
          </td>
          <td class="px-6 py-5">Badminton</td>
          <td class="px-6 py-5">21 Agu - 09:00-11:00</td>
          <td class="px-6 py-5">Pemakaian umum</td>
          <td class="px-6 py-5"><span class="text-xs font-semibold px-3 py-1 rounded-full bg-red-100 text-red-500">Ditolak</span></td>
          <td class="px-6 py-5">
            <div class="flex gap-2">
              <button onclick="openEdit(this)" data-name="Marvin Arif Pratama" data-kelas="XII TKJ 1" data-lapangan="Badminton" data-tanggal="21/08/2026" data-status="Ditolak" data-jam-mulai="09:00 AM" data-jam-selesai="11:00 AM" data-keperluan="Pemakaian umum" class="text-xs font-semibold px-3 py-1.5 rounded-full bg-amber-100 text-amber-700">Edit</button>
            </div>
          </td>
        </tr>

        <tr data-status="menunggu">
          <td class="px-6 py-5">
            <p class="font-semibold text-gray-900">Harry Wang</p>
            <p class="text-sm text-gray-400">Umum</p>
          </td>
          <td class="px-6 py-5">Futsal</td>
          <td class="px-6 py-5">21 Agu - 13:00-15:00</td>
          <td class="px-6 py-5">Latihan</td>
          <td class="px-6 py-5"><span class="text-xs font-semibold px-3 py-1 rounded-full bg-amber-100 text-amber-700">Menunggu</span></td>
          <td class="px-6 py-5">
            <div class="flex gap-2">
              <button class="text-xs font-semibold px-3 py-1.5 rounded-full bg-green-100 text-green-700">Terima</button>
              <button class="text-xs font-semibold px-3 py-1.5 rounded-full bg-red-100 text-red-500">Tolak</button>
              <button onclick="openEdit(this)" data-name="Harry Wang" data-kelas="Umum" data-lapangan="Futsal" data-tanggal="21/08/2026" data-status="Menunggu" data-jam-mulai="01:00 PM" data-jam-selesai="03:00 PM" data-keperluan="Latihan" class="text-xs font-semibold px-3 py-1.5 rounded-full bg-amber-100 text-amber-700">Edit</button>
            </div>
          </td>
        </tr>

      </tbody>
    </table>
  </div>

</div>

<!-- Modal Edit -->
<div id="editModal" onclick="if(event.target===this) closeEdit()" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
  <div class="bg-[#F4F1EA] rounded-2xl shadow-xl w-full max-w-lg p-8">
    <h3 class="font-serif text-2xl font-bold text-gray-900">Edit reservasi</h3>
    <p id="editSubtitle" class="text-gray-500 text-sm mt-1">Mengubah reservasi milik.</p>
    <hr class="border-stone-300 my-5">

    <div class="grid grid-cols-2 gap-4 mb-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama peminjam</label>
        <input id="editName" type="text" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kelas / instansi</label>
        <input id="editKelas" type="text" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
      </div>
    </div>

    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700 mb-1">Lapangan</label>
      <select id="editLapangan" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
        <option>Futsal</option>
        <option>Basket</option>
        <option>Badminton</option>
      </select>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
        <div class="relative">
          <input id="editTanggal" type="text" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
          <svg class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select id="editStatus" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
          <option>Menunggu</option>
          <option>Diterima</option>
          <option>Ditolak</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Jam mulai</label>
        <div class="relative">
          <input id="editJamMulai" type="text" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
          <svg class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Jam selesai</label>
        <div class="relative">
          <input id="editJamSelesai" type="text" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
          <svg class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
        </div>
      </div>
    </div>

    <div class="mb-6">
      <label class="block text-sm font-medium text-gray-700 mb-1">Keperluan</label>
      <input id="editKeperluan" type="text" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
    </div>

    <div class="flex gap-4">
      <button onclick="closeEdit()" class="px-6 py-2.5 rounded-lg border border-stone-300 text-gray-500 font-medium">Batal</button>
      <button onclick="closeEdit()" class="px-6 py-2.5 rounded-lg bg-brand text-white font-semibold">Simpan Perubahan</button>
    </div>
  </div>
</div>

<script>
  // Filter tabs
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const filter = btn.dataset.filter;

      document.querySelectorAll('#tableBody tr').forEach(row => {
        row.style.display = (filter === 'semua' || row.dataset.status === filter) ? '' : 'none';
      });

      document.querySelectorAll('.filter-btn').forEach(b => {
        b.classList.remove('bg-navy', 'text-white');
        b.classList.add('bg-white', 'border', 'border-stone-300', 'text-gray-500');
      });
      btn.classList.remove('bg-white', 'border', 'border-stone-300', 'text-gray-500');
      btn.classList.add('bg-navy', 'text-white');
    });
  });

  // Modal edit
  function openEdit(btn) {
    document.getElementById('editName').value = btn.dataset.name;
    document.getElementById('editKelas').value = btn.dataset.kelas;
    document.getElementById('editLapangan').value = btn.dataset.lapangan;
    document.getElementById('editTanggal').value = btn.dataset.tanggal;
    document.getElementById('editStatus').value = btn.dataset.status;
    document.getElementById('editJamMulai').value = btn.dataset.jamMulai;
    document.getElementById('editJamSelesai').value = btn.dataset.jamSelesai;
    document.getElementById('editKeperluan').value = btn.dataset.keperluan;
    document.getElementById('editSubtitle').textContent = 'Mengubah reservasi milik ' + btn.dataset.name + '.';
    document.getElementById('editModal').classList.remove('hidden');
  }

  function closeEdit() {
    document.getElementById('editModal').classList.add('hidden');
  }
</script>

</body>
</html>