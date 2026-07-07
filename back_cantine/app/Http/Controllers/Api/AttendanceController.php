<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $attendances)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 15), 1), 100);

        return AttendanceResource::collection($this->attendances->list($perPage));
    }

    public function today(): AnonymousResourceCollection
    {
        return AttendanceResource::collection($this->attendances->today());
    }

    public function store(StoreAttendanceRequest $request): AttendanceResource
    {
        return new AttendanceResource(
            $this->attendances->record($request->validated(), $request->user())
        );
    }

    public function byDate(string $date): AnonymousResourceCollection
    {
        return AttendanceResource::collection($this->attendances->forDate($date));
    }
}
