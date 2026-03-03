# Plan de Redesign - Application de Facturation

## Objectifs
- Design professionnel et moderne
- Totalement responsive (mobile-first)
- Pas d'émojis - utiliser des icônes Bootstrap Icons
- Bonne expérience utilisateur (UX)

## Étapes de conception

### 1. Layout principal (app.blade.php)
- [ ] Refaire la navbar avec un design moderne
- [ ] Sidebar collapsible pour mobile
- [ ] Améliorer latypographie
- [ ] Utiliser des couleurs professionnelles cohérentes

### 2. Dashboard (dashboard/index.blade.php)
- [ ] Cartes de statistiques avec icônes
- [ ] Graphiques améliorés
- [ ] Design des tableaux modernisé

### 3. Pages Clients
- [ ] clients/index.blade.php
- [ ] clients/create.blade.php
- [ ] clients/edit.blade.php
- [ ] clients/show.blade.php

### 4. Pages Factures
- [ ] factures/index.blade.php
- [ ] factures/create.blade.php
- [ ] factures/edit.blade.php
- [ ] factures/show.blade.php

### 5. Pages Produits
- [ ] produits/index.blade.php
- [ ] produits/create.blade.php
- [ ] produits/mouvements.blade.php
- [ ] produits/reapprovisionnement.blade.php

### 6. Historique
- [ ] historique/index.blade.php

### 7. Paramètres
- [ ] settings/edit.blade.php

### 8. Login
- [ ] auth/login.blade.php

## Palette de couleurs proposée
- Primaire: #0d6efd (Bleu Bootstrap)
- Secondaire: #6c757d (Gris)
- Success: #198754 (Vert)
- Danger: #dc3545 (Rouge)
- Warning: #ffc107 (Jaune)
- Background: #f8f9fa ( Gris clair)
- Cards: #ffffff (Blanc)

## Typographie
- Font principale: system-ui, -apple-system, sans-serif
- Headings: Roboto ou similaires

## Composants à créer
- Cards avec ombres douces
- Boutons avec hover effects
- Tableaux avec striping et hover
- Formulaires avec labels flottants
- Badges et labels cohérents
