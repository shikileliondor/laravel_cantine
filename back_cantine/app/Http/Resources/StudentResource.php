<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'classe' => $this->classe,
            'date_naissance' => $this->date_naissance?->toDateString(),
            'nom_tuteur' => $this->nom_tuteur,
            'telephone_tuteur' => $this->telephone_tuteur,
            'adresse' => $this->adresse,
            'actif' => $this->actif,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
