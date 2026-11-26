# API BICOLOR

API JSON du projet BICOLOR (Symfony). Adresse locale par défaut : `http://127.0.0.1:8000`.

## Authentification

Les routes protégées demandent un jeton JWT, obtenu à la connexion :

```
POST /api/login_check
Content-Type: application/json

{ "email": "utilisateur@exemple.fr", "password": "..." }
```

Réponse : `{ "token": "eyJ..." }`. Ce jeton est ensuite envoyé dans l'en-tête `Authorization: Bearer <token>`. Sans jeton valide, l'API répond `401`.

## Droits d'accès

| Niveau | Routes concernées |
|---|---|
| Public | `POST /api/login_check`, `POST /api/createUser` |
| `ROLE_USER` | `/api/game/*`, `/api/score*`, `/api/comment*` |
| `ROLE_ADMIN` | toutes les autres routes `/api/*` (gestion des utilisateurs, modification d'un commentaire) |

## Jeux

| Méthode | Route | Rôle |
|---|---|---|
| GET | `/api/game/1` | Guess the Color : une couleur à deviner et 4 codes hexadécimaux proposés |
| GET | `/api/game/2` | Classify Color : une palette de 10 nuances d'une même teinte |
| GET | `/api/game/3` | Guess the Hexa Color : un nom de couleur et 4 propositions |
| GET | `/api/game/4` | Speed Color : démarre un round (voir ci-dessous) |
| POST | `/api/game/4/answer` | Speed Color : envoie la réponse du joueur |

### Speed Color en détail

Le serveur garde la bonne réponse et mesure lui-même le temps : le navigateur n'envoie jamais de temps ni de score.

**1. Demander une question**

```
GET /api/game/4
```
```json
{
  "roundId": 537,
  "color": "#035ABA",
  "options": ["#8C2468", "#7EE353", "#BDDA9F", "#035ABA"]
}
```

**2. Envoyer la réponse**

```
POST /api/game/4/answer
Content-Type: application/json

{ "roundId": 537, "answer": "#035ABA" }
```
```json
{ "correct": true, "points": 2 }
```

Points : 1 pour une bonne réponse, +1 si la réponse arrive en moins de 2 secondes, 0 sinon.

| Code | Cause |
|---|---|
| `404` | round introuvable |
| `403` | le round appartient à un autre joueur |
| `400` | ce round a déjà reçu une réponse |

## Scores

| Méthode | Route | Rôle |
|---|---|---|
| GET | `/api/score` | Liste des scores |
| GET | `/api/score/{id}/game` | Scores d'un jeu |
| GET | `/api/score/{id}/user` | Scores d'un utilisateur |
| POST | `/api/score` | Enregistre un score. Corps : `{ "score": 8, "id_jeu": 1 }` |
| DELETE | `/api/score/{id}` | Supprime un score |

## Commentaires

Fonctionnalité prévue pour une prochaine version.

| Méthode | Route | Rôle |
|---|---|---|
| GET | `/api/comment` | Liste des commentaires |
| GET | `/api/comment/{id}/game` | Commentaires d'un jeu |
| GET | `/api/comment/{id}/user` | Commentaires d'un utilisateur |
| POST | `/api/comment/add` | Ajoute un commentaire |
| PATCH | `/api/{id}/comment` | Modifie un commentaire (administrateur) |
| DELETE | `/api/comment/{id}` | Supprime un commentaire |

## Utilisateurs

| Méthode | Route | Rôle |
|---|---|---|
| POST | `/api/createUser` | Inscription (public) |
| GET | `/api/users` | Liste des utilisateurs (administrateur) |
| GET | `/api/users/{id}` | Détail d'un utilisateur (administrateur) |
| PATCH | `/api/users/{id}` | Modifie un utilisateur (administrateur) |
| DELETE | `/api/users/{id}` | Supprime un utilisateur (administrateur) |

La liste complète des routes s'obtient avec `php bin/console debug:router`.
