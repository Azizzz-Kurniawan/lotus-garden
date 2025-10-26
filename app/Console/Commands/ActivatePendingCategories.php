<?php

namespace App\Console\Commands;

use App\Models\QuestionCategory;
use App\Enums\CategoryStatus; // Import Enum
use Illuminate\Console\Command;

class ActivatePendingCategories extends Command
{
    protected $signature = 'categories:activate-pending';
    protected $description = 'Activates all pending categories';

    public function handle(): void
    {
        $this->info('Mencari dan mengaktifkan kategori pending...');

        // Gunakan Scope "pending()" yang tadi kita buat
        $count = QuestionCategory::pending()
            ->update([
                'status' => CategoryStatus::ACTIVE
            ]);

        $this->info("Selesai. $count kategori diaktifkan.");
    }
}
