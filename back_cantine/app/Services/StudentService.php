<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StudentService
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Student::query()
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Student
    {
        return Student::create($data);
    }

    public function update(Student $student, array $data): Student
    {
        $student->update($data);

        return $student->refresh();
    }

    public function delete(Student $student): void
    {
        $student->delete();
    }
}
