# Projet Deefy – Mini-projet PHP
**Auteur :** Johan Schaeffer  & Loic Durand
**Date :** 31 octobre 2025  
**IUT Nancy-Charlemagne - BUT Informatique**

---

## - Liens dépot git et webetu
**dépot git :** https://github.com/loicdurand7/ProjetDeefy

**webetu :** https://webetu.iutnc.univ-lorraine.fr/~e35679u/S3D-Durand/ProjetDeefy/Index.php

---

## - Objectif
Application web de gestion de playlists.  
Permet de s’inscrire, se connecter, créer et gérer ses playlists, y ajouter des pistes, etc.

---

## - Structure du projet
- `index.php` → point d’entrée principal  
- `src/` → code source PHP (actions, repository, audio, etc.)  
- `vendor/` → dépendances (Composer)  
- `config.ini` → configuration BD pour y avoir accès

---

## - Fonctionnalités implémentées
| Fonctionnalité | État | Commentaire |
|----------------|------|--------------|
| Inscription / connexion | ✅ | avec `password_hash` |
| Authentification requise | ✅ | via `requireAuth()` |
| Mes playlists (BD) | ✅ | affiche celles de l’utilisateur connecté |
| Création playlist | ✅ | ajoute en BD, devient courante |
| Afficher playlist courante | ✅ | affiche pistes liées |
| Ajouter piste | ✅ | formulaire + INSERT |
| Sécurité XSS / SQL | ✅ | requêtes préparées + htmlspecialchars |

---

## - Démonstration
- URL Webetu : (à remplir) 
- Compte de test :
  - Email : user1@mail.com
  - Mot de passe : user1

---

## - Base de données
→ Via xampp sur PhpMyAdmin 

---
