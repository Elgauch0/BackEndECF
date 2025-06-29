# ZooV1.2 Backend

Ce projet est le backend de l'application Zoo, développé avec le framework Symfony. Il fournit l'API et la logique métier nécessaires pour gérer les données de l'application, incluant les animaux, les employés, les visiteurs, etc.

## Technologies utilisées

- **Symfony**: Framework PHP pour le développement d'applications web robustes et évolutives.
- **PHP**: Langage de programmation utilisé pour le développement du backend.
- **Doctrine**: ORM (Object-Relational Mapper) pour la gestion de la base de données relationnelles (MySQL/MariaDB) et NoSQL (MongoDB).
- **MySQL/MariaDB**: Système de gestion de base de données relationnelles.
- **MongoDB**: Base de données NoSQL.
- **LexikJWTAuthenticationBundle**: Gestion de l'authentification par JWT (JSON Web Tokens).
- **NelmioCorsBundle**: Gestion du CORS (Cross-Origin Resource Sharing) pour permettre les requêtes depuis différents domaines.
- **VichUploaderBundle**: Gestion de l'upload de fichiers.
- **DoctrineFixturesBundle**: Pour charger des données de test (fixtures).

## Prérequis

Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre machine :

- **PHP**: Version 8.2 ou supérieure (recommandée).
- **Composer**: Gestionnaire de dépendances pour PHP.
- **MySQL/MariaDB**: Système de gestion de base de données relationnelles.
- **MongoDB**: Base de données NoSQL.
- **Symfony CLI**: Interface en ligne de commande pour Symfony (facultatif mais recommandé).
- **Extension MongoDB pour PHP**: Nécessaire pour interagir avec MongoDB depuis PHP.

## Installation

1.  **Cloner le dépôt :**

    ```bash
    git clone [https://github.com/Elgauch0/BackEndECF](https://github.com/Elgauch0/BackEndECF)
    ```

2.  **Accéder au répertoire du projet :**

    ```bash
    cd BackEndECF  # Assurez-vous du chemin correct
    ```

3.  **Installer les dépendances :**

    ```bash
    composer install
    ```

    Ce projet utilise les dépendances listées dans le `composer.json` :

    ```json
    "require": {
        "php": ">=8.2",
        "ext-ctype": "*",
        "ext-iconv": "*",
        "doctrine/annotations": "^2.0.2",
        "doctrine/dbal": "^3.9.4",
        "doctrine/doctrine-bundle": "^2.13.2",
        "doctrine/doctrine-migrations-bundle": "^3.4.1",
        "doctrine/mongodb-odm-bundle": "^5.0.1",
        "doctrine/orm": "^3.3.2",
        "lexik/jwt-authentication-bundle": "^3.1.1",
        "nelmio/cors-bundle": "^2.5",
        "symfony/console": "7.2.*",
        "symfony/dotenv": "7.2.*",
        "symfony/flex": "^2.4.7",
        "symfony/framework-bundle": "7.2.*",
        "symfony/mailer": "7.2.*",
        "symfony/runtime": "7.2.*",
        "symfony/security-bundle": "7.2.*",
        "symfony/serializer": "7.2.*",
        "symfony/validator": "7.2.*",
        "symfony/yaml": "7.2.*",
        "vich/uploader-bundle": "^2.5.1",
        "doctrine/doctrine-fixtures-bundle": "^4.0"
    }
    ```

4.  **Configurer les bases de données :**

    - **MySQL/MariaDB :**
      - Créer une base de données vide (ex: `zoov12`).
      - Modifier le fichier `.env.local` pour configurer les paramètres de connexion à la base de données.
    - **MongoDB :**
      - Assurez-vous que le serveur MongoDB est en cours d'exécution.
      - Configurez les paramètres de connexion dans le fichier `.env.local`.

5.  **Exécuter les migrations de Doctrine (pour MySQL/MariaDB) :**

    ```bash
    php bin/console doctrine:migrations:migrate
    ```

6.  **Charger les fixtures (après les migrations) :**

    ```bash
    php bin/console doctrine:fixtures:load
    pour les fixtures  jai utilisé des fixtures que pour les users
    admin :'admin@admin.com',
    vet : 'veterinaire@vet.com',
    employé:'employe@emp.com'
    le password pour les trois est password;
    ```

## Configuration

- **Fichier `.env.local`**: Contient les variables d'environnement, y compris les informations de connexion aux bases de données, les clés API, etc. **Ne committez jamais ce fichier dans un dépôt public.**
- **Fichiers de configuration de Symfony**: Les fichiers de configuration de Symfony se trouvent dans le répertoire `config/`.

## Utilisation

- **Lancer le serveur de développement Symfony :**

  ```bash
  symfony server:start
  ```

## Points importants

## Structure du code

Cette API RESTful a été développée avec le framework Symfony, reconnu pour sa robustesse et sa sécurité. Le choix d'une architecture séparée entre le frontend et le backend offre plusieurs avantages :

## Flexibilité :

Le client a la possibilité de développer son application avec le framework de son choix, ou même de disposer d'une application native (mobile, desktop, etc.).

## Évolutivité :

Le backend peut évoluer indépendamment du frontend, facilitant la maintenance et l'ajout de nouvelles fonctionnalités.

## Versioning :

Il est possible de gérer différentes versions de l'API pour assurer la compatibilité avec les applications existantes.

L'architecture de l'API repose sur le pattern de Symfony, où les contrôleurs jouent un rôle central. Le fichier index.php capture les requêtes envoyées par le client et les achemine vers le contrôleur approprié, qui se charge de traiter la requête et de renvoyer la réponse.

## Sécurité

La sécurité est une priorité dans ce projet. Pour garantir l'intégrité des données et protéger l'API, plusieurs mesures ont été mises en place :

Authentification par JWT : Les requêtes vers les routes protégées sont validées à l'aide de tokens JWT (JSON Web Tokens). Cela permet de s'assurer que seules les personnes autorisées peuvent accéder à certaines ressources.
Firewall : Un firewall a été configuré pour protéger spécifiquement les routes sensibles, simplifiant ainsi le code et améliorant la performance.
Validation des données : Les données envoyées par le client sont rigoureusement validées pour prévenir les injections SQL et autres vulnérabilités.
Gestion des rôles : Un système de gestion des rôles permet de définir différents niveaux d'accès aux ressources, en fonction des privilèges de chaque utilisateur.
Validation des champs : Les champs envoyés par le client sont validés pour s'assurer qu'ils respectent les formats attendus et qu'ils ne contiennent pas de données malveillantes.
Tests
Actuellement, les tests ont été réalisés manuellement via le navigateur. Il est prévu d'ajouter des tests unitaires et fonctionnels pour améliorer la qualité et la fiabilité du code.

## Déploiement

Le processus de déploiement est en cours d'élaboration. Une documentation détaillée sera fournie pour faciliter le déploiement de l'API sur différents types de serveurs (Nginx, Apache, etc.) ou de plateformes cloud.

## Licence

pas de Licence

## Contact

Vous pouvez me contacter pour quoi que ce soit au 0783749209.
