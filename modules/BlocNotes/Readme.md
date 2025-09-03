# Module Npds Bloc-notes

- `param5` : classe(s) pour la zone de saisie (vide par défaut)

---

## Bloc-notes PARTAGÉS (SHARED)

Un bloc-notes "shared" est accessible sur l'ensemble du site. Il existe deux formes :

### Bloc-notes partagé nommé
```
params#shared,nom_du_bloc_note
```
Le même bloc-notes est partagé par tous ceux qui respectent les contraintes du BLOC NPDS.

### Bloc-notes partagé par utilisateur
```
params#shared,$username
```
Le bloc-notes est affecté à un admin ou membre en fonction de son pseudo.

---

## Bloc-notes CONTEXTUEL (CONTEXT)

Ce type de bloc-notes **DOIT NÉCESSAIREMENT** être :
- ✅ **DÉFINI dans un bloc de DROITE**
- ✅ **NON ACTIF**
- ✅ **Temps de cache à ZÉRO**

### Bloc-notes contextuel nommé
```
params#context,nom_du_bloc_note
```
Le même bloc-notes est partagé par tous ceux qui respectent les contraintes du BLOC NPDS.

### Bloc-notes contextuel par utilisateur
```
params#context,$username
```
Le bloc-notes est affecté à un admin ou membre en fonction de son pseudo.

### Appel du bloc-notes contextuel

L'appel se fait par un **meta-mot** : `!blocnote!ID`

- Utilisable partout où les meta-mots sont opérationnels
- `ID` = ID du bloc de DROITE défini de type `CONTEXT`
- Un BLOC NPDS de ce type peut servir à l'ensemble du site car il est lié au contexte d'exécution (URI)
- Il faut choisir où mettre son bloc-notes car en fonction de l'URI, son contenu sera différent

---

## Exemples

### Bloc-notes partagé et nommé
```
include#modules/bloc-notes/blocks/bloc-notes.php
function#blocnotes
params#shared,TNT
```

### Bloc-notes partagé et associé à un utilisateur/admin
```
include#modules/bloc-notes/blocks/bloc-notes.php
function#blocnotes
params#shared,$username,,8,bg-danger
```

### Bloc-notes contextuel et nommé
```
include#modules/bloc-notes/blocks/bloc-notes.php
function#blocnotes
params#context,NPDS
```

### Bloc-notes contextuel et associé à un utilisateur/admin
```
include#modules/bloc-notes/blocks/bloc-notes.php
function#blocnotes
params#context,$username,,8,bg-light
```

---

*Module Bloc-notes v1.2 - Par Dev sur la base d'un script de alat (Arnaud Latourette)*