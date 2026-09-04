# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commandes essentielles

```bash
# Installation complète (dépendances, clé, migrations, assets)
composer setup

# Développement (serveur + queue + logs + Vite en parallèle)
composer dev

# Tests (parallèles, stop au premier échec)
php artisan test --parallel

# Un seul test
php vendor/bin/pest --filter NomDuTest

# Migrations
php artisan migrate
php artisan migrate:fresh --seed

# Linter PHP (Laravel Pint)
pint

# Analyse statique
phpstan

# Refactoring automatisé
rector process

# Assets front-end
npm run dev    # développement
npm run build  # production
```

## Stack technique

| Technologie | Version | Usage |
|---|---|---|
| Laravel | 11.31+ | Backend |
| Volt | 1.6+ | Composants interactifs (single-file) |
| Livewire | 3.5+ | Réactivité serveur |
| Alpine.js | 3.x | Interactions légères (toggle, hover, accordéon) |
| Tailwind CSS | 3.4+ | Styling + DaisyUI |
| Mary | 1.41+ | Composants UI réutilisables |
| Stripe PHP | 16.4+ | Paiements |
| Laravel DomPDF | 3.1+ | Génération PDF |
| Darryldecode Cart | 4.2+ | Gestion panier |
| Pest | 3.8+ | Tests (parallèles) |
| Laravel Pint | — | Linting/formatting |
| SQLite | — | Base de données (dev + tests en mémoire) |
| Vite | 6.0+ | Bundler front-end |

## Architecture

```
app/
├── Http/
│   ├── Controllers/    # Orchestration uniquement, délègue aux Services/Actions
│   └── Middleware/     # Middleware personnalisé
├── Models/             # Eloquent ORM — Repositories uniquement pour PostRepository
├── Services/           # Logique métier réutilisable, manipulée par Controllers
├── Actions/            # Une responsabilité = une action (exécution via execute())
├── Rules/              # Validations complexes (au-delà de required|email)
├── Repositories/       # Patterns de requête réutilisables (PostRepository existe)
├── Mail/               # Mailable classes pour les emails
├── Notifications/      # Notification classes
├── Traits/             # Traits partagés
├── View/Components/    # Composants Blade réutilisables (non-Volt)
├── Providers/          # Service providers
└── Filament/           # Panel admin (`/admin`)
    ├── Resources/      # CRUD complet (forms, tables, pages, relations)
    ├── Pages/          # Pages personnalisées du panel
    └── Widgets/        # Widgets tableau de bord

resources/
├── views/              # Blade statique ou Volt interactif (.blade.php)
│   └── livewire/       # Composants Volt (interactifs)
└── css/app.css         # Tailwind CSS 3 + DaisyUI

tests/
├── Unit/               # Tests unitaires (logique métier)
└── Feature/            # Tests fonctionnels (HTTP, workflows)
    └── Models/         # Tests spécifiques aux modèles
```

**Fichiers utilitaires :**
- `app/helpers.php` — Fonctions globales (price_without_vat, transL, etc.)
- `database/factories/` — Model factories pour tests
- `database/seeders/` — Seeders pour données de test/demo

Les tests utilisent SQLite en mémoire (`:memory:`) configuré dans `phpunit.xml`.

## Règles Volt / Livewire

- Tout composant interactif → **Volt** (fichier unique PHP + Blade dans `.blade.php`)
- Ne pas créer de classe Livewire séparée sauf si le composant dépasse 300 lignes
- `wire:model.defer` sur tout formulaire de plus de 3 champs ; `wire:model.live` acceptable pour 1-2 champs simples
- `->paginate(15)` + `->onEachSide(1)` sur toute collection affichée dans un tableau — sans exception
- Pour toggle menu, hover card, accordéon, tooltip → **Alpine.js**, pas Livewire
- Aucune logique métier dans les composants Volt → déporter vers Actions ou Services
- Mettre en cache les composants fréquents : `Cache::remember('component.'.$id, 3600, fn() => ...)`

## Tests

- **Tests unitaires** (`tests/Unit/`) — Logique métier isolée (Services, Rules, Actions)
- **Tests fonctionnels** (`tests/Feature/`) — Workflows HTTP, modèles, contrôleurs
- Utiliser `RefreshDatabase` pour tester avec SQLite en mémoire
- Factories pour générer les données de test via `factory()` ou Model::factory()
- Pest syntax de préférence (syntax expressive + assertions fluide)

## Règles de performance

- Requête SQL > 50 ms → `with()` pour eager loading obligatoire
- Collections > 1 000 enregistrements → `cursor()` au lieu de `get()`
- `preventLazyLoading` activé en production (désactivé en dev)
- Préférer les événements Livewire locaux plutôt que `$dispatch` global

## Filament

- Panel admin sur `/admin`, code dans `app/Filament/`
- Toute ressource générée inclut : forms, tables, relations, pages (index, create, edit, view)
- Générer systématiquement une Policy avec chaque Resource
- Ajouter les filtres `SoftDeletesFilter` et `TrashedFilter` par défaut sur les tables
- Rôles gérés via **Spatie Permission** (pas de rôles Filament natifs)
- Préférer les Resources du panel plutôt que l'accès direct aux modèles

```bash
php artisan make:filament-resource NomModel --generate
php artisan make:filament-page NomPage
php artisan make:filament-widget NomWidget
```

## Conventions Git

- Préfixe des commits : `[Claude] `
- Préfixe des branches : `claude/`
- `autoCommit` et `autoPush` désactivés — toujours confirmer avant de committer/pousser

## Fonctionnalités spéciales

- **Paiements** : Stripe intégré pour les abonnements/paiements
- **PDF** : DomPDF pour générer des factures/documents
- **Panier** : Darryldecode Cart pour gérer le panier e-commerce
- **UI** : Mary pour composants pré-construits (modals, cards, tables, etc.)
- **Images** : Intervention Image pour manipuler images (crop, resize, etc.)
- **Gravatar** : Avatars utilisateurs
- **HTML sécurisé** : Mews Purifier pour nettoyer HTML user-generated

## Conventions modèles

Les attributs `$fillable` et `$hidden` utilisent les attributs PHP 8 natifs :

```php
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable { ... }
```

## Configuration

- Variables d'env dans `.env` (copie de `.env.example` à la première fois)
- `config/` — configuration consolidée de l'app (database, cache, queue, etc.)
- `.claude/settings.json` — paramètres spécifiques Claude Code
- `phpunit.xml` — Configuration tests (SQLite `:memory:`, env de test)
