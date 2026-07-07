<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class StudentController extends Controller
{
    public function __construct(private readonly StudentService $students)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 15), 1), 100);

        return StudentResource::collection($this->students->list($perPage));
    }

    public function store(StoreStudentRequest $request): StudentResource
    {
        return new StudentResource($this->students->create($request->validated()));
    }

    public function show(Student $student): StudentResource
    {
        return new StudentResource($student);
    }

    public function update(UpdateStudentRequest $request, Student $student): StudentResource
    {
        return new StudentResource($this->students->update($student, $request->validated()));
    }

    public function destroy(Student $student): JsonResponse
    {
        $this->students->delete($student);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
