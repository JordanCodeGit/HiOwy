<?php

namespace App\Http\Controllers;

use App\Models\LearningMaterial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class BkLearnController extends Controller
{
    /**
     * Tampilkan halaman manajemen materi edukasi & pengumuman BK.
     */
    public function index(Request $request): View
    {
        $hasTable = Schema::hasTable('learning_materials');

        if ($hasTable) {
            $query = LearningMaterial::query();

            // Filter pencarian
            if ($request->filled('q')) {
                $search = $request->input('q');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            }

            // Filter kanal/target audiens
            if ($request->filled('audience') && in_array($request->input('audience'), ['siswa', 'orang_tua', 'umum'])) {
                $query->where('audience', $request->input('audience'));
            }

            // Filter status publikasi
            if ($request->filled('status')) {
                if ($request->input('status') === 'active') {
                    $query->where('is_visible', true);
                } elseif ($request->input('status') === 'hidden') {
                    $query->where('is_visible', false);
                }
            }

            // Pengurutan
            $sort = $request->input('sort', 'newest');
            match ($sort) {
                'oldest' => $query->oldest(),
                'views'  => $query->orderByDesc('views_count'),
                default  => $query->latest(),
            };

            $materials = $query->get();
            $totalViews = LearningMaterial::sum('views_count');
            $publishedCount = LearningMaterial::where('is_visible', true)->count();
            $totalCount = LearningMaterial::count();
        } else {
            // Fallback object sebelum migrasi dijalankan oleh user di terminal
            $materials = collect([]);
            $totalViews = 0;
            $publishedCount = 0;
            $totalCount = 0;
        }

        return view('bk.learn', [
            'materials'      => $materials,
            'totalViews'     => $totalViews,
            'publishedCount' => $publishedCount,
            'totalCount'     => $totalCount,
            'hasTable'       => $hasTable,
        ]);
    }

    /**
     * Simpan pengumuman / materi baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'audience'   => ['required', 'string', 'in:siswa,orang_tua,umum'],
            'content'    => ['required', 'string'],
            'is_visible' => ['nullable'],
        ], [
            'title.required'    => 'Judul pengumuman wajib diisi.',
            'title.max'         => 'Judul maksimal 255 karakter.',
            'audience.required' => 'Target penerima materi wajib dipilih.',
            'content.required'  => 'Isi pengumuman wajib diisi.',
        ]);

        if (! Schema::hasTable('learning_materials')) {
            return back()->with('error_message', 'Tabel database belum dibuat. Silakan jalankan `php artisan migrate` di terminal.');
        }

        $code = $this->generateUniqueCode($validated['audience']);

        $author = Auth::user();

        LearningMaterial::create([
            'code'        => $code,
            'title'       => $validated['title'],
            'audience'    => $validated['audience'],
            'content'     => $validated['content'],
            'is_visible'  => $request->boolean('is_visible', true),
            'author_name' => $author?->name ?? 'Sarah Jenkins, M.Pd.',
            'author_id'   => $author?->id,
            'views_count' => 0,
        ]);

        return back()->with('success_message', 'Materi edukasi & pengumuman baru berhasil diterbitkan ke portal.');
    }

    /**
     * Perbarui pengumuman / materi yang sudah ada.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'audience'   => ['required', 'string', 'in:siswa,orang_tua,umum'],
            'content'    => ['required', 'string'],
            'is_visible' => ['nullable'],
        ], [
            'title.required'    => 'Judul pengumuman wajib diisi.',
            'title.max'         => 'Judul maksimal 255 karakter.',
            'audience.required' => 'Target penerima materi wajib dipilih.',
            'content.required'  => 'Isi pengumuman wajib diisi.',
        ]);

        $material = LearningMaterial::findOrFail($id);
        $material->update([
            'title'      => $validated['title'],
            'audience'   => $validated['audience'],
            'content'    => $validated['content'],
            'is_visible' => $request->boolean('is_visible'),
        ]);

        return back()->with('success_message', 'Materi edukasi (' . $material->code . ') berhasil diperbarui.');
    }

    /**
     * Beralih status tayang (visibilitas) secara cepat.
     */
    public function toggleVisibility(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $material = LearningMaterial::findOrFail($id);
        $material->is_visible = ! $material->is_visible;
        $material->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'    => true,
                'is_visible' => $material->is_visible,
                'message'    => 'Status tayang materi ' . $material->code . ' berhasil diubah.',
            ]);
        }

        return back()->with('success_message', 'Status tayang materi ' . $material->code . ' berhasil diubah menjadi ' . ($material->is_visible ? 'Aktif' : 'Disembunyikan') . '.');
    }

    /**
     * Hapus pengumuman / materi.
     */
    public function destroy(int $id): RedirectResponse
    {
        $material = LearningMaterial::findOrFail($id);
        $code = $material->code;
        $material->delete();

        return back()->with('success_message', 'Materi edukasi ' . $code . ' berhasil dihapus dari sistem.');
    }

    /**
     * Tampilkan Portal Siswa Publik (/s/{code}/pelajari).
     */
    public function showPublic(string $code = 'smakita'): View
    {
        if (Schema::hasTable('learning_materials')) {
            // Increment views_count untuk setiap materi aktif yang tampil dan dibaca siswa di portal
            LearningMaterial::where('is_visible', true)->increment('views_count');

            $materials = LearningMaterial::where('is_visible', true)->latest()->get();
        } else {
            $materials = collect([]);
        }

        return view('public.learn', [
            'schoolCode' => $code,
            'materials'  => $materials,
        ]);
    }

    /**
     * Seed materi edukasi bawaan desain (dinonaktifkan).
     */
    private function seedDefaultMaterials(): void
    {
        // No dummy seeding
    }

    /**
     * Generate unique code untuk materi baru berdasarkan target audiens.
     */
    private function generateUniqueCode(string $audience): string
    {
        $prefix = $audience === 'umum' ? 'BK-DARURAT-' : 'BK-EDU-';

        // Ambil semua kode yang sudah ada dengan awalan prefix ini
        $existingCodes = LearningMaterial::where('code', 'like', "{$prefix}%")->pluck('code')->all();

        $maxNumber = 0;
        foreach ($existingCodes as $existing) {
            $numString = preg_replace('/\D/', '', str_replace($prefix, '', $existing));
            if (is_numeric($numString) && (int) $numString > $maxNumber) {
                $maxNumber = (int) $numString;
            }
        }

        $counter = max($maxNumber + 1, 1);

        do {
            $code = $prefix . str_pad((string) $counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        } while (in_array($code, $existingCodes, true) || LearningMaterial::where('code', $code)->exists());

        return $code;
    }

    /**
     * Fallback array objek untuk tampilan jika tabel belum dimigrasi.
     */
    private function getDefaultFallbackArray(): array
    {
        return [];
    }
}
