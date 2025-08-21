<h6 align="center">
  ⚠️<br><br>
  Ce projet est <em>toujours en cours d'élaboration</em>.<br>
  <em>Toutes les images présentes dans ce dépôt sont mon unique propriété et ne peuvent pas être utilisées, copiées ou diffusées sans autorisation.</em>
  
</h6>

---

<div align="center">
  <img src="https://raw.githubusercontent.com/OzzMhnee/EscaleEvasion_eCom/refs/heads/main/public/img/logoLight.png" alt="Logo" width="300"/>
</div>

<div align="center">
  <h1>Escale Evasion - Plateforme de location saisonnière</h1>
</div>

---

<div align="center">
  <img align="center" src="https://raw.githubusercontent.com/OzzMhnee/EscaleEvasion_eCom/refs/heads/main/private/img/backgroundParallax.png" alt="Logo" />
  <br><br> <em> Bienvenue sur <strong>Escale Evasion</strong>, une application web de location saisonnière développée avec Symfony.<br>
  Ce projet permet aux utilisateurs de réserver des logements, de gérer leurs réservations, de payer un acompte en ligne via Stripe,<br> et de générer des devis et factures PDF.<br>
  Les fonctionnalités qui s'y trouvent changent selon le rôle de l'utilisateur connecté (simple User, Admin...). </em>
</div>

---

## Fonctionnalités principales

- Mise en place d'un background random avec ajout d'un filigrane de protection
- Parcours des logements disponibles (par catégorie, sous-catégorie, pagination…)
- Réservation d’un logement avec gestion des dates et désactivation des périodes déjà réservées
- Panier utilisateur pour gérer ses réservations en attente
- Paiement d’acompte sécurisé via Stripe Checkout (collecte adresse de facturation)
- Génération automatique de devis et factures PDF (Dompdf)
- Gestion des statuts de réservation (en attente, annulée, confirmée, finalisée)
- Espace utilisateur avec historique des réservations
- Notifications par email (Mailtrap en dev)
- Interface d’administration pour la gestion des utilisateurs et des produits

---

## Prérequis

- PHP >= 8.1
- Composer
- MySQL (ou MariaDB)
- Node.js & npm (pour les assets front si besoin)
- [Stripe CLI](https://stripe.com/docs/stripe-cli) (pour tester les webhooks en local)
- Un compte [Mailtrap](https://mailtrap.io/) (pour tester l’envoi d’emails en dev)

---

## Installation & Lancement

1. ### Clonez le projet :
   ```bash
   git clone https://github.com/<votre-utilisateur>/EscaleEvasion_eCom.git
   cd EscaleEvasion_eCom
   ```

2. ### Installez les dépendances PHP :
   ```bash
   composer install
   ```

3. ### Configurez vos variables d'environnement :

   - ***Copiez `.env` en `.env.local` et adaptez :***
     ```env
     APP_ENV=dev
     DATABASE_URL="mysql://root:password@127.0.0.1:3306/BDDEE?serverVersion=8.0.32&charset=utf8mb4"
     MAILER_DSN="smtp://<user>:<pass>@sandbox.smtp.mailtrap.io:2525"
     STRIPE_SECRET_KEY=sk_test_...
     ```

   - ***Créez la base de données si besoin :***
     ```bash
     php [bin/console](VALID_FILE) doctrine:database:create
     ```

4. ### Lancez les migrations:
   ```bash
   php bin/console doctrine:migrations:migrate
   php bin/console doctrine:fixtures:load
   ```

5. ### Démarrez le serveur Symfony :
    ```bash
    symfony server:start
    ```
    ou
    ```bash
    php -S localhost:8000 -t public
    ```

6. (En Local) ### Relayer les webhooks Stripe :
    ```bash
    stripe login
    stripe listen --forward-to localhost:8000/stripe/notify
    ```
---

## Notes importantes

Webhooks Stripe : Pour que Stripe puisse notifier votre serveur local, Stripe CLI doit être lancé comme ci-dessus.<br>
Paiement test : Utilisez les cartes de test Stripe.<br>
Emails : Les emails sont envoyés vers Mailtrap en environnement de développement.<br>
PDF : Les devis et factures sont générés avec Dompdf et proposés en téléchargement.<br>

---

Structure du projet

src/Controller/ : Contrôleurs Symfony (Stripe, User, Cart, Bill…)<br>
src/Service/StripePayment.php : Service d’intégration Stripe<br>
templates/ : Vues Twig<br>
config/ : Configuration Symfony et services <br>

---

Schéma Mermaid dégrossi

<img src="https://raw.githubusercontent.com/OzzMhnee/EscaleEvasion_eCom/d1a5c2db03a5fd1c08dffdf564d372827646a3c4/public/img/Mermaid.svg" align="center" alt="Diagramme d'architecture projet"  width="850">
