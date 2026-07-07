<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'user_id' => $this->user_id,
            'montant' => $this->montant,
            'date_paiement' => $this->date_paiement?->toDateString(),
            'periode_debut' => $this->periode_debut?->toDateString(),
            'periode_fin' => $this->periode_fin?->toDateString(),
            'mode_paiement' => $this->mode_paiement,
            'reference' => $this->reference,
            'observation' => $this->observation,
            'student' => new StudentResource($this->whenLoaded('student')),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
