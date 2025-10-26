<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionCategory;
use App\Enums\CategoryStatus; // 1. DIUBAH: Import Enum
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse; // 2. DIUBAH: Import RedirectResponse

// Hapus 'use App\Jobs\ActivateCategoryJob;' (sudah tidak dipakai)

class QuestionCategoryController extends Controller
{
    public function index()
    {
        $categories = QuestionCategory::all();
        // 3. DIUBAH: Asumsi path view Anda ada di dalam folder 'admin'
        return view('question_categories.index', compact('categories'));
    }

    public function create()
    {
        // 4. DIUBAH: Asumsi path view Anda ada di dalam folder 'admin'
        return view('question_categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:question_categories,name',
        ]);

        QuestionCategory::create([
            'name' => $request->name,
            // 5. DIUBAH: Gunakan Enum, bukan string
            'status' => CategoryStatus::INACTIVE,
        ]);

        // 6. DIUBAH: Asumsi nama route Anda pakai prefix 'admin.'
        return redirect()->route('question-categories.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function edit(QuestionCategory $question_category)
    {
        // 7. DIUBAH: Asumsi path view Anda ada di dalam folder 'admin'
        // Variabel $question_category sudah benar dari route model binding
        return view('question_categories.edit', compact('question_category'));
    }

    public function update(Request $request, QuestionCategory $question_category): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:question_categories,name,' . $question_category->id,
        ]);

        $question_category->update([
            'name' => $request->name,
        ]);

        // 8. DIUBAH: Asumsi nama route Anda pakai prefix 'admin.'
        return redirect()->route('question-categories.index')
            ->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(QuestionCategory $question_category): RedirectResponse
    {
        $question_category->delete();
        // 9. DIUBAH: Asumsi nama route Anda pakai prefix 'admin.'
        return redirect()->route('question-categories.index')->with('success', 'Category Deleted');
    }


    /**
     * 10. DIUBAH TOTAL:
     * Mengganti status menggunakan Enum dan logika yang benar.
     */
    public function toggle(QuestionCategory $category): RedirectResponse
    {
        $message = '';

        // Gunakan Enum untuk mengecek status
        switch ($category->status) {

            // Jika statusnya ACTIVE (tombol "Nonaktifkan" ditekan)
            case CategoryStatus::ACTIVE:
                // Cek dulu, jangan biarkan nonaktif saat jam aktif
                if ($category->isDuringActivePeriod()) {
                    return redirect()->route('question-categories.index')
                        ->with('error', 'Tidak bisa menonaktifkan kategori selama jam aktif.');
                }

                $category->status = CategoryStatus::INACTIVE;
                $message = 'Kategori berhasil dinonaktifkan.';
                break;

            // Jika statusnya PENDING (tombol "Cancel" ditekan)
            case CategoryStatus::PENDING:
                $category->status = CategoryStatus::INACTIVE;
                $message = 'Publikasi kategori berhasil dibatalkan.';
                break;

            // Jika statusnya INACTIVE (tombol "Publish" ditekan)
            case CategoryStatus::INACTIVE:
                $category->status = CategoryStatus::PENDING;
                $message = 'Kategori berhasil dijadwalkan (pending) untuk publikasi.';
                break;
        }

        // Simpan perubahan ke database
        $category->save();

        // Kembali ke halaman index dengan pesan sukses
        return redirect()->route('question-categories.index')
            ->with('success', $message);
    }
}
