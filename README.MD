# Mélanie API

> OpenClassRooms Project
[![SymfonyInsight](https://insight.symfony.com/projects/7f94ef16-ed89-4e1a-903b-2541455b116b/big.svg)](https://insight.symfony.com/projects/7f94ef16-ed89-4e1a-903b-2541455b116b)

## Introduction

Ce projet API est créé dans le cadre de ma formation chez OpenClassRooms. L'API permet de faire X, Y, et Z. Elle est destinée à être utilisée par des développeurs qui souhaitent faire A, B, et C.
L'export de la collection de cette API se trouve dans le fichier bilemo.postman_collection.json à la racine.

## Prérequis

- PHP 7.4 ou supérieur
- Composer
- MySQL 5.7 ou supérieur
- Postman pour les tests de l'API

## Installation

### Étape 1: Cloner le dépôt

Clonez le dépôt GitHub sur votre machine locale.

### Étape 2: Installer les dépendances

Installez toutes les dépendances nécessaires en utilisant Composer.

```shell
composer install

### Étape 3: Configuration de la base de données

Configurez la base de données en modifiant le fichier .env. Spécifiez les paramètres de la base de données (host, dbname, user, password).

### Étape 4: Création de la base de données

Après avoir configuré votre base de données dans le fichier .env, créez la base de données avec la commande suivante :

bin/console doctrine:schema:update -f

### Étape 5: Création d'un utilisateur
Créez un utilisateur avec la commande ci-dessous :
bin/console app:create-user your-email@domain.com your-password

### Étape 6: Génération de la paire de clés pour JWT
commande : composer require lexik/jwt-authentication-bundle
Configurez le chemin et la phrase de passe des clés SSL dans votre fichier .env :
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=
Configurer la sécurité de l'application dans security.yaml (voir documentation https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/index.html)

### Utilisation Authentification et Tokens
Pour tester l'API via Postman, vous devez mettre en place un système de "Bearer Token". Utilisez le token généré précédemment comme valeur du "Bearer Token".

Routes
Toutes les routes de l'API doivent être ajoutées à Postman.

Requêtes POST
Pour les routes qui utilisent la méthode POST, vous pouvez ajouter des éléments en fournissant les informations nécessaires dans l'onglet "Body" de Postman.

