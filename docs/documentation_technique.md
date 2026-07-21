# DOCUMENTATION TECHNIQUE

| |                                                        |
|---|--------------------------------------------------------|
| **Nom du projet** | Cactus                                                 |
| **Environnement** | Production — AWS EC2 (Ubuntu), Nginx + PHP-FPM + MySQL |
| **Auteur(s)** | Valantine Dagany  — Conceptrice / Développeuse         |
| **Version** | V1.0                                                   |
| **Date** | 27/05/2026                                             |

## HISTORIQUE DES VERSIONS

| **Version** | **Date**     | **Auteur** | **Description** |
| --- |--------------|------------| --- |
| 0.1 | [24/02/2026] | Dagany V   | Création |
| 1.0 | [27/05/2026] | Dagany V   | Version consolidée en fin de développement |

---

## 1. INTRODUCTION

### 1.1 Objectif du document

Ce document décrit l'architecture technique, les composants, les technologies utilisées et les choix d'implémentation de l'application **Cactus**.

Objectif spécifique : permettre à un développeur découvrant le projet (ou au jury du Titre Professionnel DWWM) de comprendre l'architecture, d'installer l'application, de la maintenir et de la faire évoluer.

### 1.2 Références

Documents associés :

- [Spécifications fonctionnelles V1.0](specifications_fonctionnelles.md)
- [README du projet](../README.fr.md) (installation locale pas à pas)
- Dossier de projet DWWM (procédure de déploiement détaillée)
- Politique d'usage de l'API Nominatim : https://operations.osmfoundation.org/policies/nominatim/

---

## 2. ARCHITECTURE GÉNÉRALE

### 2.1 Vue d'ensemble

Cactus est une application web **monolithique MVC**, rendue côté serveur (server-side rendering), construite sur le framework Symfony. Elle s'organise en trois couches :

- **Présentation** : templates Twig + Bootstrap 5, enrichis côté client par des bibliothèques JavaScript ciblées (Leaflet pour la carte, FullCalendar pour le calendrier, Tom Select pour les champs de sélection).
- **Application / métier** : contrôleurs Symfony et services dédiés (upload et redimensionnement d'images, géocodage avec cache).
- **Données** : Doctrine ORM 3 avec MySQL 8, schéma versionné par migrations.

**Choix d'architecture assumé** : un monolithe MVC plutôt que des microservices ou une SPA + API. Pour une application de cette taille, développée et maintenue en solo, ce choix réduit la complexité (une seule base de code, un seul déploiement), tout en restant structuré grâce à la séparation en couches de Symfony.

### 2.2 Diagramme d'architecture

Description textuelle des flux :

```
[Navigateur]
   │  HTTPS/HTTP
   ▼
[Nginx] ──► [PHP-FPM / Symfony 7.4]
                 │
                 ├──► [MySQL 8]            (Doctrine ORM, requêtes paramétrées)
                 ├──► [Système de fichiers] (images uploadées + 4 tailles générées)
                 └──► [API Nominatim]       (géocodage, résultats cachés 24 h)

[Navigateur] ──► [Tuiles OpenStreetMap]     (chargées directement par Leaflet)
```

- Le navigateur communique avec Nginx, qui transmet les requêtes PHP à PHP-FPM (Symfony).
- Le back-end interagit avec MySQL via Doctrine et avec le disque pour les images.
- Le géocodage des adresses passe par l'API Nominatim, avec un cache applicatif de 24 h.
- Les tuiles de la carte sont chargées directement par le navigateur (Leaflet), sans transiter par le serveur.

---

## 3. ENVIRONNEMENT TECHNIQUE

### 3.1 Stack technologique

**Front-end :**

- Twig (templates rendus côté serveur)
- Bootstrap 5, Sass
- Webpack Encore (compilation des assets)
- Leaflet (carte), FullCalendar (calendrier), Tom Select (sélections multiples)

**Back-end :**

- PHP 8.2 (extension `gd` requise pour le redimensionnement d'images)
- Symfony 7.4
- Doctrine ORM 3 + Doctrine Migrations

**Base de données :**

- MySQL 8 (charset `utf8mb4`)

**Infrastructure :**

- AWS EC2 (Ubuntu)
- Nginx + PHP-FPM

**Outils de développement et de test :**

- Composer, npm
- PHPUnit, Doctrine Fixtures + Faker
- Git / GitHub

### 3.2 Environnements

| Environnement | Description | Configuration |
|---|---|---|
| **Développement** | Poste local (`symfony server:start` ou serveur PHP intégré) | `APP_ENV=dev`, `.env.local` avec base MySQL locale, `npm run watch`, barre de debug Symfony active |
| **Test** | Exécution des tests PHPUnit | `APP_ENV=test`, base de données dédiée aux tests <!-- À vérifier --> |
| **Production** | Instance AWS EC2 | `APP_ENV=prod`, secrets dans `.env.local` (jamais versionnés), cache compilé, assets buildés (`npm run build`) |

Les identifiants (base de données, secrets) sont fournis par **variables d'environnement** via `.env.local`, distinct par environnement et exclu du dépôt Git.

---

## 4. ARCHITECTURE APPLICATIVE

### 4.1 Structure du projet

```
cactus-project/
├── assets/              # Sources front-end (JS, Sass) compilées par Webpack Encore
├── config/              # Configuration Symfony (security.yaml, services.yaml, packages/)
├── migrations/          # Migrations Doctrine (versionnage du schéma SQL)
├── public/              # Racine web : index.php (front controller), build/, uploads/
├── src/
│   ├── Controller/      # Contrôleurs HTTP (activités, participation, messagerie, admin…)
│   ├── Entity/          # Entités Doctrine (User, Activity, Theme, Tag, Picture…)
│   ├── Form/            # FormTypes Symfony (validation + rendu des formulaires)
│   ├── Repository/      # Requêtes personnalisées (recherche multi-critères, filtres…)
│   ├── Service/         # Logique métier réutilisable (upload d'images, géocodage…)
│   └── Security/        # Authenticator, contrôle d'accès
├── templates/           # Vues Twig (layout de base + une vue par écran)
├── tests/               # Tests PHPUnit
└── fixtures/            # Jeu de données de démonstration (Faker)
```

Principe : les **contrôleurs restent minces** ; la logique réutilisable (traitement d'images, appel Nominatim et gestion du cache) est isolée dans des **services** injectés par le conteneur de dépendances de Symfony, ce qui la rend testable indépendamment du HTTP.

### 4.2 Gestion des dépendances

| Écosystème | Gestionnaire | Fichiers |
|---|---|---|
| PHP | Composer | `composer.json` / `composer.lock` |
| JavaScript | npm | `package.json` / `package-lock.json` |

Les fichiers de lock sont versionnés afin de garantir des installations reproductibles entre les environnements.

---

## 5. BASE DE DONNÉES

### 5.1 Modèle conceptuel des données

Entités principales :

- **User** — compte membre (authentification, rôles)
- **Activity** — activité proposée (cœur du modèle)
- **Theme** — catégorie principale, gérée par l'administrateur
- **Tag** — étiquette libre
- **Picture** — image d'une activité (galerie ordonnée)
- **Participation** — lien User ↔ Activity (inscription à une activité)
- **Conversation** / **Message** — messagerie privée
- **GeocodeCache** — cache des résultats Nominatim (24 h) <!-- À vérifier : nom réel de l'entité, ou cache Symfony -->

Relations principales :

- Un **User** crée plusieurs **Activity** (1‑N).
- Une **Activity** possède plusieurs **Picture** (1‑N, ordonnées, avec une image principale).
- Une **Activity** est liée à des **Theme** et des **Tag** (N‑N).
- **Participation** matérialise la relation N‑N User ↔ Activity avec ses données propres (date d'inscription).
- Une **Conversation** relie deux **User** et contient plusieurs **Message**.

### 5.2 Structure des tables

Exemple — table `activity` : <!-- À vérifier : noms et types exacts des colonnes -->

- `id` (INT, PK, auto-incrément)
- `title` (VARCHAR 255)
- `description` (TEXT)
- `address` (VARCHAR 255)
- `latitude` / `longitude` (DECIMAL, nullable tant que non géocodée)
- `start_at` (DATETIME) — date et créneau de l'activité
- `capacity` (INT) — nombre de places
- `status` (VARCHAR / ENUM) — brouillon ou publié
- `deleted_at` (DATETIME, nullable) — suppression logique
- `created_at` / `updated_at` (DATETIME)
- `user_id` (INT, FK → user.id) — créateur

Exemple — table `picture` :

- `id` (INT, PK)
- `filename` (VARCHAR 255) — nom généré, jamais le nom d'origine
- `position` (INT) — ordre dans la galerie
- `is_main` (BOOLEAN) — image principale
- `activity_id` (INT, FK → activity.id)

### 5.3 Contraintes

- Clés primaires sur `id` de chaque table.
- Contrainte **UNIQUE** sur `user.email` (RG-001).
- Clés étrangères : `activity.user_id → user.id`, `picture.activity_id → activity.id`, `participation.user_id / participation.activity_id`, `message.conversation_id`, etc.
- Contrainte **UNIQUE** composée sur `participation(user_id, activity_id)` : un membre ne participe qu'une fois à une activité (RG-008).
- Le schéma est entièrement versionné par **Doctrine Migrations** : chaque évolution est un fichier de migration rejouable (`doctrine:migrations:migrate`).

---

## 6. ROUTES ET CONTRÔLEURS

> L'application n'expose pas d'API REST publique : les pages sont rendues côté serveur (Twig). Cette section décrit donc les routes HTTP principales et leur contrôle d'accès, en remplacement de la section « API REST » du template.

### 6.1 Authentification

- Formulaire de connexion Symfony (`form_login`) avec **jeton CSRF**.
- Session gérée côté serveur ; mots de passe hachés via le `PasswordHasher` de Symfony (algorithme `auto` : bcrypt/argon2).
- Deux rôles : `ROLE_USER`, `ROLE_ADMIN` (hiérarchie : l'admin hérite des droits utilisateur).

### 6.2 Routes principales

<!-- À vérifier : noms de routes et URL exacts -->

| Méthode | URL | Description | Accès |
|---|---|---|---|
| GET | `/` | Accueil : activités récentes + carte | Public |
| GET/POST | `/inscription` | Création de compte | Public |
| GET/POST | `/connexion` | Authentification | Public |
| GET | `/activites` | Liste + recherche (mots-clés, lieu, thèmes, tags) | Public |
| GET | `/activites/{id}` | Détail d'une activité publiée | Public |
| GET/POST | `/activites/nouvelle` | Création d'activité (brouillon ou publiée) | ROLE_USER |
| GET/POST | `/activites/{id}/modifier` | Édition (créateur uniquement) | ROLE_USER + propriétaire |
| POST | `/activites/{id}/supprimer` | Suppression logique | ROLE_USER + propriétaire |
| POST | `/activites/{id}/participer` | Rejoindre l'activité | ROLE_USER |
| POST | `/activites/{id}/quitter` | Quitter l'activité | ROLE_USER |
| GET | `/calendrier` | Calendrier personnel (FullCalendar) | ROLE_USER |
| GET/POST | `/messagerie` | Conversations et messages | ROLE_USER |
| GET/POST | `/admin/themes` | CRUD des thèmes | ROLE_ADMIN |

### 6.3 Format des échanges

- Réponses **HTML** (Twig) pour l'essentiel des pages.
- Réponses **JSON** internes pour les composants JavaScript : événements du calendrier (FullCalendar) et données des marqueurs de la carte (Leaflet). <!-- À vérifier : données injectées dans la page ou endpoint JSON dédié -->

---

## 7. SÉCURITÉ

### 7.1 Gestion des accès

- Authentification obligatoire pour toute action d'écriture.
- Contrôle d'accès déclaré dans `config/packages/security.yaml` (`access_control`) et affiné dans les contrôleurs.
- Vérification de **propriété** sur les ressources : seul le créateur (ou un admin) peut modifier/supprimer son activité (RG-003).
- L'espace `/admin` est restreint à `ROLE_ADMIN`.

### 7.2 Protection des données

- **Mots de passe** : hachage moderne géré par Symfony (`auto` → bcrypt/argon2), jamais stockés ni loggés en clair.
- **CSRF** : jeton sur tous les formulaires (connexion, inscription, activités, participation, messagerie).
- **Injection SQL** : prévenue par Doctrine (requêtes préparées/paramétrées, pas de SQL concaténé).
- **XSS** : échappement automatique de Twig sur toutes les variables affichées.
- **Upload de fichiers** : détection du **vrai type MIME** (indépendamment de l'extension annoncée), formats d'image autorisés uniquement, **renommage systématique** avec un nom généré — un script PHP renommé en `.jpg` est donc rejeté et ne peut jamais être exécuté depuis `public/uploads`.
- **Secrets** : identifiants de base de données et `APP_SECRET` dans `.env.local`, exclu du dépôt Git.
- **RGPD / minimisation** : seules les données strictement nécessaires sont collectées (pseudo, email, mot de passe haché) ; aucune donnée personnelle superflue n'est affichée publiquement.

---

## 8. TESTS

### 8.1 Tests unitaires et fonctionnels

- Framework : **PHPUnit**.
- Périmètre couvert : logique métier des services (traitement d'images, géocodage/cache) et règles de gestion critiques (refus d'inscription sur activité complète, unicité de l'email…). <!-- À vérifier : périmètre réel de tes tests -->
- Exécution :

```bash
vendor/bin/phpunit
```

### 8.2 Données de test

- **Doctrine Fixtures + Faker** génèrent un jeu de données réaliste (utilisateurs, activités géolocalisées, participations, conversations) :

```bash
php bin/console doctrine:fixtures:load
```

- Ces fixtures servent à la fois aux tests, aux démonstrations et au développement local.

### 8.3 Tests manuels

- Scénarios de recette déroulés sur chaque fonctionnalité (voir les **critères d'acceptation** des spécifications fonctionnelles, §10).
- Vérification responsive (mobile / tablette / desktop) et multi-navigateurs (Chrome, Firefox, Edge).

---

## 9. DÉPLOIEMENT

### 9.1 Workflow de livraison

Le déploiement est actuellement **manuel et documenté** (pas encore de pipeline CI/CD — voir §12) :

1. Développement et tests en local, commits sur GitHub.
2. Connexion SSH à l'instance EC2.
3. `git pull` du dépôt (accès via une clé SSH dédiée au serveur, enregistrée sur GitHub).
4. `composer install --no-dev --optimize-autoloader`
5. `php bin/console doctrine:migrations:migrate --no-interaction`
6. `npm install && npm run build`
7. `php bin/console cache:clear --env=prod`

### 9.2 Hébergement

| Composant | Solution |
|---|---|
| Serveur | Instance **AWS EC2** (Ubuntu) |
| Serveur web | **Nginx**, racine pointant sur `public/`, toutes les requêtes routées vers `index.php` |
| Exécution PHP | **PHP-FPM 8.2** |
| Base de données | **MySQL 8** installé sur la même instance |
| Environnement | `APP_ENV=prod`, secrets dans `.env.local` sur le serveur |

Points de configuration Nginx notables :

- racine web limitée à `public/` (le code source, `.env.local` et `var/` ne sont jamais servis) ;
- taille maximale d'upload (`client_max_body_size`) alignée sur la configuration PHP pour l'envoi multi-images.

La procédure complète et testée (installation de la pile serveur, création de la clé SSH GitHub, vhost Nginx, mise en production) est détaillée dans le dossier de projet DWWM.

---

## 10. MONITORING ET LOGS

### 10.1 Monitoring

- Suivi de l'instance via la console **AWS EC2** (CPU, réseau, état de l'instance).
- Évolution envisagée : supervision applicative dédiée (par ex. UptimeRobot pour la disponibilité, ou Prometheus + Grafana).

### 10.2 Logs

| Source | Emplacement |
|---|---|
| Application (Monolog, canal prod) | `var/log/prod.log` |
| Nginx (accès / erreurs) | `/var/log/nginx/access.log`, `/var/log/nginx/error.log` |
| PHP-FPM | `/var/log/php8.2-fpm.log` |
| MySQL | `/var/log/mysql/error.log` |

En production, seuls les événements de niveau `error` et supérieur sont journalisés côté application ; aucune donnée sensible (mot de passe, contenu de message) n'apparaît dans les logs.

---

## 11. CONTRAINTES TECHNIQUES

- **Contrainte technique** : PHP ≥ 8.2 avec l'extension `gd` activée (redimensionnement d'images) ; MySQL 8 ; Node.js ≥ 18 pour la compilation des assets.
- **Contrainte d'API externe** : respect strict de la politique d'usage de **Nominatim** (fréquence limitée, User-Agent identifiant l'application, mise en cache 24 h des résultats).
- **Contrainte réglementaire** : minimisation des données personnelles (logique RGPD).
- **Contrainte d'infrastructure** : hébergement mono-instance (application, base de données et fichiers sur la même machine EC2) — dimensionné pour le périmètre du projet, voir évolutions.

---

## 12. ÉVOLUTIONS PRÉVUES

- **HTTPS** : nom de domaine dédié + certificat Let's Encrypt (Certbot) sur Nginx.
- **CI/CD** : pipeline GitHub Actions (lint, tests PHPUnit, déploiement automatisé sur EC2).
- **Notifications** : email ou in-app lors d'une nouvelle participation ou d'un nouveau message.
- **Scalabilité** : externalisation de la base de données (AWS RDS) et des images (AWS S3) pour séparer les responsabilités de l'instance.
- **Qualité** : augmentation de la couverture de tests (objectif : services métier couverts en priorité) et analyse statique (PHPStan).
- **PWA** : version installable sur mobile.
