<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Carbon;

class DashboardService
{
    public function summary(): array
    {
        return [
            'total_eleves' => Student::query()->count(),
            'repas_servis_aujourdhui' => $this->mealsServedToday(),
            'montant_total_encaisse' => (string) Payment::query()->sum('montant'),
            'nombre_impayes' => $this->unpaidStudentsCount(),
        ];
    }

    private function mealsServedToday(): int
    {
        return Attendance::query()
            ->whereDate('date', today())
            ->where('present', true)
            ->count();
    }

    private function unpaidStudentsCount(): int
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return Student::query()
            ->where('actif', true)
            ->whereDoesntHave('payments', function ($query) use ($startOfMonth, $endOfMonth): void {
                $query->whereBetween('date_paiement', [$startOfMonth, $endOfMonth]);
            })
            ->count();
    }
}
