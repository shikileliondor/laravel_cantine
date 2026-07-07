<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Student;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $payments)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 15), 1), 100);

        return PaymentResource::collection($this->payments->list($perPage));
    }

    public function store(StorePaymentRequest $request): PaymentResource
    {
        return new PaymentResource(
            $this->payments->create($request->validated(), $request->user())
        );
    }

    public function studentPayments(Request $request, Student $student): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 15), 1), 100);

        return PaymentResource::collection($this->payments->forStudent($student, $perPage));
    }
}
