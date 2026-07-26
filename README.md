
# Bibliothèque en Ligne

![Symfony](https://img.shields.io/badge/Symfony-6.4-black.svg)
![PHP](https://img.shields.io/badge/PHP-8.1-purple.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0-blue.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)
![Status](https://img.shields.io/badge/status-completed-brightgreen.svg)

## Description

Application web de gestion de bibliothèque développée avec Symfony 6.4. Les utilisateurs peuvent emprunter des livres, gérer leur profil, et les administrateurs peuvent gérer le catalogue et les utilisateurs.

## Fonctionnalites

### Utilisateurs
- Inscription avec validation par administrateur
- Connexion / Déconnexion
- Modification du mot de passe
- Résiliation de compte (sans emprunt en cours)
- Consultation de l'historique des emprunts

### Gestion des livres
- Recherche par titre, auteur, catégorie
- Panier d'emprunt
- Validation du panier avec confirmation
- Gestion des exemplaires disponibles
- Renouvellement des emprunts (dernier jour uniquement)

### Administration
- Gestion des livres (ajout, modification, suppression)
- Gestion des utilisateurs (ajout, suppression)
- Gestion des demandes d'inscription
- Gestion des alertes (livres epuises, retards)

## Architecture du projet

Le projet suit l'architecture MVC (Modèle-Vue-Contrôleur) propre a Symfony.

Le dossier src/Controller contient tous les controleurs de l'application. BookController gere les operations liees aux livres, BorrowingController gere les emprunts, SecurityController gere l'authentification, RegistrationController gere les inscriptions, MainController gere la page d'accueil et ShopController gere le panier. Le sous-dossier Admin contient les controleurs pour l'interface d'administration avec DashboardController, BookCrudController, AuthorCrudController, EditorCrudController, UserCrudController et ContactCrudController.

Le dossier src/Entity contient les entites Doctrine representant les tables de la base de donnees : User, Book, Author, Editor, Borrowing, Comment, Contact, Mark et Shop.

Le dossier src/Repository contient les repositories pour interroger la base de donnees.

Le dossier src/Form contient les formulaires : RegistrationFormType, PasswordModifyType, BookType, AuthorType, EditorType, ContactType et MarkType.

Le dossier src/EventListener contient les ecouteurs d'evenements : BorrowingListener et UserListener.

Le dossier src/EventSubscriber contient EasyAdminSubscriber pour personnaliser l'interface d'administration.

Le dossier src/Security/Voter contient les votants pour la securite : BookCreatorVoter et ShopCreatorVoter.

Le dossier templates contient les templates Twig organises par fonctionnalite : admin, book, borrowing, main, partials, registration, security et shop.

Le dossier config contient la configuration de l'application : bundles, packages (doctrine, security, twig, vich_uploader...), routes et services.

Le dossier public contient le point d'entree index.php et les assets publiques.

Le dossier assets contient les fichiers JavaScript et CSS geres par AssetMapper.

## Installation
  git clone https://github.com/Diengelh/Bibliotheque-en-ligne.git
  cd Bibliotheque-en-ligne
  composer install
  npm install


## Configuration

1. Creer un fichier .env.local et configurer la base de donnees :
   DATABASE_URL="mysql://user:password@127.0.0.1:3306/bibliotheque
2.   Creer la base de donnees et executer les migrations :
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrat
3. Charger les donnees initiales (si disponible) :
    php bin/console doctrine:fixtures:load

## Lancement
    php bin/console server:run


Acceder a http://localhost:8000

## Technologies utilisees

- Symfony 6.4
- Doctrine ORM
- MySQL
- Twig
- EasyAdmin (interface d'administration)
- VichUploader (gestion des images)
- AssetMapper
- Bootstrap (interface utilisateur)
- Stimulus (JavaScript)

## Auteur

- Elhadji Loum Dieng

## Licence

MIT
