markdown# Documentation des fonctions de thème

## Vue d'ensemble

Ce fichier contient les fonctions principales pour la gestion des thèmes et l'affichage du contenu dans un CMS PHP. Ces fonctions permettent de traiter et d'afficher les articles, blocs latéraux, éditoriaux et informations utilisateur avec un système de templates HTML.

---

## Fonctions disponibles

### `local_var($Xcontent)`

**Description :** Extrait une variable locale depuis le contenu en cherchant le pattern `!var!`.

**Paramètres :**

- `$Xcontent` (string) : Le contenu dans lequel chercher la variable

**Retour :** `string` - Le nom de la variable trouvée ou `null`

**Utilisation :**

```php
$content = "Ceci est un texte avec !var!mavariable et du contenu";
$variable = local_var($content);
// Résultat : "mavariable"
```

---

### `themeindex($aid, $informant, $time, $title, $counter, $topic, $thetext, $notes, $morelink, $topicname, $topicimage, $topictext, $id)`

**Description :** Affiche un article dans la page d'accueil avec le template `index-news.html`.

**Paramètres :**

- `$aid` (string) : ID de l'auteur/publicateur
- `$informant` (string) : Nom de l'émetteur
- `$time` (timestamp) : Date/heure de publication
- `$title` (string) : Titre de l'article
- `$counter` (int) : Nombre de lectures
- `$topic` (int) : ID du sujet/catégorie
- `$thetext` (string) : Contenu de l'article
- `$notes` (string) : Notes additionnelles
- `$morelink` (array) : Liens supplémentaires [caractères, lien_suite, nb_commentaires, lien_commentaires, print, friend, catégorie]
- `$topicname` (string) : Nom du sujet
- `$topicimage` (string) : Image du sujet
- `$topictext` (string) : Description du sujet
- `$id` (int) : ID de l'article

**Variables de template disponibles :**

- `!N_publicateur!` : Nom du publicateur
- `!N_emetteur!` : Émetteur avec popover et lien profil
- `!N_date!` : Date complète formatée
- `!N_date_y!` : Année
- `!N_date_m!` : Mois
- `!N_date_d!` : Jour
- `!N_date_h!` : Heure
- `!N_titre!` : Titre de l'article
- `!N_texte!` : Contenu de l'article
- `!N_sujet!` : Image/badge du sujet avec lien recherche
- `!N_suite!` : Liens "Lire la suite", commentaires, etc.

**Utilisation :**

```php
$morelink = [120, "Lire la suite", 5, "commentaires", "print", "friend", "Tech"];
themeindex(
    "admin",
    "john_doe",
    time(),
    "Mon titre d'article",
    150,
    1,
    "Contenu de l'article...",
    "Note importante",
    $morelink,
    "Technologie",
    "tech.png",
    "Articles sur la technologie",
    123
);
```

---

### `themearticle($aid, $informant, $time, $title, $thetext, $topic, $topicname, $topicimage, $topictext, $id, $previous_sid, $next_sid, $archive)`

**Description :** Affiche un article complet avec le template `detail-news.html`.

**Paramètres :**

- Paramètres identiques à `themeindex` plus :
- `$previous_sid` (int) : ID de l'article précédent
- `$next_sid` (int) : ID de l'article suivant
- `$archive` (int) : Indicateur d'archive

**Variables de template supplémentaires :**

- `!N_previous_article!` : Lien vers l'article précédent
- `!N_next_article!` : Lien vers l'article suivant
- `!N_print!` : Lien d'impression
- `!N_friend!` : Lien d'envoi à un ami
- `!N_boxrel_title!` : Titre des boîtes associées
- `!N_boxrel_stuff!` : Contenu des boîtes associées

**Utilisation :**

```php
themearticle(
    "admin",
    "john_doe",
    time(),
    "Article détaillé",
    "Contenu complet...",
    1,
    "Technologie",
    "tech.png",
    "Articles tech",
    123,
    122,  // Article précédent
    124,  // Article suivant
    0     // Pas d'archive
);
```

---

### `themesidebox($title, $content)`

**Description :** Affiche un bloc latéral avec les templates `bloc-left.html`, `bloc-right.html` ou `bloc.html`.

**Paramètres :**

- `$title` (string) : Titre du bloc (utiliser "no-title" pour masquer)
- `$content` (string) : Contenu HTML du bloc

**Variables de template :**

- `!B_title!` : Titre du bloc
- `!B_content!` : Contenu du bloc
- `!B_class_title!` : Classe CSS pour le titre
- `!B_class_content!` : Classe CSS pour le contenu

**Utilisation :**

```php
// Bloc avec titre
themesidebox("Menu Navigation", "<ul><li>Accueil</li><li>Articles</li></ul>");

// Bloc sans titre
themesidebox("no-title", "<div>Contenu sans titre</div>");
```

---

### `themedito($content)`

**Description :** Affiche un éditorial avec le template `editorial.html`.

**Paramètres :**

- `$content` (string) : Contenu de l'éditorial

**Variables de template :**

- `!editorial_content!` : Contenu de l'éditorial

**Retour :** `string` - Chemin du fichier template utilisé

**Utilisation :**

```php
$template_used = themedito("<p>Message éditorial important...</p>");
```

---

### `userpopover($who, $dim, $avpop)`

**Description :** Génère un avatar utilisateur avec popover d'informations.

**Paramètres :**

- `$who` (string) : Nom d'utilisateur
- `$dim` (int) : Dimension de l'avatar (définit la classe CSS n-ava-{dim})
- `$avpop` (int) : Type d'affichage
  - `1` : Avatar simple
  - `2` : Avatar avec popover interactif

**Fonctionnalités du popover :**

- Profil utilisateur
- Envoi de message interne
- Email (si autorisé)
- Localisation géographique
- Site web personnel
- Minisite
- Réseaux sociaux

**Utilisation :**

```php
// Avatar simple 40px
echo userpopover("john_doe", 40, 1);

// Avatar avec popover 64px
echo userpopover("john_doe", 64, 2);
```

---

## Variables globales utilisées

- `$theme` : Nom du thème actuel
- `$tipath` : Chemin vers les images de sujets
- `$nuke_url` : URL de base du site
- `$user` : Informations de l'utilisateur connecté
- `$short_user` : Mode utilisateur simplifié
- `$NPDS_Prefix` : Préfixe des tables de base de données

---

## Fichiers de templates requis

### Pour les articles :

- `themes/{theme}/html/index-news.html` ou `themes/default/html/index-news.html`
- `themes/{theme}/html/detail-news.html` ou `themes/default/html/detail-news.html`

### Pour les blocs :

- `themes/{theme}/html/bloc-right.html` (bloc droit)
- `themes/{theme}/html/bloc-left.html` (bloc gauche)
- `themes/{theme}/html/bloc.html` (bloc générique)
- `themes/default/html/bloc.html` (fallback)

### Pour l'éditorial :

- `themes/{theme}/html/editorial.html` ou `themes/default/html/editorial.html`

---

## Notes importantes

1. **Sécurité :** Les fonctions utilisent `preg_replace()` pour le remplacement des variables de template
2. **Fallback :** Si un template n'existe pas dans le thème, le système utilise le thème par défaut
3. **Multilangue :** Support des fonctions `translate()` et `affLangue()`
4. **Cache :** Utilisation d'`ob_start()` et `ob_get_contents()` pour la gestion des templates
5. **Permissions :** Certaines fonctionnalités dépendent des droits utilisateur (`autorisation()`)

---

## Exemple complet d'utilisation

```php
// Affichage d'un article sur la page d'accueil
$morelink = [
    250,           // Nombre de caractères
    "Lire plus",   // Texte du lien "lire la suite"
    3,             // Nombre de commentaires
    "commentaires", // Texte des commentaires
    "Imprimer",    // Texte impression
    "Partager",    // Texte partage ami
    "Actualités"   // Catégorie
];

themeindex(
    "editeur1",
    "redacteur_chef",
    time(),
    "Nouvelle fonctionnalité disponible",
    75,
    2,
    "Nous sommes heureux d'annoncer... !var!highlight",
    "Mise à jour importante",
    $morelink,
    "Développement",
    "dev.png",
    "Articles sur le développement",
    456
);

// Affichage d'un bloc latéral
themesidebox(
    "Derniers articles",
    "<ul><li><a href='#'>Article 1</a></li><li><a href='#'>Article 2</a></li></ul>"
);
```
