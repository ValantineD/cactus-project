# DOCUMENT DE SPÉCIFICATIONS FONCTIONNELLES


| |                                                        |
|---|--------------------------------------------------------|
| **Nom du projet** | Cactus                                                 |
| **Environnement** | Production — AWS EC2 (Ubuntu), Nginx + PHP-FPM + MySQL |
| **Auteur(s)** | Valantine Dagany  — Conceptrice / Développeuse         |
| **Version** | V1.0                                                   |
| **Date** | 27/05/2026                                             |
| **Statut** | Validé |

## HISTORIQUE DES VERSIONS

| **Version** | **Date**     | **Auteur** | **Description** |
| --- |--------------|------------| --- |
| 0.1 | [24/02/2026] | Dagany V   | Création |
| 1.0 | [27/05/2026] | Dagany V   | Version consolidée en fin de développement |

---

## 1. CONTEXTE DU PROJET

### 1.1 Présentation générale

Ce document décrit les spécifications fonctionnelles de **Cactus**, une application web qui aide les gens à se rencontrer autour d'activités partagées (sport, culture, randonnée, jeux…).

Constat de départ : les applications de rencontre et les réseaux sociaux classiques reposent sur la mise en avant de **profils personnels** (photos, biographies, historique), ce qui :

- expose la vie privée des utilisateurs ;
- favorise le jugement sur l'apparence plutôt que sur les affinités réelles ;
- rend difficile la rencontre spontanée autour d'un centre d'intérêt local.

Cactus inverse la logique : l'utilisateur ne parcourt pas des profils, il parcourt des **activités** géolocalisées, qu'il peut rejoindre ou créer. Le lien social se construit par l'expérience vécue ensemble.

### 1.2 Objectifs du projet

Le logiciel doit permettre :

- la **création et la publication d'activités** par les membres (titre, description, lieu, créneau, nombre de places, thèmes, tags, photos) ;
- la **découverte d'activités** à proximité via une carte interactive et un moteur de recherche ;
- la **participation** à une activité en un clic, dans la limite des places disponibles ;
- la **communication privée** entre membres via une messagerie interne ;
- le **suivi personnel** des activités créées et rejointes via un calendrier.

Objectifs spécifiques mesurables :

- **Objectif principal** : permettre à un visiteur de trouver et rejoindre une activité près de chez lui en moins de 3 minutes (inscription comprise).
- **Objectif secondaire** : garantir qu'aucune donnée personnelle autre que le pseudo n'est visible publiquement (respect de la vie privée « by design »).
- **Amélioration attendue** : centraliser dans un outil unique la découverte, l'inscription et l'organisation d'activités locales.

Indicateurs spécifiques :

- **KPI-1** : 100 % des activités publiées sont géolocalisées et visibles sur la carte.
- **KPI-2** : 0 inscription possible sur une activité complète (contrôle systématique côté serveur).

---

## 2. PÉRIMÈTRE

### 2.1 Inclus dans le projet

Fonctionnalités incluses :

1. Inscription, connexion et gestion des rôles (utilisateur / administrateur)
2. Création, modification, publication, brouillon et suppression logique d'activités
3. Upload multi-images sécurisé avec génération de tailles responsive
4. Carte interactive des activités géolocalisées (Leaflet + OpenStreetMap)
5. Géocodage des adresses via l'API Nominatim, avec cache de 24 h
6. Recherche par mots-clés, lieu, thèmes et tags
7. Participation / désinscription à une activité avec gestion des places
8. Calendrier personnel des activités (vue mensuelle)
9. Messagerie privée entre membres
10. Espace d'administration : gestion des thèmes

### 2.2 Exclus du projet

Ne sont pas inclus dans cette version :

- Application mobile native (l'application est responsive, utilisable sur mobile via le navigateur)
- Paiement en ligne (toutes les fonctionnalités sont gratuites)
- Notifications push ou email automatiques
- Modération automatique des contenus
- Système d'avis / notation des activités ou des membres

Évolutions futures envisagées :

- Notifications lors d'une nouvelle participation ou d'un nouveau message
- Suggestions d'activités personnalisées selon les centres d'intérêt

---

## 3. ACTEURS

| Acteur | Description | Droits |
|---|---|---|
| **Visiteur** | Internaute non connecté | Consulte les activités publiées, la carte et la recherche ; peut s'inscrire |
| **Utilisateur** | Membre inscrit et connecté | Crée, modifie et supprime **ses** activités ; participe aux activités des autres ; utilise la messagerie et le calendrier |
| **Administrateur** | Gère le référentiel de l'application | Tous les droits utilisateur + gestion des thèmes (création, modification, suppression) |

---

## 4. DESCRIPTION FONCTIONNELLE DÉTAILLÉE

### UC-01 – Inscription d'un membre

**Acteur principal :** Visiteur

**Description :** permet à un visiteur de créer un compte pour devenir membre.

**Préconditions :** le visiteur n'est pas connecté.

**Postconditions :** un nouvel utilisateur est enregistré en base avec le rôle `ROLE_USER` et un mot de passe haché ; il est connecté. <!-- À vérifier : connexion automatique ou redirection vers la page de connexion -->

**Scénario nominal :**

1. Le visiteur clique sur « Inscription ».
2. Il renseigne les champs obligatoires (pseudo, email, mot de passe).
3. Il soumet le formulaire (protégé par un jeton CSRF).
4. Le système valide les données (format d'email, longueur du mot de passe, unicité de l'email).
5. Le système hache le mot de passe et enregistre l'utilisateur.
6. L'utilisateur est redirigé vers l'application, connecté.

**Scénarios alternatifs :**

- **A1 – Email déjà utilisé** : le système affiche un message d'erreur et bloque l'enregistrement (RG-001).
- **A2 – Données invalides** : le formulaire est réaffiché avec les messages d'erreur champ par champ ; aucune donnée n'est enregistrée.

### UC-02 – Création d'une activité

**Acteur principal :** Utilisateur

**Description :** permet à un membre de créer une activité, en brouillon ou publiée.

**Préconditions :** l'utilisateur est authentifié.

**Postconditions :** l'activité est enregistrée en base, rattachée à son créateur ; si elle est publiée et géolocalisée, elle apparaît sur la carte et dans la recherche.

**Scénario nominal :**

1. L'utilisateur clique sur « Créer une activité ».
2. Il renseigne : titre, description, lieu (adresse), date et créneau, nombre de places, thème(s), tags, et téléverse une ou plusieurs images.
3. Il choisit « Publier » (ou « Enregistrer en brouillon »).
4. Le système valide les données et vérifie les images (vrai type MIME, taille).
5. Le système renomme les images de façon sécurisée et génère 4 tailles responsive (300/500/900/1300 px).
6. Le système convertit l'adresse en coordonnées GPS via l'API Nominatim (ou via le cache si l'adresse a déjà été géocodée depuis moins de 24 h).
7. L'activité est enregistrée et l'utilisateur est redirigé vers sa page de détail.

**Scénarios alternatifs :**

- **A1 – Enregistrement en brouillon** : l'activité est enregistrée mais n'apparaît ni sur la carte, ni dans la recherche ; seul son créateur peut la voir et la modifier (RG-004).
- **A2 – Fichier image invalide** : si le fichier n'est pas une image réelle (vérification du type MIME, pas seulement de l'extension), le système le refuse avec un message d'erreur (RG-006).
- **A3 – Adresse introuvable** : si le géocodage échoue, le système en informe l'utilisateur. <!-- À vérifier : l'activité est-elle enregistrée sans coordonnées, ou la publication est-elle bloquée ? -->

### UC-03 – Participation à une activité

**Acteur principal :** Utilisateur

**Description :** permet à un membre de rejoindre une activité publiée.

**Préconditions :** l'utilisateur est authentifié ; l'activité est publiée et n'est pas complète.

**Postconditions :** une participation est enregistrée ; le compteur de places restantes est décrémenté.

**Scénario nominal :**

1. L'utilisateur consulte la page de détail d'une activité.
2. Il clique sur « Participer ».
3. Le système vérifie qu'il reste des places et que l'utilisateur ne participe pas déjà.
4. La participation est enregistrée ; le compteur de places restantes est mis à jour.

**Scénarios alternatifs :**

- **A1 – Activité complète** : le bouton est désactivé et toute tentative côté serveur est refusée avec un message explicite (RG-002).
- **A2 – Désinscription** : un participant peut quitter l'activité ; sa place est libérée immédiatement.
- **A3 – Créateur de l'activité** : le créateur ne peut pas « participer » à sa propre activité via ce mécanisme. <!-- À vérifier -->

### Autres cas d'usage (synthèse)

| ID | Cas d'usage | Acteur | Résumé |
|---|---|---|---|
| UC-04 | Connexion / déconnexion | Visiteur / Utilisateur | Authentification par email + mot de passe, session sécurisée |
| UC-05 | Modification / suppression d'une activité | Utilisateur (créateur) | Édition des champs et images ; suppression **logique** (RG-005) |
| UC-06 | Recherche d'activités | Visiteur / Utilisateur | Filtres par mots-clés, lieu, thèmes et tags |
| UC-07 | Consultation de la carte | Visiteur / Utilisateur | Carte Leaflet affichant les activités publiées géolocalisées |
| UC-08 | Calendrier personnel | Utilisateur | Vue mensuelle FullCalendar des activités créées et rejointes |
| UC-09 | Messagerie privée | Utilisateur | Conversations privées entre membres, avec historique |
| UC-10 | Gestion des thèmes | Administrateur | Création, modification et suppression des thèmes |

### UC-02 – Règle associée

Une activité ne peut être publiée que si ses champs obligatoires sont valides ; une fois complète, elle refuse toute nouvelle participation.

---

## 5. RÈGLES DE GESTION

- **RG-001** : une adresse email ne peut être associée qu'à un seul compte.
- **RG-002** : une activité complète (places restantes = 0) refuse toute nouvelle participation ; le contrôle est effectué **côté serveur**, pas uniquement dans l'interface.
- **RG-003** : seul le créateur d'une activité (ou un administrateur) peut la modifier ou la supprimer.
- **RG-004** : une activité en **brouillon** n'est visible que par son créateur ; elle n'apparaît ni sur la carte, ni dans la recherche, ni dans les listes publiques.
- **RG-005** : la suppression d'une activité est **logique** (soft delete) : l'activité est marquée supprimée et disparaît de l'application, mais la ligne est conservée en base.
- **RG-006** : tout fichier téléversé est contrôlé par son **vrai type MIME** (et non par son extension) ; seuls les formats d'image autorisés sont acceptés ; le fichier est renommé avec un nom généré (jamais le nom d'origine).
- **RG-007** : les appels à l'API Nominatim respectent sa politique d'usage : résultat mis en cache pendant **24 h**, une même adresse n'est pas géocodée deux fois dans cet intervalle.
- **RG-008** : un utilisateur ne peut pas participer deux fois à la même activité.
- **RG-009** : à la suppression d'une image (ou d'une activité), les fichiers correspondants sont supprimés du disque **et** les enregistrements de la base sont nettoyés (pas de fichiers orphelins).

---

## 6. DESCRIPTION DES ÉCRANS

### ÉCRAN-01 – Page de connexion

**Objectif :** permettre à l'utilisateur de s'authentifier.

| Élément | Type | Obligatoire | Règle |
|---|---|---|---|
| Email | Texte | Oui | Format email valide |
| Mot de passe | Mot de passe | Oui | — |
| Bouton « Connexion » | Action | — | Vérifie les identifiants, jeton CSRF |

Messages d'erreur : « Identifiants invalides » (message volontairement générique pour ne pas révéler si l'email existe).

### ÉCRAN-02 – Formulaire d'activité (création / édition)

**Objectif :** créer ou modifier une activité.

| Élément | Type | Obligatoire | Règle |
|---|---|---|---|
| Titre | Texte | Oui | Longueur maximale contrôlée |
| Description | Texte long | Oui | — |
| Adresse / lieu | Texte | Oui | Géocodée via Nominatim |
| Date et créneau | Date / heure | Oui | Date future <!-- À vérifier --> |
| Nombre de places | Nombre | Oui | Entier positif |
| Thèmes | Sélection multiple (Tom Select) | Oui | Thèmes gérés par l'admin |
| Tags | Sélection / saisie multiple (Tom Select) | Non | — |
| Images | Fichiers multiples | Non | Types MIME image uniquement, RG-006 |
| Boutons « Publier » / « Brouillon » | Action | — | Détermine le statut de l'activité |

### ÉCRAN-03 – Carte interactive

**Objectif :** visualiser les activités publiées à proximité.

Composants : carte Leaflet (fonds OpenStreetMap), marqueurs sur chaque activité géolocalisée, popup avec titre et lien vers la page de détail.

### ÉCRAN-04 – Détail d'une activité

**Objectif :** consulter une activité et y participer.

Composants : galerie d'images (image principale + galerie ordonnée), titre, description, lieu (avec mini-carte), date et créneau, compteur de places restantes, liste des thèmes et tags, bouton « Participer » / « Quitter », lien vers la messagerie avec l'organisateur. <!-- À vérifier : présence du lien messagerie sur cette page -->

### ÉCRAN-05 – Calendrier personnel

**Objectif :** visualiser dans une vue mensuelle FullCalendar toutes les activités créées et rejointes ; chaque événement renvoie vers la page de détail.

### ÉCRAN-06 – Messagerie

**Objectif :** échanger en privé avec d'autres membres.

Composants : liste des conversations, fil de messages avec historique, champ de saisie et bouton d'envoi.

### ÉCRAN-07 – Administration des thèmes

**Objectif :** permettre à l'administrateur de gérer les catégories qui structurent l'application (liste, ajout, édition, suppression). Accès restreint au rôle administrateur.

---

## 7. EXIGENCES NON FONCTIONNELLES

### 7.1 Performance

- Temps de réponse des pages inférieur à 2 secondes en conditions normales.
- Les images sont servies dans **4 tailles responsive** (300/500/900/1300 px) pour limiter le poids des pages, notamment sur mobile.
- Le cache de géocodage (24 h) évite les appels réseau redondants vers Nominatim.

### 7.2 Sécurité

- Authentification obligatoire pour toute action d'écriture (créer, participer, messagerie).
- Mots de passe hachés (hachage moderne géré par Symfony — bcrypt/argon2).
- Protection **CSRF** sur tous les formulaires.
- Protection contre l'**injection SQL** via l'ORM Doctrine (requêtes paramétrées).
- Protection **XSS** via l'échappement automatique de Twig.
- Contrôle d'accès par rôles (`ROLE_USER`, `ROLE_ADMIN`) et vérification de propriété sur les ressources (RG-003).
- Upload sécurisé : vérification du vrai type MIME et renommage des fichiers (RG-006).

### 7.3 Compatibilité

- Navigateurs modernes : Chrome, Firefox, Edge, Safari.
- Interface **responsive** (Bootstrap 5) : utilisable sur mobile, tablette et desktop.

### 7.4 Conformité

- Minimisation des données collectées (pseudo, email, mot de passe) dans une logique de conformité **RGPD** : aucune donnée personnelle superflue n'est demandée ni affichée publiquement.
- Respect de la politique d'usage de l'API Nominatim (cache, identification des requêtes).

---

## 8. DONNÉES MÉTIER

| Entité | Description | Champs principaux <!-- À vérifier : noms exacts --> |
|---|---|---|
| Utilisateur | Compte membre | id, pseudo, email, mot de passe (haché), rôles, date d'inscription |
| Activité | Événement proposé par un membre | id, titre, description, adresse, latitude, longitude, date/créneau, nombre de places, statut (brouillon/publié), supprimé (soft delete), créateur |
| Thème | Catégorie principale (gérée par l'admin) | id, nom |
| Tag | Étiquette libre de description | id, nom |
| Image | Photo attachée à une activité | id, nom de fichier, position dans la galerie, image principale (booléen), activité |
| Participation | Lien membre ↔ activité rejointe | id, utilisateur, activité, date d'inscription |
| Conversation | Fil de discussion privé entre deux membres | id, participants, date de création |
| Message | Message d'une conversation | id, conversation, auteur, contenu, date d'envoi |
| Cache de géocodage | Résultat Nominatim mis en cache 24 h | adresse, latitude, longitude, date d'expiration |

---

## 9. INTERFACES ET INTÉGRATIONS

| Système externe | Type d'échange | Format | Remarques |
|---|---|---|---|
| **Nominatim** (OpenStreetMap) | API REST HTTPS (géocodage d'adresses) | JSON | Cache de 24 h, respect de la politique d'usage (fréquence limitée, User-Agent identifiant l'application) |
| **Tuiles OpenStreetMap** (via Leaflet) | Chargement de tuiles cartographiques côté client | Images | Affichage de la carte interactive |

---

## 10. CRITÈRES D'ACCEPTATION

- Un visiteur peut consulter la carte et la recherche **sans compte** ; toute action d'écriture exige une authentification.
- Une inscription avec un email déjà utilisé est refusée avec un message d'erreur.
- Une activité publiée avec une adresse valide apparaît sur la carte avec un marqueur au bon emplacement.
- Une activité en brouillon n'est visible que par son créateur.
- Un clic sur « Participer » sur une activité complète est refusé côté serveur.
- Le téléversement d'un fichier non-image renommé en `.jpg` est refusé.
- La suppression d'une activité fait disparaître ses images du disque et ses enregistrements associés de la base.
- Les activités créées et rejointes apparaissent dans le calendrier personnel du membre.
- Un message envoyé apparaît dans la conversation des deux membres, avec conservation de l'historique.

---

## 11. CONTRAINTES

- **Contrainte technique** : application développée avec PHP 8.2 / Symfony 7.4 et MySQL 8 ; extension PHP `gd` requise pour le redimensionnement des images.
- **Contrainte réglementaire** : minimisation des données personnelles (logique RGPD) ; respect de la politique d'usage de Nominatim.
- **Contrainte budgétaire** : budget nul hors hébergement — uniquement des technologies et services gratuits ou open source.
- **Contrainte de délai** : projet réalisé en solo pendant la formation DWWM (2025–2026), livrable pour la session d'examen du Titre Professionnel.
- **Contrainte organisationnelle** : projet mené seul, couvrant conception, développement, tests et déploiement.

---

## 12. ANNEXES

- [README du projet](../README.fr.md)
- [Documentation technique](documentation_technique.md)