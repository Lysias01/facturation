# Guide de Déploiement Laravel sur Render (Gratuit)

## Résumé des fichiers créés/modifiés

1. **Dockerfile** - Configuration Docker pour PHP 8.2 + Apache
2. **render.yaml** - Configuration du service Render
3. **Procfile** - Commande de démarrage (pour Heroku/Render)

---

## Étapes pour déployer sur Render

### Étape 1 : Créer un compte Render
1. Aller sur [render.com](https://render.com)
2. Cliquer sur "Sign Up" et connecter avec GitHub

### Étape 2 : Créer la base de données PostgreSQL
1. Dans le dashboard Render, cliquer sur "New +"
2. Sélectionner "PostgreSQL"
3. Configurer :
   - Name : `facturation-db`
   - Plan : Free
4. Cliquer sur "Create Database"
5. **Noter les informations de connexion** (host, database, user, password)

### Étape 3 : Mettre à jour render.yaml avec vos informations de DB
Modifier le fichier `render.yaml` avec les vraies informations de votre base de données :
- DB_HOST : l'hôte de votre PostgreSQL Render
- DB_DATABASE : le nom de la base
- DB_USERNAME : l'utilisateur
- DB_PASSWORD : le mot de passe

### Étape 4 : Pousser le code sur GitHub
```bash
# Initialiser git (si pas fait)
git init
git add .
git commit -m "Ready for deployment"

# Créer un dépôt GitHub et pousser
git remote add origin https://github.com/votre-compte/facturation.git
git push -u origin main
```

### Étape 5 : Connecter GitHub à Render
1. Dans Render, cliquer sur "New +" puis "Web Service"
2. Sélectionner votre dépôt GitHub
3. Configurer :
   - Name : `facturation-app`
   - Runtime : Docker
   - Plan : Free

### Étape 6 : Variables d'environnement
Les variables sont déjà configurées dans `render.yaml` :
- APP_ENV=production
- APP_DEBUG=false
- DB_CONNECTION=pgsql
- DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

### Étape 7 : Déployer
1. Cliquer sur "Create Web Service"
2. Attendre le build (environ 5-10 minutes)
3. Une fois déployé, l'URL sera disponible

### Étape 8 : Exécuter les migrations
Après le déploiement, vous pouvez exécuter les migrations via :
1. Render Dashboard → Your Service → Shell
2. Ou utiliser le bouton "Manual Deploy" avec "Clear build cache & Deploy"

```bash
php artisan migrate
php artisan db:seed --class=DatabaseSeeder
```

---

## Informations importantes

### Base de données
- L'application utilise **PostgreSQL** sur Render (gratuit)
- Votre application locale utilise MySQL
- Les migrations sont compatibles avec les deux bases

### Limites du plan gratuit Render
- 750 heures d'exécution par mois
- Le service se met en veille après 15 minutes d'inactivité
- Se réveille automatiquement lors d'une requête

### Résolution des problèmes
Si l'application ne fonctionne pas :
1. Vérifier les logs dans Render Dashboard
2. Vérifier les variables d'environnement
3. S'assurer que les migrations ont été exécutées

---

## Fichiers de configuration

### Dockerfile
```dockerfile
FROM php:8.2-apache
# ... configuration pour PHP + Apache + PostgreSQL
```

### render.yaml
```yaml
services:
  - type: web
    name: facturation-app
    runtime: docker
    dockerfilePath: Dockerfile
    plan: free
    envVars:
      # Variables d'environnement
```

---

## Compte rendu

- Application Laravel 10.x
- PHP 8.2
- Base de données PostgreSQL (Render)
- Hébergement gratuit sur Render
- Budget : 0 fcfa ✓
