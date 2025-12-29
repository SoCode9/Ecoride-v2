# Ecoride
EcoRide est une application web de covoiturage conçue pour faciliter la mise en relation entre conducteurs et passagers, dans une démarche écologique et économique.

Ce dépôt contient le code source du projet, incluant un frontend (HTML/CSS/JS) et un backend en PHP, avec une base de données MySQL et une base de données NoSQL MongoDB.

---

## Prérequis
Avant de déployer l’application en local, assurez-vous d’avoir installé :
- Docker Desktop
- Git
- Un navigateur web moderne

Toutes les autres dépendances (PHP, Apache, MySQL, MongoDB, Mailpit, etc.) sont gérées par les conteneurs Docker.

## Installation 
### 1. Cloner le dépôt
```bash
git clone https://github.com/SoCode9/Ecoride-v2.git
```

### 2. Créer le fichier .env
À la racine du projet, créer un fichier .env (non versionné) contenant vos paramètres :
```bash
APP_ENV=local

# MySQL
DB_HOST=db
DB_PORT=3306
DB_NAME=ecoride2
DB_USER=votreuser
DB_PASS=votremdp
DB_CHARSET=utf8mb4
DB_ROOT_PASSWORD=votremdp

# Mongo
MONGO_ROOT_USER=votreuser
MONGO_ROOT_PASS=votremdp
MONGO_DB=ecoride2
MONGO_URI=mongodb://votreuser:votremdp@mongo:27017/ecoride2?authSource=admin

# mongo-express
ME_BASIC_USER=votreuser
ME_BASIC_PASS=votremdp

# Mail (Mailpit)
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM=no-reply@ecoride.local
MAIL_FROM_NAME=EcoRide
```

### 3. Démarrer les conteneurs
```bash
docker compose up -d --build
```
> Les services démarrés sont :
>- Application PHP/Apache → http://localhost:8090
>- phpMyAdmin → http://localhost:8084
>- Mongo Express → http://localhost:8083
>- Mailpit → http://localhost:8026

> Les dépendances PHP sont installées automatiquement lors du build Docker

> Assurez-vous que Docker Desktop est lancé avant d’exécuter la commande Docker

### 4. Installer les tables SQL
Utilisez les scripts SQL fournis dans le dossier du projet afin de créer les tables, puis créer l’utilisateur administrateur à l’aide du script dédié.

## Envoi d'emails
Les emails envoyés par l’application sont interceptés par Mailpit (service SMTP de test).
- Interface : http://localhost:8026
- Paramètres SMTP : host=mailpit, port=1025.

## Démarrer l'application
Une fois les conteneurs lancés, l’application est accessible à l’adresse :
> http://localhost:8090
