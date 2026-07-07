<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nom',
    'prenom',
    'classe',
    'date_naissance',
    'nom_tuteur',
    'telephone_tuteur',
    'adresse',
    'actif',
])]
class Student extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
            'actif' => 'boolean',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
