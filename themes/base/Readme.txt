
# Documentation des fonctions du thème

## local_var($Xcontent)
Extrait une variable spéciale définie dans le texte avec le format !var!VariableName.

**Paramètres :**  
- $Xcontent (string) : Le texte contenant potentiellement la variable.

**Retour :**  
- (string|void) : Le nom de la variable extraite, ou rien si aucune variable trouvée.

**Exemple :**  
local_var("Hello !var!MyVar world") // retourne "MyVar"

## themeindex($aid, $informant, $time, $title, $counter, $topic, $thetext, $notes, $morelink, $topicname, $topicimage, $topictext, $id)
Génère l’affichage d’un article sur la page d’index en utilisant le template HTML du thème.

**Paramètres :**  
- $aid : ID du publicateur  
- $informant : Nom de l’émetteur  
- $time : Timestamp de publication  
- $title : Titre de l’article  
- $counter : Nombre de lectures  
- $topic : ID du topic  
- $thetext : Contenu de l’article  
- $notes : Notes éventuelles  
- $morelink : Tableau contenant les informations pour “Lire la suite”, commentaire, catégorie  
- $topicname : Nom du topic  
- $topicimage : Image du topic  
- $topictext : Texte descriptif du topic  
- $id : ID de l’article

**Retour :**  
- Affiche directement le rendu HTML.

**Fonctionnalités :**  
- Extraction des variables !var!  
- Construction des liens “Lire la suite” et “Commentaires”  
- Intégration d’une image ou badge pour le topic  
- Remplacement des métamots !N_*!  

## themearticle($aid, $informant, $time, $title, $thetext, $topic, $topicname, $topicimage, $topictext, $id, $previous_sid, $next_sid, $archive)
Génère l’affichage détaillé d’un article individuel.

**Paramètres :**  
- $aid : ID du publicateur  
- $informant : Nom de l’émetteur  
- $time : Timestamp de publication  
- $title : Titre de l’article  
- $thetext : Contenu de l’article  
- $topic : ID du topic  
- $topicname : Nom du topic  
- $topicimage : Image du topic  
- $topictext : Texte descriptif du topic  
- $id : ID de l’article  
- $previous_sid : ID de l’article précédent  
- $next_sid : ID de l’article suivant  
- $archive : Indicateur d’archive

**Retour :**  
- Affiche directement le rendu HTML détaillé.

**Fonctionnalités :**  
- Extraction des variables !var!  
- Génération des liens “Précédent” et “Suivant”  
- Ajout des boutons d’impression et d’envoi à un ami  
- Intégration du sujet avec image  
- Remplacement des métamots !N_*!  

## themesidebox($title, $content)
Génère un bloc latéral (sidebar) selon le thème et la position (gauche/droite).

**Paramètres :**  
- $title : Titre du bloc  
- $content : Contenu HTML ou texte du bloc

**Retour :**  
- Affiche directement le rendu HTML du bloc

**Fonctionnalités :**  
- Détection du template correct en fonction du thème (bloc-right.html, bloc-left.html, bloc.html)  
- Intégration du titre et du contenu dans le template  

## themefootbox($content)
Génère un bloc dans le pied de page.

**Paramètres :**  
- $content : Contenu HTML ou texte du bloc

**Retour :**  
- Affiche directement le rendu HTML

**Fonctionnalités :**  
- Utilise le template bloc-foot.html ou celui du thème par défaut  

## themesidebox_adv($title, $content, $htvar = '')
Version avancée de themesidebox permettant de passer une variable de type htvar.

**Paramètres :**  
- $title : Titre du bloc  
- $content : Contenu HTML ou texte  
- $htvar (optionnel) : Variable supplémentaire pour le template

**Retour :**  
- Affiche directement le rendu HTML

**Fonctionnalités :**  
- Détection du template correct selon le thème  
- Possibilité d’utiliser $htvar dans le template  

## Notes générales
- Les fonctions themeindex et themearticle utilisent un système de métalang (!N_!*) pour remplacer dynamiquement les balises dans les templates.  
- Les templates sont recherchés dans themes/<theme>/html/ et sinon dans themes/default/html/.  
- Dépendances : theme_image, translate, formatTimes, getPartOfTime, userpopover, meta_lang, aff_langue.
