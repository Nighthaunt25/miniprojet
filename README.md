# Le Monde – Aggrégateur de Flux RSS

Projet réalisé en **PHP** avec stockage en **CSV** (sans base de données).

---

## 📁 Structure du projet

```
rss_agregateur/
├── index.php             ← Page de connexion
├── register.php          ← Inscription
├── validate.php          ← Validation du compte (lien email)
├── dashboard.php         ← Tableau de bord (flux RSS)
├── subscriptions.php     ← Gestion des abonnements
├── forgot_password.php   ← Mot de passe oublié
├── reset_password.php    ← Réinitialisation du mot de passe
├── logout.php            ← Déconnexion
├── css/
│   └── style.css         ← Styles CSS
├── includes/
│   ├── config.php        ← Configuration (BASE_URL, flux RSS, fichiers CSV)
│   └── functions.php     ← Toutes les fonctions métier
└── data/
    ├── users.csv         ← Stockage des utilisateurs (auto-créé)
    └── tokens.csv        ← Tokens de réinitialisation (auto-créé)
```

---

## ⚙ Installation

### Prérequis
- PHP 7.4+ avec les extensions : `simplexml`, `openssl`
- Serveur web (Apache/Nginx ou PHP built-in server)
- Connexion internet (pour récupérer les flux RSS)

### Étapes

1. **Copier le dossier** dans votre répertoire web (ex: `/var/www/html/rss_agregateur/`)

2. **Configurer** `includes/config.php` :
   ```php
   define('BASE_URL', 'http://localhost/rss_agregateur/');
   define('MAIL_FROM', 'noreply@votre-domaine.fr');
   ```

3. **Créer le dossier data** (s'il n'existe pas) avec les droits d'écriture :
   ```bash
   mkdir data
   chmod 755 data
   ```

4. **Démarrer le serveur** (ou utiliser XAMPP/MAMP) :
   ```bash
   php -S localhost:8080
   # Puis ouvrir http://localhost:8080/rss_agregateur/
   ```

---

## 🔄 Fonctionnalités

| Fonctionnalité | Priorité MoSCoW | Statut |
|---|---|---|
| Inscription avec centres d'intérêt | Must | ✅ |
| Email de validation du compte | Must | ✅ |
| Connexion / Déconnexion | Must | ✅ |
| Affichage des flux RSS abonnés | Must | ✅ |
| Modification des abonnements | Should | ✅ |
| Réinitialisation du mot de passe | Must | ✅ |
| Stockage CSV (sans BDD) | Must | ✅ |
| Interface responsive | Could | ✅ |

---

## 📊 Format CSV

### `data/users.csv`
```
email, password_hash, validated (0/1), token_validation, subscriptions (pipe-separated), created_at
```
Exemple :
```
jean@test.fr,$2y$10$xxx,1,,France|Sport|Culture,2025-01-15 10:30:00
```

### `data/tokens.csv`
```
email, token, expiry_datetime
```

---

## 📮 Configuration Email

L'envoi d'emails utilise la fonction native PHP `mail()`.  
Pour un environnement local, utiliser **MailHog** ou **Mailtrap** pour intercepter les emails de test.

Pour la production, configurer `sendmail_path` dans `php.ini` ou intégrer **PHPMailer** avec SMTP.

---

## 🗺 User Stories (MoSCoW)

### Must Have
- **US01** : En tant que visiteur, je veux m'inscrire avec mon email et choisir mes centres d'intérêt
- **US02** : En tant que nouvel inscrit, je reçois un email pour valider mon compte
- **US03** : En tant qu'utilisateur, je veux me connecter avec email/mot de passe
- **US04** : En tant qu'utilisateur, je veux consulter les derniers articles de mes flux RSS
- **US05** : En tant qu'utilisateur, j'ai oublié mon mot de passe et je veux le réinitialiser

### Should Have
- **US06** : En tant qu'utilisateur connecté, je veux modifier mes abonnements RSS

### Could Have
- **US07** : En tant qu'utilisateur, je veux voir une image de prévisualisation pour chaque article
- **US08** : En tant qu'utilisateur, je veux une interface responsive sur mobile
