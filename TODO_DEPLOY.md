# Guide de Déploiement Laravel sur Render (Gratuit)

## Étape 1 : Prérequis

### 1.1 Compte GitHub
- Créez un compte sur [github.com](https://github.com)
- Installez Git sur votre machine

### 1.2 Compte Render
- Créez un compte sur [render.com](https://render.com)
- Connectez avec votre compte GitHub

---

## Étape 2 : Préparer l'application

### 2.1 Fichiers déjà créés :
- ✅ `Procfile` - Commande de démarrage pour Render
- ✅ `.htaccess` - Configuré pour Laravel

### 2.2 Variables d'environnement à configurer sur Render :
```
APP_NAME=Facturation
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-app.onrender.com
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

DB_CONNECTION=mysql
DB_HOST=votre-mysql-host.onrender.com
DB_PORT=3306
DB_DATABASE=facturation
DB_USERNAME=votre-user
DB_PASSWORD=votre-mot-de-passe
```

---

## Étape 3 : Créer la base de données MySQL sur Render

1. Connectez-vous à [Render Dashboard](https://dashboard.render.com)
2. Cliquez sur **"New +"** → **"PostgreSQL"** ou **"MySQL"**
3. Configuration :
   - **Name** : `facturation-db`
   - **Plan** : Free
4. Cliquez **"Create Database"**
5. Attendez le déploiement, puis copiez :
   - **Internal Database URL** (pour DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)

---

## Étape 4 : Pousser le code sur GitHub

### 4.1 Initialiser Git (si pas fait)
```bash
cd c:/laragon/www/facturation
git init
git add .
git commit -m "Ready for production deployment"
```

### 4.2 Créer le dépôt GitHub
1. Allez sur [github.com/new](https://github.com/new)
2. Nom du dépôt : `facturation-laravel`
3. Cliquez **"Create repository"**

### 4.3 Lier et pousser
```bash
git remote add origin https://github.com/VOTRE_USERNAME/facturation-laravel.git
git branch -M main
git push -u origin main
```

---

## Étape 5 : Déployer sur Render

### 5.1 Créer le Web Service
1. Render Dashboard → **"New +"** → **"Web Service"**
2. Connectez votre dépôt GitHub
3. Sélectionnez le dépôt `facturation-laravel`
4. Configuration :
   - **Name** : `facturation-app`
   - **Branch** : `main`
   - **Build Command** : `composer install --no-dev --optimize-autoloader`
   - **Start Command** : `php artisan serve --host=0.0.0.0 --port=$PORT`
   - **Plan** : Free

### 5.2 Variables d'environnement
Dans la section **"Environment"**, ajoutez :
```
APP_NAME=Facturation
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-app.onrender.com
```

### 5.3 Générer APP_KEY
Cliquez sur le bouton **"Manual Deploy"** → **"Clear build cache & Deploy"**

---

## Étape 6 : Configurer la base de données

1. Après le premier déploiement, cliquez sur **"Shell"** dans votre service
2. Générez la clé d'application :
```bash
php artisan key:generate
```
3. Copiez la clé générée et ajoutez-la aux variables d'environnement :
```
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxx
```
4. Redéployez l'application

5. Exécutez les migrations et seeders :
```bash
php artisan migrate --force
php artisan db:seed --force
```

---

## Étape 7 : Vérifier le déploiement

1. Ouvrez l'URL fournie par Render
2. Testez la page de connexion
3. Connectez-vous avec les identifiants par défaut :

### Identifiants par défaut (après seed) :

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@facturation.com | admin123 |
| Employé | employe@facturation.com | employe123 |

---

##⚠️ Important : Modifier le mot de passe par défaut

Après la première connexion, allez dans les paramètres et modifiez le mot de passe par défaut pour des raisons de sécurité.

---

## Commandes Utiles après déploiement

```bash
# Vider le cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Annuler les migrations (si besoin)
php artisan migrate:rollback

# Créer un nouvel utilisateur admin
php artisan tinker
User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => bcrypt('password'), 'role' => 'admin']);
```

---

## Résumé des Étapes Rapides

1. ✅ Préparer l'application (fait)
2. ✅ Créer compte GitHub et Render
3. ✅ Créer MySQL sur Render
4. ✅ Pousser sur GitHub
5. ✅ Créer Web Service sur Render
6. ✅ Configurer variables d'environnement
7. ✅ Générer APP_KEY
8. ✅ Exécuter migrations
9. ✅ Tester l'application

