# TODO Déploiement Render - Facturation App

## ✅ Étape 1: Créer TODO.md (fait)

## ✅ Étape 2: Fix Dockerfile
- [x] Supprimer caches build-time
- [x] Ajouter script start.sh runtime avec migrate/config/cache/perms
- [x] Mettre à jour CMD pour utiliser start.sh

## ✅ Étape 3: Update render.yaml
- [x] APP_DEBUG=false
- [x] LOG_CHANNEL=errorlog + DB_SSLMODE=prefer

## ✅ Étape 4: Fix .env.example
- [x] Template Postgres prod

## ⬜ Étape 5: Supprimer Procfile inutile

## ⬜ Étape 6: Test local (docker build)
- docker build -t facturation .
- docker run -p 8080:80 -e DB_*=... facturation

## ⬜ Étape 7: Push & Deploy
- git add/commit/push
- Vérifier Render logs
- Tester app en ligne

**Statut: Prêt à implémenter étape par étape**
