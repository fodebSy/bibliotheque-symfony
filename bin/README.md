Bibliothèque municipale — Application Symfony

Application web de gestion de bibliothèque : catalogue de livres, emprunts et adhérents.
Projet réalisé avec Symfony 6.4 (PHP 8.2) dans le cadre de la formation développeur web.

Fonctionnalités


Catalogue de livres : CRUD complet, page d'accueil listant les livres disponibles, filtres par catégorie, recherche par titre ou auteur, page de détails
Gestion des emprunts : emprunt d'un livre par un adhérent avec vérification de disponibilité, retour des livres, historique des emprunts par adhérent
Administration : espace sécurisé réservé aux bibliothécaires (ROLE_ADMIN), dashboard avec statistiques en temps réel (livres, disponibilités, emprunts en cours, adhérents)
Validation des données : contraintes sur les entités (ISBN valide, format email, unicité de l'ISBN, de l'email et du numéro d'adhérent)


Choix techniques et architecture


Architecture MVC : routes déclarées par attributs #[Route] → contrôleurs minces → repositories / services → templates Twig. Aucune logique métier dans les contrôleurs.
Logique métier isolée dans un service : la classe App\Service\EmpruntManager centralise les règles d'emprunt et de retour — vérification de la disponibilité (levée d'une \DomainException sinon), date de retour prévue à +14 jours, bascule du statut disponible du livre.
Modélisation : Emprunt est une entité d'association (entité pivot) reliée par deux relations ManyToOne vers Livre et Adherent, car la relation porte des données (les dates). La propriété dateRetourEffective est nullable : NULL signifie « emprunt en cours ».
Requêtes métier centralisées dans les repositories : recherche par titre/auteur (QueryBuilder avec LIKE paramétré, protection contre l'injection SQL), historique trié par date décroissante, comptages via count() pour le dashboard (pas d'hydratation d'objets inutile).
Sécurité : entité User (bibliothécaire, accès back-office) volontairement distincte de l'entité Adherent (usager) , deux notions métier différentes. Mots de passe hachés automatiquement (password_hashers: 'auto'). Zone /admin protégée en double couche : access_control (^/admin → ROLE_ADMIN) dans security.yaml et attribut #[IsGranted('ROLE_ADMIN')] sur le contrôleur. Connexion protégée par jeton CSRF.
Données de test : DataFixtures insérant des livres, des adhérents et un compte administrateur (mot de passe haché via UserPasswordHasherInterface).


Prérequis


PHP >= 8.1
Composer
Un serveur MySQL ou MariaDB


Installation

bashgit clone https://github.com/fodebSy/bibliotheque-symfony.git
cd bibliotheque-symfony
composer install

Créer un fichier .env.local à la racine du projet avec la connexion à votre base de données :

DATABASE_URL="mysql://root:@127.0.0.1:3306/bibliotheque?serverVersion=10.4.32-MariaDB"

(Adapter l'utilisateur, le mot de passe, le port et le serverVersion à votre serveur — par exemple serverVersion=8.0 pour MySQL 8.)

Puis initialiser la base et lancer le serveur :

bashphp bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
symfony serve

L'application est accessible sur http://127.0.0.1:8000

Compte de démonstration

RôleEmailMot de passeAdministrateuradmin@bibliotheque.fradmin123

L'espace d'administration est accessible sur /admin (connexion via /login).

Structure des données

Livre (1) ←—— (N) Emprunt (N) ——→ (1) Adherent

Un emprunt relie un livre et un adhérent et porte trois dates : date d'emprunt, date de retour prévue (+14 jours) et date de retour effective (NULL tant que le livre n'est pas rendu).

Structure du projet

src/
├── Controller/     HomeController, LivreController (CRUD), EmpruntController,
│                   AdherentController, AdminController, SecurityController
├── Entity/         Livre, Adherent, Emprunt, User
├── Repository/     Requêtes métier (recherche, historique, comptages)
├── Service/        EmpruntManager (logique métier des emprunts)
└── DataFixtures/   Données de test + compte administrateur
templates/          Vues Twig (home, livre, adherent, admin, security)
migrations/         Historique du schéma de base de données