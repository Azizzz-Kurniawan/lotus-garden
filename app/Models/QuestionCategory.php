<?php

namespace App\Models;

use App\Enums\CategoryStatus; // 1. Import Enum
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; // Import Builder untuk Scope
use Carbon\Carbon;

class QuestionCategory extends Model
{
    protected $fillable = [
        'name',
        'status'
    ];

    /**
     * 2. Casting otomatis string 'pending' menjadi objek Enum
     */
    protected $casts = [
        'status' => CategoryStatus::class,
    ];

    /**
     * Relasi (sudah benar)
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'question_category_id');
    }

    /**
     * 3. Scope untuk mencari yang pending
     */
    public function scopePending(Builder $query): void
    {
        $query->where('status', CategoryStatus::PENDING);
    }

    /**
     * 4. Scope untuk mencari yang active
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', CategoryStatus::ACTIVE);
    }

    public function isDuringActivePeriod(): bool
    {
        $now = Carbon::now();

        $start = Carbon::today()
            ->setHour((int) config('questionCategory.active_hour', 10))
            ->setMinute((int) config('questionCategory.active_minute', 0));

        $end = Carbon::today()
            ->setHour((int) config('questionCategory.inactive_hour', 23))
            ->setMinute((int) config('questionCategory.inactive_minute', 0));

        return $now->isBetween($start, $end);
    }
}
