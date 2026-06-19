# VON.CHESS Backend API

The central orchestration and authentication layer for the VON.CHESS platform, built with Laravel 12.

## Core Responsibilities

- **Authentication**: Secure user management via Laravel Sanctum.
- **Data Persistence**: Manage user profiles, studies, courses, tactics progress, and tournament data in PostgreSQL.
- **Study Sync**: Keep the database in sync with collaborative analysis sessions.

## Modular Controller Architecture

The project has been refactored to focus on specific user study and training domains:

### 1. Studies & Collaboration
- **[`StudyController`](./app/Http/Controllers/Api/StudyController.php)**: Isolated logic for handling study creation, chapter manipulation, collaborator management, and analysis logs.

### 2. Tactics Training & Spaced Repetition
- **[`TacticsController`](./app/Http/Controllers/Api/TacticsController.php)**: Serves puzzles, stores solve statistics, and supports custom motifs/themes.
- **[`WoodpeckerController`](./app/Http/Controllers/Api/WoodpeckerController.php)**: Orchestrates Woodpecker training sets, custom cycle setups, and accuracy tracking.

## Tech Stack

- **Framework**: Laravel 12
- **Database**: PostgreSQL / Eloquent ORM
- **Broadcasting**: Laravel Echo / Pusher protocol
- **Proxy**: Authoritative proxying to the Node.js engine for real-time sync.

## Setup

```bash
composer install
php artisan migrate
php artisan serve
```

---
*Note: This repository does not contain the game engine. See the [chess-microservice](../chess-microservice/) for the authoritative chess physics logic.*
