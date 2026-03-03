# TODO - Historique (Logs) Export et Suppression

## 1. Export PDF/Excel avec filtres
- [ ] Modifier ActivityLogController pour supporter le paramètre "per_page" pour les exports
- [ ] Ajouter des champs de date dans les boutons d'export (historique/index.blade.php)
- [ ] Ajouter un sélecteur pour le nombre de pages/éléments à exporter

## 2. Suppression avancée des logs
- [ ] Modifier ActivityLogController cleanup() pour supporter:
  - Suppression par date (entre deux dates)
  - Suppression par nombre de jours (actuel)
- [ ] Mettre à jour le formulaire de nettoyage dans la vue

## 3. Tests et vérification
- [ ] Tester les exports avec filtres
- [ ] Tester la suppression par période
