# SnowTricks - README

Bienvenue sur **SnowTricks**, une application Symfony permettant de partager et de gérer des figures (tricks) de sports de glisse, avec la possibilité d’ajouter des images, des vidéos et des commentaires, tout en assurant une gestion des utilisateurs (inscription et authentification).

---

1. [Prérequis](#prérequis)  
2. [Installation et Configuration](#installation-et-configuration)  
3. [Analyse de qualité](#analyse-de-qualité)  
4. [Principes SOLID et Design Patterns](#principes-solid-et-design-patterns)

---

## Prérequis

- **PHP 8.1** ou version supérieure  
- **Composer** (pour la gestion des dépendances)  
- **Base de données** (MySQL, PostgreSQL, etc.)  
- **Symfony CLI** (optionnelle, mais recommandée)
- **Node.js** et **npm** (ou **yarn**), si vous gérez les assets front-end avec Webpack

---

## Installation et Configuration

1. **Récupération du projet :**  
Pour commencer, clonez le dépôt Git, puis placez-vous dans le dossier correspondant :

```bash
git clone https://github.com/Adrien1988/OcProjectSnowTricks.git
cd SnowTricks
```

2. **Installation des dépendances :**
Toutes les dépendances nécessaires sont gérées via Composer. Pour les installer, entrez la commande suivante :

```bash
composer install
```

Si vous avez un front-end géré par Webpack (ou un autre bundler), installez également les dépendances Node (ex. npm install ou yarn install) :

```bash 
npm install
```

3. **Configuration des variables d’environnement :**

Le fichier .env contient les variables par défaut. Pour personnaliser la configuration (connexion à la base de données, mailer, etc.), vous pouvez créer ou modifier un fichier .env.local :

```bash
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/snowtricks_db?serverVersion=8.0"
MAILER_DSN="smtp://localhost"
```
Adaptez ces valeurs (utilisateur, mot de passe, port, etc.) à votre environnement.

4. **Création de la base de données :** 

Une fois la configuration effectuée, créez la base indiquée dans la variable DATABASE_URL. Pour cela :

```bash
php bin/console doctrine:database:create
```

5. **Import d'un fichier SQL pour pré-remplir la base :**

Utilisez le fichier SQL qui se trouve à la racine du projet : 

```bash
mysql -u db_user -p snowtricks_db < snow_tricks.sql
```
ou importer le fichier via l'interface du service de BDD que vous utilisez.


6. **Exécution des migrations :**

Pour générer la structure (tables, colonnes) nécessaire au fonctionnement du projet :

```bash
php bin/console doctrine:migrations:migrate
```


7. **Chargement de données de test (fixtures) :**
Si vous souhaitez ajouter des exemples de données (figures, images, utilisateurs, etc.), exécutez :

```bash
php bin/console doctrine:fixtures:load
```
Attention : cette action peut réinitialiser le contenu de certaines tables.

8. **Lancement du serveur de développement :**
Pour démarrer l’application en local :

```bash
symfony server:start
```

Si vous ne possédez pas la CLI Symfony :

```bash
php -S 127.0.0.1:8000 -t public
```

Vous pourrez ensuite accéder à l’application à l’adresse http://127.0.0.1:8000

9. ** Compilation des assets avec Webpack :**
Pour gérer et compiler vos assets avec webpack, exécutez la commande appropriée : 

```bash 
npm run dev
```
en local


```bash 
npm run build
```
en prod


## Analyse de qualité

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/d72c269f2f9e4500b2a557d51115d49c)](https://app.codacy.com/gh/Adrien1988/OcProjectSnowTricks/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

## Principes SOLID et Design Patterns

Ce projet applique au maximum les principes SOLID :

- Single Responsibility Principle : chaque classe se concentre sur une responsabilité unique.
- Open/Closed Principle : extensible sans modifier le code source existant.
- Liskov Substitution Principle : les classes filles peuvent remplacer les classes mères sans briser le fonctionnement.
- Interface Segregation Principle : privilégier des interfaces spécialisées plutôt qu’une interface globale.
- Dependency Inversion Principle : injection de dépendances (services) pour dépendre d’abstractions.

Design Patterns utilisés :

- MVC (Model – View – Controller) : Architecture principale du projet (structure Symfony)
- Repository Pattern : pour gérer l’accès aux données de chaque entité.
- Form Builder Pattern : pour créer et gérer les formulaires (Form Types).
- Event Listener : pour réagir aux événements du framework et du cycle de vie des entités.
- Factory Pattern : Approche utilisée lors de la création d’entités à partir de données de formulaire, même si aucune classe Factory dédiée n’est définie.