<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class PaymentService
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Payment::query()
            ->with(['student', 'user'])
            ->latest('date_paiement')
            ->paginate($perPage);
    }

    public function create(array $data, ?User $user = null): Payment
    {
        if ($user !== null) {
            $data['user_id'] = $user->id;
        }

        return Payment::create($data)->load(['student', 'user']);
    }

    public function forStudent(Student $student, int $perPage = 15): LengthAwarePaginator
    {
        return $student->payments()
            ->with('user')
            ->latest('date_paiement')
            ->paginate($perPage);
    }

    public function totalCollected(?Carbon $from = null, ?Carbon $to = null): string
    {
        $query = Payment::query();

        if ($from !== null) {
            $query->whereDate('date_paiement', '>=', $from);
        }

        if ($to !== null) {
            $query->whereDate('date_paiement', '<=', $to);
        }

        return (string) $query->sum('montant');
    }
}
