# Configuration des Pages (`$PAGES`)

Ce système permet de définir pour chaque script ou module du CMS un ensemble de paramètres :  
- **Titre**  
- **Affichage des blocs (colonnes gauche/droite)**  
- **Exécution (run)**  
- **TinyMCE**  
- **CSS / JS spécifiques**  
- **SEO (sitemap, meta description, meta keywords)**  

---

## 🔹 Syntaxe 1

### Définition d’un titre
```php
$PAGES['index.php']['title'] = "TITRE de la page";
```

- `Votre_titre+` → rajoute le titre de la page devant le titre du site  
- `Votre_titre-` → n’ajoute pas le titre du site  
- `""` ou pas de `+-` → n’affiche que le titre du site  

#### Titre alternatif
```php
$PAGES['index.php']['title'] = "Index du site+|$title-";
```
- Si `$title` **n’est pas vide**, alors `"$title-"` sera utilisé  
- Sinon, `"Index du site+"` sera affiché  
- Le caractère `|` représente un **OU (OR)**  

#### Multi-langue
```php
$PAGES['index.php']['title'] = "[french]Index[/french][english]Home[/english]+";
```
Les titres supportent plusieurs langues.

---

### Affichage des blocs
```php
$PAGES['index.php']['blocs'] = "valeur";
```

Valeurs possibles :  

| Valeur | Effet |
|--------|---------------------------------------------|
| `-1`   | Pas de blocs gauche **et** pas de blocs droite |
| `0`    | Blocs gauche **et** pas de blocs droite *(défaut)* |
| `1`    | Blocs gauche **et** blocs droite |
| `2`    | Pas de blocs gauche **et** blocs droite |
| `3`    | Colonne gauche + Colonne droite + Central |
| `4`    | Central + Colonne gauche + Colonne droite |

⚠️ Cette valeur n’a d’effet que si elle n’est pas définie dans votre thème (`$pdst`).

---

### Exécution du script (`run`)
```php
$PAGES['index.php']['run'] = "yes|no|script";
```

- `""` ou `"yes"` → le script peut s’exécuter  
- `"no"` → redirection vers `index.php` + message *"Site Web fermé"*  
- `"script.php"` → autorise le **reroutage** vers un autre script  

Exemple :
```php
$PAGES['user.php']['run'] = "user2.php";
```

---

### Modules
Deux syntaxes possibles :  

- **Cas par page** :
```php
$PAGES['modules.php?ModPath=links&ModStart=links']['title'] = "...";
```

- **Cas global (toutes les pages du module)** :
```php
$PAGES['modules.php?ModPath=links&ModStart=links*']['title'] = "...";
```
*(le `*` indique que toutes les pages du module sont concernées)*

---

### TinyMCE
```php
$PAGES['index.php']['TinyMce'] = 1; // ou 0
$PAGES['index.php']['TinyMce-theme'] = "full"; // ou "short"
$PAGES['index.php']['TinyMceRelurl'] = "true"; // ou "false"
```

- Active TinyMCE et choisit le thème  
- Définit si TinyMCE génère des chemins relatifs ou absolus  

---

### CSS spécifiques
```php
$PAGES['index.php']['css'] = "css-specifique.css+-";
$PAGES['index.php']['css'] = array(
    "css-specifique.css+-",
    "http://www.exemple.com/css/style.min.css+-"
);
```

- `"nom.css+"` → ajoutée **en plus** de la CSS du thème  
- `"nom.css-"` → remplace la CSS du thème  
- La CSS locale doit être dans `themes/votre_theme/style/`  
- La CSS distante doit être chargée via `http(s)://`  

---

### JS spécifiques
```php
$PAGES['index.php']['js'] = "mon-script.js";
$PAGES['index.php']['js'] = array(
    "mon-script.js",
    "http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"
);
```

- JS local : `themes/votre_theme/js/` ou chemin explicite depuis la racine  
- JS distant : via `http://` ou `https://`  

---

## 🔹 SEO

### Sitemap
```php
$PAGES['index.php']['sitemap'] = "priorité"; // 0.1 à 1
```

- Configure le `sitemap.xml` généré par `sitemap.php`  
- Pour `article.php`, `forum.php`, `sections.php`, `download.php`, le sitemap inclut toutes les données.  

---

### Meta Description
```php
$PAGES['index.php']['meta-description'] = "votre phrase de description";
```

### Meta Keywords
```php
$PAGES['index.php']['meta-keywords'] = "vos mots clefs";
```

---

## 🔹 Syntaxe 2 (filtrage par utilisateur/admin/variable)

Permet de restreindre l’accès en fonction d’un paramètre ou du type d’utilisateur.

Exemple : réserver un forum aux membres uniquement  
```php
$PAGES['forum=1']['title'] = "forum.php";
$PAGES['forum=1']['run']   = "user"; // ou "admin" ou variable
```

- `"user"` → réservé aux utilisateurs connectés  
- `"admin"` → réservé aux administrateurs  
- `"nom_variable"` → contrôle conditionnel basé sur une variable  

⚠️ Ce système ne remplace pas une **gestion de droits complète**, mais rend de nombreux services.
