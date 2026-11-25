# BICOLOR — API (back-end)

Site de jeux autour des couleurs, réservé aux utilisateurs inscrits. Ce dépôt contient **l'API et le back-office** (Symfony). Le front (React) est dans un dépôt séparé : `<https://github.com/Mohamed-Elhamidi/Pro-Project-Oclock-Bicolor-Front>`.

> Projet réalisé en équipe pendant la formation O'Clock (4 apprenants). Voir la section [Ma contribution](#ma-contribution).

## Fonctionnalités

- Inscription et connexion par **jeton JWT**
- **4 jeux** sur les couleurs, avec enregistrement des scores
- Deux rôles : `USER` (jeux, scores, profil) et `ADMIN` (tous les droits)
- **Back-office** (Twig + Bootstrap) : gestion des utilisateurs, des jeux et des commentaires

## Les jeux

| Jeu | Principe | Route |
|---|---|---|
| Guess the Color | Retrouver le code hexadécimal d'une couleur affichée (4 choix) | `GET /api/game/1` |
| Classify Color | Classer des nuances d'une même teinte, du plus clair au plus foncé | `GET /api/game/2` |
| Guess the Hexa Color | Retrouver la couleur correspondant à un nom (« chartreuse »), parmi 4 choix | `GET /api/game/3` |
| **Speed Color** | Comme le jeu 1, mais chronométré : 1 point par bonne réponse, +1 si moins de 2 secondes | `GET /api/game/4` puis `POST /api/game/4/answer` |

Les scores sont enregistrés via `POST /api/score` (voir [API.md](API.md)).

## Stack technique

- PHP 7.4 (testé ; `composer.json` autorise ≥ 7.2.5), **Symfony 5.4**
- **Doctrine ORM** et migrations, base **MySQL**
- **LexikJWTAuthenticationBundle** (authentification JWT)
- Fixtures avec **Faker**
- Back-office : Twig, Bootstrap

## Speed Color : un jeu où le serveur fait autorité

Dans un jeu chronométré, le navigateur du joueur peut être modifié : on ne lui fait donc pas confiance pour le temps ni pour la bonne réponse.

1. `GET /api/game/4` génère 4 couleurs valides et distinctes, choisit la bonne, et enregistre un **`Round`** en base (joueur, bonne couleur, heure d'envoi). Seul l'identifiant du round est renvoyé au front.
2. `POST /api/game/4/answer` reçoit `roundId` et le choix du joueur. Le serveur **retrouve le round**, vérifie qu'il appartient bien au joueur et qu'il n'a pas déjà reçu de réponse, **mesure lui-même le temps écoulé** et calcule les points.

Un round ne peut être validé qu'une fois, et un joueur ne peut pas répondre à la place d'un autre (erreurs `403` et `400`).

## Installation

Prérequis : PHP, Composer, MySQL.

```bash
git clone <url-du-dépôt>
cd <dossier>
composer install

# Configuration locale (ce fichier n'est jamais envoyé sur GitHub)
cp .env .env.local
# puis éditer .env.local : DATABASE_URL, APP_SECRET, JWT_PASSPHRASE

# Clés JWT
php bin/console lexik:jwt:generate-keypair

# Base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load   # attention : vide les tables avant de les remplir

# Lancer le serveur
php -S 127.0.0.1:8000 -t public
```

### Variables d'environnement

| Variable | Rôle |
|---|---|
| `APP_ENV` | `dev` en local |
| `APP_SECRET` | Chaîne aléatoire propre à chaque installation |
| `DATABASE_URL` | Connexion MySQL, ex. `mysql://USER:PASSWORD@127.0.0.1:3306/NOM_BASE?serverVersion=8.0&charset=utf8mb4` |
| `JWT_SECRET_KEY`, `JWT_PUBLIC_KEY` | Chemins des clés JWT (générées par la commande ci-dessus) |
| `JWT_PASSPHRASE` | Mot de passe de la clé privée JWT |
| `CORS_ALLOW_ORIGIN` | Origines autorisées à appeler l'API |

Les vraies valeurs vont dans `.env.local`, jamais dans `.env`.

## Limites connues et pistes d'amélioration

- Les scores des jeux 1 à 3 sont envoyés par le navigateur : ils pourraient être falsifiés. Prochaine étape : les calculer côté serveur, comme pour Speed Color.
- Tests automatisés (PHPUnit) à ajouter, en commençant par les routes de Speed Color.
- Le temps de réponse de Speed Color est mesuré à la seconde près.

## Ma contribution

- **Speed Color (jeu 4)** : conception de bout en bout côté API (entité `Round`, migration, deux routes, vérifications de sécurité) et côté front.
- **Corrections de bugs** : codes couleur hexadécimaux invalides (zéros manquants) dans les jeux 1, 2 et 3, plage de couleurs incomplète dans les jeux 2 et 3, 10 manches jouables au lieu de 9, zone cliquable du bouton START.

## Équipe et contributions

### Développement en équipe — Projet Bicolor

Dans le cadre de ma formation, j'ai participé au développement de Bicolor au sein d'une équipe composée de développeurs frontend et backend.

L'équipe backend s'est notamment chargée de la mise en place de la logique serveur, de la gestion des données et des fonctionnalités liées au back-office et à la sécurité de la plateforme.

De mon côté, j'ai principalement contribué au développement frontend, tout en travaillant en collaboration avec l'équipe backend afin d'intégrer les différentes fonctionnalités et d'assurer la communication entre l'interface utilisateur et le serveur.

Mes contributions :

- Développement et amélioration des interfaces utilisateur.
- Intégration des différentes pages et des jeux de la plateforme.
- Participation à l'intégration des fonctionnalités communiquant avec le backend.
- Collaboration avec les développeurs backend pour connecter et tester les fonctionnalités.
- Participation aux échanges techniques, à la résolution de problèmes et à l'organisation du projet.

### Évolution personnelle — Développement de Speed Color

Après ma formation, j'ai décidé de reprendre le projet Bicolor en autonomie afin de consolider mes compétences et de continuer à faire évoluer la plateforme.

Dans cette démarche, j'ai imaginé, conçu et développé un quatrième jeu : **Speed Color**, en prenant en charge aussi bien sa partie frontend que backend.

Mes contributions :

- Conception du concept, des règles et du fonctionnement du jeu.
- Développement de l'interface utilisateur en frontend.
- Développement de la logique métier côté backend.
- Mise en place du chronomètre et du système de points.
- Intégration complète du jeu dans la plateforme Bicolor existante.
- Adaptation aux fonctionnalités et à l'architecture déjà en place.
