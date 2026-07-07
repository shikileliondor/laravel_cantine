<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class AttendanceService
{
    public function today(): Collection
    {
        return $this->forDate(now());
    }

    public function forDate(Carbon|string $date): Collection
    {
        return Attendance::query()
            ->with(['student', 'user'])
            ->whereDate('date', Carbon::parse($date))
            ->orderBy('heure_pointage')
            ->get();
    }

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Attendance::query()
            ->with(['student', 'user'])
            ->latest('date')
            ->paginate($perPage);
    }

    public function record(array $data, ?User $user = null): Attendance
    {
        if ($user !== null) {
            $data['user_id'] = $user->id;
        }

        $data['date'] ??= now()->toDateString();
        $data['heure_pointage'] ??= now()->format('H:i:s');
        $data['type_repas'] ??= 'dejeuner';

        return Attendance::updateOrCreate(
            [
                'student_id' => $data['student_id'],
                'date' => $data['date'],
                'type_repas' => $data['type_repas'],
            ],
            $data
        )->load(['student', 'user']);
    }

    public function mealsServedToday(): int
    {
        return Attendance::query()
            ->whereDate('date', today())
            ->where('present', true)
            ->count();
    }
}
