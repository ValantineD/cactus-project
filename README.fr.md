# 🌵 Cactus

**Se rencontrer autour d'activités, pas de profils.**

*Lire en : [English](README.md)*

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/Symfony-7.4-000000?logo=symfony&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white)

<!-- TODO : ajouter une capture d'écran de l'application ici -->
<!-- ![Capture d'écran de Cactus](docs/screenshot.png) -->

## Table des matières

* [Documentation du projet](#documentation-du-projet)
* [Description du projet](#description-du-projet)
* [À qui s'adresse ce projet ?](#à-qui-sadresse-ce-projet-)
* [Fonctionnalités](#fonctionnalités)
* [Technologies](#technologies)
* [Dépendances du projet](#dépendances-du-projet)
* [Démarrage](#démarrage)
  * [Prérequis](#prérequis)
  * [Installation](#installation)
  * [Comptes de démonstration](#comptes-de-démonstration)
  * [Lancer les tests](#lancer-les-tests)
* [Structure du projet](#structure-du-projet)
* [Déploiement](#déploiement)
* [Évolutions prévues](#évolutions-prévues)
* [Autrice](#autrice)

## Documentation du projet

* 📘 [DOCUMENT DE SPÉCIFICATIONS FONCTIONNELLES](docs/specifications_fonctionnelles.md)
* 📗 [DOCUMENTATION TECHNIQUE](docs/documentation_technique.md)

## Description du projet

Cactus est une application web qui aide les gens à se rencontrer autour d'activités partagées — un match de badminton, une séance de cinéma, une randonnée dans les Calanques. Plutôt que de parcourir des profils sociaux, les utilisateurs créent ou rejoignent des **activités** selon leurs centres d'intérêt et leur localisation. Le lien se crée par l'expérience vécue ensemble.

Les partis pris du projet :

- **L'activité d'abord** : pas de profils sociaux détaillés, respect de la vie privée
- **Local** : une carte interactive montre ce qui se passe à proximité
- **Gratuit** : créer et rejoindre des activités ne coûte rien

Ce projet a été conçu, développé et déployé en solo comme projet fil rouge du Titre Professionnel *Développeur Web et Web Mobile* à M2i Formation (Marseille).

## À qui s'adresse ce projet ?

- **Aux utilisateurs finaux** : toute personne souhaitant organiser ou rejoindre des activités près de chez elle, sans exposer sa vie privée sur un réseau social classique.
- **Aux développeurs et recruteurs** : le dépôt sert de vitrine technique d'une application Symfony complète (authentification, upload d'images, géocodage, messagerie, tests, déploiement AWS) construite de zéro et documentée.
- **Au jury du Titre Professionnel DWWM** : la documentation fonctionnelle et technique ci-dessus couvre les compétences du référentiel (front-end, back-end, base de données, sécurité, déploiement).

## Fonctionnalités

-  **Authentification** — inscription, connexion, mots de passe hachés, protection CSRF, contrôle d'accès par rôles (utilisateur / administrateur)
-  **Activités** — création, modification, publication ou enregistrement en **brouillon**, suppression logique ; titre, description, lieu, créneau, nombre de places, thèmes et tags
-  **Upload multi-images** — renommage sécurisé (détection du vrai type MIME), génération automatique de 4 tailles responsive (300/500/900/1300 px) avec conservation des proportions, galerie ordonnée avec image principale, nettoyage disque/base à la suppression
- **Carte interactive** — activités géolocalisées avec [Leaflet](https://leafletjs.com/) + OpenStreetMap ; adresses converties en coordonnées GPS via l'API de géocodage [Nominatim](https://nominatim.org/), avec un cache de 24h respectant sa politique d'usage
-  **Recherche** — par mots-clés, lieu, thèmes et tags
-  **Participations** — rejoindre/quitter une activité, compteur de places restantes, refus d'inscription quand l'activité est complète
-  **Calendrier personnel** — toutes les activités créées et rejointes dans une vue mensuelle [FullCalendar](https://fullcalendar.io/)
-  **Messagerie** — conversations privées entre membres avec historique
-  **Espace d'administration** — gestion des thèmes (les catégories qui structurent l'application)

## Technologies

| Couche | Technologie |
|---|---|
| Back-end | PHP 8.2, Symfony 7.4, Doctrine ORM 3 (+ migrations) |
| Front-end | Twig, Bootstrap 5, Sass, Webpack Encore |
| JavaScript | Leaflet, FullCalendar, Tom Select |
| Base de données | MySQL 8 |
| API externe | Nominatim (géocodage OpenStreetMap) |
| Tests | PHPUnit, Doctrine Fixtures + Faker |
| Hébergement | AWS EC2 (Ubuntu), Nginx + PHP-FPM |

## Dépendances du projet

Principales dépendances PHP (`composer.json`) : <!-- À vérifier : ajuster selon ton composer.json réel -->

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

Principales dépendances JavaScript (`package.json`) :

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

## Démarrage

### Prérequis

- PHP ≥ 8.2 (avec l'extension `gd` activée, utilisée pour le redimensionnement d'images)
- [Composer](https://getcomposer.org/)
- Node.js ≥ 18 et npm
- MySQL 8

### Installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/ValantineD/cactus-project.git
cd cactus-project

# 2. Installer les dépendances PHP et JavaScript
composer install
npm install

# 3. Configurer l'environnement local
# Créer un fichier .env.local à la racine du projet avec vos identifiants de base de données :
# DATABASE_URL="mysql://USER:PASSWORD@127.0.0.1:3306/cactus_db?serverVersion=8.0&charset=utf8mb4"

# 4. Créer la base de données et charger le schéma
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 5. (Optionnel) Charger un jeu de données de démonstration
php bin/console doctrine:fixtures:load

# 6. Compiler les assets front-end
npm run build        # ou : npm run watch (en développement)

# 7. Lancer l'application
symfony server:start # ou : php -S localhost:8000 -t public
```

L'application est alors disponible sur `http://localhost:8000`.

### Comptes de démonstration

Après le chargement des fixtures, les comptes suivants sont disponibles : <!-- À vérifier : adapter aux identifiants réels de tes fixtures -->

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur | `admin@cactus.fr` | `password` |
| Utilisateur | `user@cactus.fr` | `password` |

### Lancer les tests

```bash
vendor/bin/phpunit
```

## Structure du projet

```
cactus-project/
├── assets/              # Sources front-end (JS, Sass) compilées par Webpack Encore
├── config/              # Configuration Symfony (services, security, packages)
├── migrations/          # Migrations Doctrine (versionnage du schéma)
├── public/              # Racine web (index.php, assets compilés, uploads)
├── src/
│   ├── Controller/      # Contrôleurs (activités, participations, messagerie, admin…)
│   ├── Entity/          # Entités Doctrine (User, Activity, Theme, Tag…)
│   ├── Form/            # Types de formulaires Symfony
│   ├── Repository/      # Requêtes personnalisées (recherche, filtres…)
│   ├── Service/         # Logique métier (upload d'images, géocodage…)
│   └── Security/        # Authentification et contrôle d'accès
├── templates/           # Vues Twig
├── tests/               # Tests PHPUnit
└── fixtures/            # Jeu de données de démonstration
```

## Déploiement

L'application est déployée sur une instance **AWS EC2** (Ubuntu) avec **Nginx + PHP-FPM + MySQL**. La procédure complète et testée (installation de la pile serveur, clé SSH GitHub, configuration Nginx, environnement de production, procédure de mise à jour) est détaillée dans la [documentation technique](docs/documentation_technique.md#9-déploiement).

## Évolutions prévues

-  Notifications (email ou in-app) lors d'une nouvelle participation ou d'un nouveau message
-  Passage en HTTPS avec certificat Let's Encrypt et nom de domaine dédié
-  Pipeline CI/CD (GitHub Actions) : tests automatiques et déploiement continu
-  Version PWA installable sur mobile

## Autrice

**Dagany Valantine** — en reconversion de l'animation 3D vers le développement web.
Projet construit de zéro entre 2025 et 2026 pendant la formation DWWM à M2i Formation, Marseille.
