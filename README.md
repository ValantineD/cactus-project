# 🌵 Cactus

**Meet people through activities, not profiles.**

*Read this in: [Français](README.fr.md)*

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/Symfony-7.4-000000?logo=symfony&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white)

<!-- TODO: add a screenshot of the app here -->
<!-- ![Cactus screenshot](docs/screenshot.png) -->

## Table of contents

* [Project documentation](#project-documentation)
* [Project description](#project-description)
* [Who is this project for?](#who-is-this-project-for)
* [Features](#features)
* [Tech stack](#tech-stack)
* [Project dependencies](#project-dependencies)
* [Getting started](#getting-started)
  * [Requirements](#requirements)
  * [Installation](#installation)
  * [Demo accounts](#demo-accounts)
  * [Running the tests](#running-the-tests)
* [Project structure](#project-structure)
* [Deployment](#deployment)
* [Roadmap](#roadmap)
* [Author](#author)

## Project documentation

* 📘 [FUNCTIONAL SPECIFICATIONS DOCUMENT](docs/specifications_fonctionnelles.md) *(in French)*
* 📗 [TECHNICAL DOCUMENTATION](docs/documentation_technique.md) *(in French)*

## Project description

Cactus is a web application that helps people meet around shared activities — a badminton game, a movie night, a hike in the Calanques. Instead of browsing social profiles, users create or join **activities** based on their interests and location. Connections happen through real-life shared experiences.

Key ideas:

- **Activity-first**: no elaborate social profiles, privacy by design
- **Local**: an interactive map shows what's happening nearby
- **Free**: creating and joining activities costs nothing

This project was designed, developed and deployed solo as the capstone project ("projet fil rouge") of the *Développeur Web et Web Mobile* professional degree at M2i Formation (Marseille).

## Who is this project for?

- **End users**: anyone who wants to organise or join activities nearby, without exposing their private life on a classic social network.
- **Developers and recruiters**: the repository serves as a technical showcase of a complete Symfony application (authentication, image upload, geocoding, messaging, tests, AWS deployment) built from scratch and documented.
- **The DWWM examination board**: the functional and technical documentation above covers the skills of the official reference framework (front-end, back-end, database, security, deployment).

## Features

-  **Authentication** — registration, login, hashed passwords, CSRF protection, role-based access (user / admin)
-  **Activities** — create, edit, publish or save as **draft**, soft delete; title, description, location, time slot, number of spots, themes and tags
- **Multi-image upload** — secure renaming (real MIME type detection), automatic generation of 4 responsive sizes (300/500/900/1300 px) with aspect-ratio preservation, ordered gallery with a main picture, disk/database cleanup on deletion
- **Interactive map** — activities geolocated with [Leaflet](https://leafletjs.com/) + OpenStreetMap; addresses converted to GPS coordinates via the [Nominatim](https://nominatim.org/) geocoding API, with a 24h cache respecting its usage policy
-  **Search** — by keywords, place, themes and tags
-  **Participations** — join/leave an activity, live remaining-spots counter, full activities refuse new participants
-  **Personal calendar** — all created and joined activities in a [FullCalendar](https://fullcalendar.io/) monthly view
-  **Messaging** — private conversations between members with history
-  **Admin area** — theme management (the categories that structure the app)

## Tech stack

| Layer | Technology |
|---|---|
| Back-end | PHP 8.2, Symfony 7.4, Doctrine ORM 3 (+ migrations) |
| Front-end | Twig, Bootstrap 5, Sass, Webpack Encore |
| JavaScript | Leaflet, FullCalendar, Tom Select |
| Database | MySQL 8 |
| External API | Nominatim (OpenStreetMap geocoding) |
| Tests | PHPUnit, Doctrine Fixtures + Faker |
| Hosting | AWS EC2 (Ubuntu), Nginx + PHP-FPM |

## Project dependencies

Main PHP dependencies (`composer.json`): <!-- To check: adjust to your actual composer.json -->

```json
"require": {
    "php": ">=8.2",
    "symfony/framework-bundle": "7.4.*",
    "symfony/security-bundle": "7.4.*",
    "symfony/form": "7.4.*",
    "symfony/validator": "7.4.*",
    "symfony/twig-bundle": "7.4.*",
    "symfony/http-client": "7.4.*",
    "symfony/webpack-encore-bundle": "^2.0",
    "doctrine/orm": "^3.0",
    "doctrine/doctrine-bundle": "^2.13",
    "doctrine/doctrine-migrations-bundle": "^3.3"
},
"require-dev": {
    "phpunit/phpunit": "^11.0",
    "doctrine/doctrine-fixtures-bundle": "^4.0",
    "fakerphp/faker": "^1.24",
    "symfony/maker-bundle": "^1.60"
}
```

Main JavaScript dependencies (`package.json`):

```json
"dependencies": {
    "bootstrap": "^5.3",
    "leaflet": "^1.9",
    "fullcalendar": "^6.1",
    "tom-select": "^2.3"
},
"devDependencies": {
    "@symfony/webpack-encore": "^5.0",
    "sass": "^1.7",
    "sass-loader": "^16.0",
    "webpack": "^5.9"
}
```

## Getting started

### Requirements

- PHP ≥ 8.2 (with the `gd` extension enabled, used for image resizing)
- [Composer](https://getcomposer.org/)
- Node.js ≥ 18 and npm
- MySQL 8

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/ValantineD/cactus-project.git
cd cactus-project

# 2. Install PHP and JavaScript dependencies
composer install
npm install

# 3. Configure your local environment
# Create a .env.local file at the project root with your database credentials:
# DATABASE_URL="mysql://USER:PASSWORD@127.0.0.1:3306/cactus_db?serverVersion=8.0&charset=utf8mb4"

# 4. Create the database and load the schema
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 5. (Optional) Load a realistic demo dataset
php bin/console doctrine:fixtures:load

# 6. Build the front-end assets
npm run build        # or: npm run watch (during development)

# 7. Start the application
symfony server:start # or: php -S localhost:8000 -t public
```

The application is now available at `http://localhost:8000`.

### Demo accounts

After loading the fixtures, the following accounts are available: <!-- To check: adapt to the actual credentials in your fixtures -->

| Role | Email | Password |
|---|---|---|
| Admin | `admin@cactus.fr` | `password` |
| User | `user@cactus.fr` | `password` |

### Running the tests

```bash
vendor/bin/phpunit
```

## Project structure

```
cactus-project/
├── assets/              # Front-end sources (JS, Sass) compiled by Webpack Encore
├── config/              # Symfony configuration (services, security, packages)
├── migrations/          # Doctrine migrations (schema versioning)
├── public/              # Web root (index.php, compiled assets, uploads)
├── src/
│   ├── Controller/      # Controllers (activities, participations, messaging, admin…)
│   ├── Entity/          # Doctrine entities (User, Activity, Theme, Tag…)
│   ├── Form/            # Symfony form types
│   ├── Repository/      # Custom queries (search, filters…)
│   ├── Service/         # Business logic (image upload, geocoding…)
│   └── Security/        # Authentication and access control
├── templates/           # Twig views
├── tests/               # PHPUnit tests
└── fixtures/            # Demo dataset
```

## Deployment

The application is deployed on an **AWS EC2** instance (Ubuntu) running **Nginx + PHP-FPM + MySQL**. The full, tested deployment procedure (server stack installation, GitHub SSH key, Nginx configuration, production environment, update workflow) is detailed in the [technical documentation](docs/documentation_technique.md#9-déploiement).

## Roadmap

-  Notifications (email or in-app) on new participations or messages
- HTTPS with a Let's Encrypt certificate and a dedicated domain name
-  CI/CD pipeline (GitHub Actions): automated tests and continuous deployment
-  Installable PWA version for mobile

## Author

**Dagany Valantine** — career changer from 3D animation to web development.
Project built from scratch between 2025 and 2026 during the DWWM training at M2i Formation, Marseille.
