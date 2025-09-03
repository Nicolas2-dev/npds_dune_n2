# Module Npds Contact

# Installation du module

## Description

**Module :** contact (Formulaire de contact (ou autre))

Ce module démontre les possibilités de SFORM :
- Fabrication simple d'un formulaire
- Utilisation des fonctions de NPDS (en l'occurrence send_email)
  
> ℹ️ **Note :** C'est l'email qui est configuré dans les préférences / Section : "Envoyer par E-mail les nouveaux articles à l'administrateur"

## Lancement

Pour lancer le module, utilisez une URL de ce type :

```php
modules.php?ModPath=contact&ModStart=contact
```

## Personnalisation

### 1. Modifier le contenu du formulaire

Le contenu du formulaire est modifiable dans le fichier :
```
modules/contact/support/sform/formulaire.php
```

### 2. Créer d'autres formulaires

Vous pouvez créer d'autres formulaires en :
- Copiant le premier formulaire
- Changeant le nom du formulaire (par ex : `contact2.php`)
- Modifiant le lancement (`ModStart=contact2`)

### 3. Gérer les paramètres via pages.php

Vous pouvez gérer certains paramètres de ce module via `pages.php` :

```php
$PAGES['modules.php?ModPath=ModPath=contact&ModStart=contact*'][title]="[french]Contactez-nous[/french][english]Contact us[/english][chinese]Contact us[/chinese]+|$title+";
$PAGES['modules.php?ModPath=contact&ModStart=contact*'][run]="yes";
$PAGES['modules.php?ModPath=contact&ModStart=contact*'][blocs]="0";
```

---

*Module de démonstration des capacités SFORM*