<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourtRequest;
use App\Models\Court;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourtController extends Controller
{
    public function index(): View
    {
        $courts = Court::query()->withCount('reservations')->orderBy('id')->get();

        return view('admin.courts.index', compact('courts'));
    }

    public function create(): View
    {
        return view('admin.courts.create', ['categories' => Court::CATEGORIES]);
    }

    public function store(CourtRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['image'] = $this->storeImage($request);
        $data['is_active'] = true;

        Court::create($data);

        return redirect()->route('admin.courts.index')->with('status', 'Lapangan berhasil ditambahkan.');
    }

    public function edit(Court $court): View
    {
        return view('admin.courts.edit', [
            'court' => $court,
            'categories' => Court::CATEGORIES,
        ]);
    }

    public function update(CourtRequest $request, Court $court): RedirectResponse
    {
        $data = $request->validated();
        unset($data['image']);

        if ($request->hasFile('image')) {
            $this->deleteImage($court->image);
            $data['image'] = $this->storeImage($request);
        }

        $court->update($data);

        return redirect()->route('admin.courts.index')->with('status', 'Data lapangan berhasil diperbarui.');
    }

    public function toggle(Court $court): RedirectResponse
    {
        $court->update(['is_active' => ! $court->is_active]);

        return back()->with('status', $court->name.($court->is_active ? ' diaktifkan.' : ' dinonaktifkan.'));
    }

    public function destroy(Court $court): RedirectResponse
    {
        if ($court->reservations()->exists()) {
            return back()->withErrors([
                'court' => 'Lapangan sudah memiliki riwayat reservasi dan tidak bisa dihapus. Nonaktifkan saja.',
            ]);
        }

        $this->deleteImage($court->image);
        $court->delete();

        return redirect()->route('admin.courts.index')->with('status', 'Lapangan berhasil dihapus.');
    }

    protected function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'lapangan';
        $slug = $base;
        $counter = 2;

        while (Court::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }

    protected function storeImage(CourtRequest $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return 'storage/'.$request->file('image')->store('courts', 'public');
    }

    protected function deleteImage(?string $path): void
    {
        if ($path !== null && str_starts_with($path, 'storage/')) {
            Storage::disk('public')->delete(Str::after($path, 'storage/'));
        }
    }
}
