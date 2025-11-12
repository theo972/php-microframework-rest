# 🧩 PHP REST Microframework

Micro-framework REST développé from scratch en **PHP 8.2**, sans aucun framework applicatif.
Le projet implémente son propre routeur attributif et un système de validation générique construits entièrement à la main.
L’ensemble respecte les standards **PSR-4 et PSR-12**, avec une architecture claire :
**Controller → Service → Repository → DTO → Validator**
---

## 🗂 Sommaire
- [Stack technique](#stack-technique)
- [Installation](#installation)
- [Démarrage](#démarrage)
- [Authentification](#authentification)
- [Endpoints](#endpoints)
- [Structure du projet](#structure-du-projet)
- [Sécurité et prévention des injections](#sécurité-et-prévention-des-injections)
- [Code Style PSR-12](#code-style-psr-12)
- [Commandes utiles](#commandes-utiles)
- [Tests unitaires](#tests-unitaires)
- [Licence](#licence)

---

## ⚙️ Stack technique

| Composant        | Description                         |
|------------------|-------------------------------------|
| **Langage**      | PHP 8.2+                            |
| **Base de données** | MySQL                     |
| **Autoloading**  | PSR-4 via Composer                  |
| **Code style**   | PSR-12                              |
| **Outils**       | PHP_CodeSniffer, PHP-CS-Fixer       |
| **HTTP**         | Routeur custom utilisant les attributs PHP |
| **Sécurité**     | HTTP Basic + PDO préparé            |
| **Validation**   | DTO + Validator générique           |
| **Architecture** | MVC découplée                       |

---

## 🚀 Installation

### 1. Installer les dépendances
```bash
composer install
```

### 2. Configurer la base de données
Créer un fichier `.env` à la racine :
```env
APP_ENV=dev
DB_NAME=api_test
DB_USER=root
DB_PASS=
DB_PORT=3306
DATABASE_URL="mysql:host=;port=;dbname=api_test;charset=utf8mb4"
```
---

## ▶️ Démarrage

Lancer le serveur PHP intégré :
```bash
php -S 127.0.0.1:8000 -t public public/index.php
```

L’API sera disponible sur :
👉 **http://localhost:8000**

---

## 🔐 Authentification

Toutes les routes (sauf `auth/login` et `/users en POST`) utilisent **HTTP Basic Auth**.

### Header requis :
```
Authorization: Basic base64(username:password)
```

### En cas d’erreur :
```
401 Unauthorized
```

---

## 📡 Endpoints

### `POST /stores/search`

Recherche paginée avec filtres et tri.

| Paramètre | Type  | Défaut | Description                      |
|------------|-------|---------|----------------------------------|
| `page` | int | 1 | Numéro de page                   |
| `size` | int | 50 | Nombre de résultats              |
| `filters` | object | - | Filtres (name, created_at, etc.) |
| `order` | string | id | Champ de tri                     |
| `direction` | string | ASC | Sens du tri (ASC ou DESC)        |

#### Exemple de requête :
```http
POST /stores/search
Authorization: Basic dXNlcjpwYXNz
Content-Type: application/json

{
  "page": 1,
  "size": 25,
  "filters": {
    "name": "Shop",
    "city": "Paris"
  },
  "order": "created_at",
  "direction": "DESC"
}
```

#### Exemple de réponse :
```json
{
  "data": [
    {
      "id": 1,
      "name": "Paris Store",
      "created_at": "2024-10-01T12:32:00",
      "updated_at": "2024-10-15T14:50:00"
    }
  ],
  "meta": {
    "page": 1,
    "size": 25,
    "total": 100
  }
}
```

---

## 🛡️ Sécurité et prévention des injections

✅ **Sécurisé**
- Requêtes SQL via `prepare()` + `execute()` (PDO)
- Aucun paramètre utilisateur injecté directement
- Champs de tri et direction validés via whitelist
- Entrées vérifiées via DTO + Validator

---
## ⚙️ Composants développés sur mesure
### 🔄 Routeur custom

- Le routeur a été entièrement développé à la main, sans framework.
- Il s’appuie sur les attributs PHP 8 (#[Route(...)]) pour enregistrer dynamiquement les routes des contrôleurs.
- Chaque méthode annotée est automatiquement liée à une URL avec :

   - Gestion des paramètres dynamiques (/stores/{id})
   - Filtrage par méthode HTTP (GET, POST, PUT, DELETE, …)
   - Injection automatique des dépendances dans les constructeurs
   - Réponses JSON standardisées avec les bons codes HTTP

Ce composant constitue le cœur du framework et offre une base légère, claire et extensible.

### ✅ Validator générique

- Le validateur est une implémentation maison inspirée de Symfony Validator.
- Il exploite la réflexion PHP (ReflectionClass) pour parcourir les propriétés des DTO et appliquer des attributs tels que #[NotBlank], #[Email], #[Length(min:3, max:255)], etc.

- Sélection automatique du validateur adapté

- Sortie uniforme sous forme de tableau d’erreurs

- Validation centralisée et fortement typée

Résultat : une sécurité renforcée, zéro duplication de logique, et une gestion propre des entrées utilisateur avant tout accès à la base.

## 🧾 Code Style PSR-12


### Fichiers de configuration :
```
.php-cs-fixer.php
phpcs.xml
.editorconfig
```

---

## 🧰 Commandes utiles

| Commande | Description |
|-----------|-------------|
| `composer cs` | Vérifie le code (lint + PSR-12) |
| `composer fix` | Corrige automatiquement le style |
| `composer lint` | Vérifie la syntaxe PHP |

---

## 🧪 Exemple de route minimale

```php
#[Route('/ping', methods: ['GET'])]
public function ping(): JsonResponse
{
    return new JsonResponse(['pong' => true], 200);
}
```
---

## 👤 Créer un utilisateur

**Endpoint**

```bash
curl -i -X POST http://127.0.0.1:8000/users \
  -H "Content-Type: application/json" \
  -d '{"email":"user1@example.com","password":"password"}'
```

## 👤 Connection à un utilisateur

**Endpoint**
```bash
curl -i -X POST http://127.0.0.1:8000/auth/login \
-u user1@example.com:password
```
---
