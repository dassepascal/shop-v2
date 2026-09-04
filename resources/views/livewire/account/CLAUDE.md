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
| Laravel | 13+ | Backend |
| Volt | latest | Composants interactifs (single-file) |
| Livewire | 3.x | Réactivité serveur |
| Alpine.js | 3.x | Interactions légères (toggle, hover, accordéon) |
| Tailwind CSS | 4.x | Styling |
| Filament | 3.x | Panel admin (`/admin`) |
| Laravel Cashier | — | Abonnements / facturation |
| Spatie Permission | — | Gestion des rôles |
| Pest | 2.x | Tests (parallèles) |
| PHPStan | — | Analyse statique |
| Rector | — | Refactoring automatisé |
| SQLite | — | Base de données (dev + tests en mémoire) |
| Vite | 8.x | Bundler front-end |

## Architecture

```
app/
├── Http/Controllers/   # Orchestration uniquement, délègue aux Services
├── Models/             # Eloquent, pas de Repository sauf cas complexe
├── Actions/            # Une responsabilité → méthode execute()
├── Services/           # Logique métier réutilisable
├── Rules/              # Validations complexes (au-delà de required|email)
└── Filament/           # Panel admin
    ├── Resources/      # CRUD complet (forms, tables, pages, relations)
    ├── Pages/          # Pages personnalisées du panel
    └── Widgets/        # Widgets tableau de bord
resources/
├── views/              # Blade statique ou Volt interactif (.blade.php)
│   └── livewire/       # Composants Volt
└── css/app.css         # Tailwind 4 (@import 'tailwindcss')
```

Les tests utilisent SQLite en mémoire (`:memory:`) configuré dans `phpunit.xml`.

## Règles Volt / Livewire

- Tout composant interactif → **Volt** (fichier unique PHP + Blade dans `.blade.php`)
- Ne pas créer de classe Livewire séparée sauf si le composant dépasse 300 lignes
- `wire:model.defer` sur tout formulaire de plus de 3 champs ; `wire:model.live` acceptable pour 1-2 champs simples
- `->paginate(15)` + `->onEachSide(1)` sur toute collection affichée dans un tableau — sans exception
- Pour toggle menu, hover card, accordéon, tooltip → **Alpine.js**, pas Livewire
- Aucune logique métier dans les composants Volt → déporter vers Actions ou Services
- Mettre en cache les composants fréquents : `Cache::remember('component.'.$id, 3600, fn() => ...)`

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

## Conventions modèles

Les attributs `$fillable` et `$hidden` utilisent les attributs PHP 8 natifs :

```php
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable { ... }
```
