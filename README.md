# Laravel Cantine

Backend Laravel pour la gestion d'une cantine scolaire : eleves, paiements, presences aux repas et tableau de bord.

## Documentation

- [Guide IA et consommation de l'API](docs/GUIDE_IA_ET_API.md)
- [Audit de securite](docs/AUDIT_SECURITE.md)

## Backend

Le code de l'API se trouve dans `back_cantine`.

Demarrage local rapide :

```bash
cd back_cantine
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Base URL locale par defaut : `http://localhost:8000/api`.
