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
   <pre>
```bash
git clone https://github.com/Adrien1988/OcProjectSnowTricks.git
cd SnowTricks
```
   </pre>

2. **Installation des dépendances :**
   Toutes les dépendances nécessaires sont gérées via Composer. Pour les installer, entrez la commande suivante :
   <pre>
   ```bash
   composer install
   ```
   </pre> (version en local 2.7.4)

   Si vous avez un front-end géré par Webpack (ou un autre bundler), installez également les dépendances Node (ex. npm install ou yarn install) :
   <pre>
   ```bash 
   npm install
   ```
   </pre> (version en local 10.2.4)

3. **Configuration des variables d’environnement :**

Le fichier .env contient les variables par défaut. Pour personnaliser la configuration (connexion à la base de données, mailer, etc.), vous pouvez créer ou modifier un fichier .env.local :
    <pre>
    DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/snowtricks_db?serverVersion=8.0"
    MAILER_DSN="smtp://localhost"
    </pre>
   Adaptez ces valeurs (utilisateur, mot de passe, port, etc.) à votre environnement.

4. **Création de la base de données :** 

Une fois la configuration effectuée, créez la base indiquée dans la variable DATABASE_URL. Pour cela :
   <pre>
   ```bash
   php bin/console doctrine:database:create
   ```
   </pre>

   4.1 **Import d'un fichier SQL pour pré-remplir la base :**

   Utilisez le fichier SQL qui se trouve à la racine du projet : 

   <pre>
   ```bash
   mysql -u db_user -p snowtricks_db < snow_tricks.sql
   ```
   </pre> ou importer le fichier via l'interface du service de BDD que vous utilisez.

   4.1 **Import d'un fichier SQL pour pré-remplir la base :**

   Utilisez le fichier SQL qui se trouve à la racine du projet : 

   <pre>
   ```bash
   mysql -u db_user -p snowtricks_db < snow_tricks.sql
   ```
   </pre> ou importer le fichier via l'interface du service de BDD que vous utilisez.

5. **Exécution des migrations :**

Pour générer la structure (tables, colonnes) nécessaire au fonctionnement du projet :
   <pre>
   ```bash
   php bin/console doctrine:migrations:migrate
   ```
   </pre>

6. **Chargement de données de test (fixtures) :**
Si vous souhaitez ajouter des exemples de données (figures, images, utilisateurs, etc.), exécutez :
   <pre>
   ```bash
   php bin/console doctrine:fixtures:load
   ```
   </pre>
   Attention : cette action peut réinitialiser le contenu de certaines tables.

7. **Lancement du serveur de développement :**
Pour démarrer l’application en local :
   <pre>
   ```bash
   symfony server:start
   ```
   </pre>
Si vous ne possédez pas la CLI Symfony :
   <pre>
   ```bash
   php -S 127.0.0.1:8000 -t public
   ```
   </pre>
Vous pourrez ensuite accéder à l’application à l’adresse http://127.0.0.1:8000

8. ** Compilation des assets avec Webpack :**
Pour gérer et compiler vos assets avec webpack, exécutez la commande appropriée : 
   <pre>
   ```bash 
   npm run dev
   ```
   </pre> en local

   <pre>
   ```bash 
   npm run build
   ```
   </pre> en prod


## Analyse de qualité

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/d72c269f2f9e4500b2a557d51115d49c)](https://app.codacy.com/gh/Adrien1988/OcProjectSnowTricks/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

## Principes SOLID et Design Patterns

Ce projet applique au maximum les principes SOLID :

- Single Responsibility Principle : chaque classe se concentre sur une responsabilité unique.
- Open/Closed Principle : extensible sans modifier le code source existant.
- Liskov Substitution Principle : les classes filles peuvent remplacer les classes mères sans briser le fonctionnement.
- Interface Segregation Principle : privilégier des interfaces spécialisées plutôt qu’une interface globale.
- Dependency Inversion Principle : injection de dépendances (services) pour dépendre d’abstractions.

Design Patterns utilisés
En se basant sur l’ensemble du code et des discussions du projet :

- Repository Pattern : pour gérer l’accès aux données de chaque entité.
- Form Builder Pattern : pour créer et gérer les formulaires (Form Types).
- Event Subscriber/Listener : pour réagir aux événements du framework et du cycle de vie des entités.