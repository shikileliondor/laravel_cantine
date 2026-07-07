# Guide IA et consommation de l'API Cantine

Ce document explique le projet a une IA ou a un developpeur qui doit consommer l'API.

## Resume du projet

`laravel_cantine` est un backend Laravel pour la gestion d'une cantine scolaire. Le code principal se trouve dans `back_cantine`.

L'API permet de :

- authentifier un utilisateur ;
- recuperer l'utilisateur connecte ;
- gerer les eleves ;
- enregistrer les paiements ;
- enregistrer les presences aux repas ;
- consulter des indicateurs de tableau de bord.

## Architecture utile pour une IA

- `back_cantine/routes/api.php` : catalogue des routes API.
- `back_cantine/app/Http/Controllers/Api` : controleurs HTTP.
- `back_cantine/app/Http/Requests` : validation des payloads entrants.
- `back_cantine/app/Http/Resources` : format des reponses JSON.
- `back_cantine/app/Services` : logique metier.
- `back_cantine/app/Models` : modeles Eloquent et relations.
- `back_cantine/database/migrations` : schema de base de donnees.

## Authentification

L'API utilise Laravel Sanctum avec des Bearer tokens.

### 1. Recuperer un token

Envoyer les identifiants a la route de connexion :

```bash
curl -X POST "http://localhost:8000/api/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password",
    "device_name": "client-api"
  }'
```

Reponse attendue :

```json
{
  "message": "Connexion reussie.",
  "token": "1|exemple_de_token_sanctum",
  "user": {
    "id": 1,
    "name": "Test User",
    "email": "test@example.com"
  }
}
```

Le champ `token` doit ensuite etre envoye dans l'en-tete HTTP `Authorization`.

> Important : les identifiants ci-dessus sont des identifiants de developpement issus du seeder/factory. Ils ne doivent jamais exister en production.

### 2. Appeler une route protegee

```bash
curl "http://localhost:8000/api/user" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer VOTRE_TOKEN"
```

### 3. Se deconnecter

```bash
curl -X POST "http://localhost:8000/api/logout" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer VOTRE_TOKEN"
```

Cette route supprime le token courant.

## Convention generale des reponses

Les collections paginees Laravel contiennent generalement :

- `data` : liste des objets ;
- `links` : liens de pagination ;
- `meta` : informations de pagination.

Les erreurs de validation Laravel retournent typiquement un statut HTTP `422` avec un objet `errors`.

## Routes disponibles

Toutes les routes ci-dessous, sauf `/api/login`, necessitent :

```http
Authorization: Bearer VOTRE_TOKEN
Accept: application/json
```

### Authentification

| Methode | Route | Description |
| --- | --- | --- |
| POST | `/api/login` | Connexion et creation d'un token Sanctum |
| GET | `/api/user` | Profil de l'utilisateur connecte |
| POST | `/api/logout` | Suppression du token courant |

### Tableau de bord

| Methode | Route | Description |
| --- | --- | --- |
| GET | `/api/dashboard` | Totaux : eleves, repas servis aujourd'hui, montant encaisse, impayes |

Exemple :

```bash
curl "http://localhost:8000/api/dashboard" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer VOTRE_TOKEN"
```

### Eleves

| Methode | Route | Description |
| --- | --- | --- |
| GET | `/api/students?per_page=15` | Liste paginee des eleves |
| POST | `/api/students` | Creation d'un eleve |
| GET | `/api/students/{id}` | Detail d'un eleve |
| PUT/PATCH | `/api/students/{id}` | Modification d'un eleve |
| DELETE | `/api/students/{id}` | Suppression d'un eleve |
| GET | `/api/students/{id}/payments?per_page=15` | Paiements d'un eleve |

Payload de creation :

```json
{
  "nom": "Diop",
  "prenom": "Awa",
  "classe": "CM2",
  "date_naissance": "2015-03-12",
  "nom_tuteur": "Mamadou Diop",
  "telephone_tuteur": "+221770000000",
  "adresse": "Dakar",
  "actif": true
}
```

Champs obligatoires : `nom`, `prenom`, `classe`, `nom_tuteur`, `telephone_tuteur`.

### Paiements

| Methode | Route | Description |
| --- | --- | --- |
| GET | `/api/payments?per_page=15` | Liste paginee des paiements |
| POST | `/api/payments` | Creation d'un paiement |

Payload de creation :

```json
{
  "student_id": 1,
  "montant": 15000,
  "date_paiement": "2026-07-07",
  "periode_debut": "2026-07-01",
  "periode_fin": "2026-07-31",
  "mode_paiement": "especes",
  "reference": "RECU-2026-0001",
  "observation": "Paiement mensuel"
}
```

Valeurs autorisees pour `mode_paiement` : `especes`, `cheque`, `virement`, `autre`.

### Presences aux repas

| Methode | Route | Description |
| --- | --- | --- |
| GET | `/api/attendances?per_page=15` | Liste paginee des presences |
| GET | `/api/attendances/today` | Presences du jour |
| GET | `/api/attendances/date/{YYYY-MM-DD}` | Presences d'une date precise |
| POST | `/api/attendances` | Creation ou mise a jour d'un pointage |

Payload de pointage :

```json
{
  "student_id": 1,
  "date": "2026-07-07",
  "heure_pointage": "12:30:00",
  "type_repas": "dejeuner",
  "present": true,
  "observation": "Repas servi"
}
```

Valeurs autorisees pour `type_repas` : `petit_dejeuner`, `dejeuner`, `gouter`, `diner`.

La combinaison `student_id` + `date` + `type_repas` est unique. Un nouvel appel avec la meme combinaison met a jour le pointage existant.

## Exemple de workflow client

1. `POST /api/login` pour obtenir le token.
2. Stocker le token dans un stockage securise du client.
3. Envoyer `Authorization: Bearer <token>` sur chaque appel.
4. Charger `GET /api/dashboard` pour l'accueil.
5. Charger `GET /api/students` pour selectionner un eleve.
6. Utiliser `POST /api/payments` pour enregistrer un paiement ou `POST /api/attendances` pour pointer un repas.
7. Appeler `POST /api/logout` a la deconnexion.

## Conseils pour un agent IA consommateur de l'API

- Toujours commencer par lire `routes/api.php` pour confirmer les endpoints disponibles.
- Utiliser les classes dans `app/Http/Requests` comme source de verite pour les champs requis et les formats.
- Utiliser les classes dans `app/Http/Resources` comme source de verite pour les champs retournes.
- Ne jamais inventer de route non declaree.
- Ne jamais exposer ou journaliser le token Sanctum.
- En cas de statut `401`, renouveler le token via `/api/login`.
- En cas de statut `422`, lire l'objet `errors` et corriger le payload.

## Demarrage local rapide

Depuis le dossier `back_cantine` :

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

L'API sera disponible par defaut sur `http://localhost:8000/api`.
