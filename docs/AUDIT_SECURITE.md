# Audit de securite - API Cantine Laravel

Date de l'audit : 2026-07-07.

## Portee

Cet audit couvre le backend Laravel situe dans `back_cantine`. L'application expose une API REST protegee par Laravel Sanctum pour gerer :

- les utilisateurs authentifies ;
- les eleves ;
- les paiements de cantine ;
- les pointages de presence aux repas ;
- le tableau de bord administratif.

## Synthese executif

Le projet possede une base saine : authentification Sanctum, validation centralisee via Form Requests, ressources JSON explicites, ORM Eloquent et requetes parametrees. Les principaux risques residuels concernent surtout l'exploitation en production : creation de comptes administrateurs, politique de mots de passe, limitation de debit, configuration CORS, journalisation, protection des donnees personnelles et gouvernance des tokens.

Niveau de risque global actuel : **moyen** tant que le projet reste en environnement de developpement ; **eleve** si deployee en production sans les mesures ci-dessous.

## Points positifs constates

- Toutes les routes metier sont protegees par le middleware `auth:sanctum`.
- La route de connexion est separee et valide les champs `email`, `password` et `device_name`.
- Les entrees API sont validees avec des Form Requests dediees.
- Les operations SQL passent par Eloquent, ce qui limite les injections SQL classiques.
- Les mots de passe utilisateurs sont hashes automatiquement par le cast `hashed` du modele `User`.
- Les relations sensibles utilisent des cles etrangeres avec suppression ou mise a null controlee.
- Les reponses JSON passent par des Resources, ce qui evite d'exposer directement tous les attributs Eloquent.

## Risques et recommandations prioritaires

### 1. Comptes de test et mot de passe par defaut

**Constat :** le seeder cree un utilisateur `test@example.com`. La factory utilise le mot de passe `password` pour les utilisateurs generes.

**Risque :** si les seeders ou les comptes de demonstration sont utilises en production, un attaquant peut obtenir un acces complet a l'API.

**Recommandations :**

- Ne jamais executer les seeders de demonstration en production.
- Creer un seeder production separe demandant un mot de passe fort via variable d'environnement.
- Supprimer ou desactiver les comptes de test avant tout deploiement.
- Mettre en place une rotation obligatoire du mot de passe initial.

### 2. Absence de roles et permissions metier

**Constat :** tout utilisateur authentifie peut consulter, creer, modifier ou supprimer les eleves, saisir des paiements et enregistrer les pointages.

**Risque :** un compte compromis ou un utilisateur interne non autorise peut acceder a toutes les donnees et effectuer toutes les actions.

**Recommandations :**

- Ajouter des roles, par exemple `admin`, `comptable`, `agent_cantine`, `lecture_seule`.
- Ajouter des Policies Laravel sur `Student`, `Payment` et `Attendance`.
- Restreindre la suppression d'eleves aux administrateurs.
- Restreindre les paiements aux profils autorises.

### 3. Limitation de debit insuffisamment explicite

**Constat :** aucune limitation de debit specifique n'est declaree dans `routes/api.php`, notamment pour `/login`.

**Risque :** attaques par force brute sur les identifiants ou saturation volontaire de l'API.

**Recommandations :**

- Appliquer `throttle` sur `/login`, par exemple `throttle:5,1` ou une regle personnalisee par email + adresse IP.
- Appliquer une limitation adaptee aux routes authentifiees.
- Journaliser les echecs de connexion repetes.

### 4. Gouvernance des tokens Sanctum

**Constat :** les tokens sont crees a la connexion. Le logout supprime le token courant, mais aucune duree de vie applicative n'est documentee.

**Risque :** un token vole peut rester exploitable trop longtemps selon la configuration effective.

**Recommandations :**

- Definir une expiration de tokens dans `config/sanctum.php` ou a la creation du token.
- Prevoir une route d'invalidation de tous les tokens de l'utilisateur en cas de compromission.
- Nommer les tokens par appareil avec `device_name` et afficher les sessions actives cote client.
- Ne jamais stocker le token dans un stockage non securise cote mobile ou web.

### 5. Donnees personnelles des eleves et tuteurs

**Constat :** l'API manipule des noms, prenoms, dates de naissance, telephones, adresses et informations de paiement.

**Risque :** exposition de donnees personnelles et financieres en cas de fuite, journalisation excessive ou mauvaise configuration CORS.

**Recommandations :**

- Servir l'API uniquement en HTTPS.
- Configurer CORS avec une liste stricte de domaines clients autorises.
- Eviter de logger les payloads complets contenant telephone, adresse, observation ou references de paiement.
- Definir une politique de conservation et d'export/suppression des donnees.
- Limiter les champs exposes selon les roles.

### 6. Validation metier a renforcer

**Constat :** les validations couvrent le format et l'existence des cles etrangeres, mais pas toutes les regles metier.

**Risque :** donnees incoherentes, paiements a zero, observations tres longues, pointages antidates non controles.

**Recommandations :**

- Remplacer `min:0` par `gt:0` pour les montants si les paiements nuls sont interdits.
- Ajouter des longueurs maximales aux champs `observation`.
- Definir si les dates futures ou tres anciennes sont autorisees.
- Normaliser les telephones et verifier les doublons d'eleves selon la politique metier.

### 7. Suppression d'eleves en cascade

**Constat :** la suppression d'un eleve supprime automatiquement ses paiements et presences.

**Risque :** perte definitive de donnees financieres et historiques.

**Recommandations :**

- Preferer une desactivation via `actif=false` a la suppression physique.
- Ajouter SoftDeletes pour les eleves si la suppression reste necessaire.
- Restreindre ou auditer fortement la route `DELETE /students/{id}`.

### 8. Audit trail applicatif

**Constat :** les paiements et pointages stockent `user_id`, mais il n'y a pas de journal d'audit dedie pour les modifications, suppressions et connexions.

**Risque :** difficulte a reconstituer les actions en cas d'erreur ou d'incident.

**Recommandations :**

- Ajouter un journal d'audit pour creation, modification, suppression et connexion.
- Conserver l'ancien et le nouveau contenu pour les champs critiques.
- Proteger les journaux contre la modification par les utilisateurs applicatifs.

## Checklist avant production

- [ ] `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` correct.
- [ ] Cle `APP_KEY` generee et secrete.
- [ ] HTTPS obligatoire via reverse proxy ou serveur web.
- [ ] Domaines CORS strictement listes.
- [ ] Aucun compte de test actif.
- [ ] Seeders de demonstration non executes en production.
- [ ] Politique de mots de passe et de rotation definie.
- [ ] Expiration et revocation des tokens Sanctum configurees.
- [ ] Rate limiting sur `/api/login` et routes sensibles.
- [ ] Sauvegardes chiffrees et testees.
- [ ] Logs applicatifs sans secrets ni donnees personnelles inutiles.
- [ ] Permissions fichiers Laravel durcies : `.env` non public, `storage` et `bootstrap/cache` accessibles uniquement au processus serveur.
- [ ] Supervision des erreurs et alertes de securite activees.

## Plan d'action recommande

1. **Bloquant production :** supprimer les comptes de test, configurer HTTPS, CORS, `APP_DEBUG=false`, expiration des tokens et throttle login.
2. **Court terme :** ajouter roles/policies, journal d'audit, validations de longueur et de montant.
3. **Moyen terme :** SoftDeletes ou archivage des eleves, gestion des sessions/tokens cote interface, alertes sur activites suspectes.
4. **Long terme :** revue RGPD/donnees personnelles, tests de penetration, procedure de reponse aux incidents.
