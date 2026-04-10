# VON.CHESS Backend API

The central orchestration and authentication layer for the VON.CHESS platform, built with Laravel 12.

## Core Responsibilities

- **Authentication**: Secure user management via Laravel Sanctum.
- **Data Persistence**: Manage user profiles, game history, and tournament data in PostgreSQL.
- **Matchmaking**: Orchestrate game creation and player pairing.
- **Microservice Bridge**: Acts as the primary controller for the Node.js game engine.

## Modular Controller Architecture

The project has been refactored to move away from fat controllers into a service-oriented structure:

### 1. Matchmaking & Seeks
- **[`MatchmakingController`](./app/Http/Controllers/Api/MatchmakingController.php)**: Isolated logic for handling player seeks and joining queues.
- **[`SeeksService`](./app/Services/SeeksService.php)**: Underlying logic for queue management.

### 2. Game Management
- **[`GameController`](./app/Http/Controllers/Api/GameController.php)**: Handles active game actions (resigning, draws, etc.).
- **[`ChessMicroservice`](./app/Services/ChessMicroservice.php)**: A dedicated service for all outgoing communication to the Node.js authoritative engine. Includes retry logic for cold starts.

### 3. Internal Authoritative APIs
- **`/api/internal/game/complete`**: Secured endpoint used by the Node.js microservice to report authoritative game results.
- **`/api/internal/game/create`**: Used for rematch orchestration.

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
