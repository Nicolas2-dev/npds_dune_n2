# Utilisation de TinyMCE (mode Markdown)

Une fois TinyMCE initialisé avec le plugin **Markdown**, tu as accès à un ensemble de fonctionnalités et d’APIs pour manipuler du contenu en Markdown.

---

## 🔹 Initialisation de base

```js
tinymce.init({
  selector: '#editor',
  plugins: 'markdown code',
  toolbar: 'undo redo | bold italic | code',
});
```

---

## 🔹 Fonctions principales disponibles

### 1. Récupérer le contenu en Markdown

```js
const editor = tinymce.get('editor');
const markdownContent = editor.plugins.markdown.getContent();
console.log(markdownContent);
```

👉 Retourne le contenu de l’éditeur **en Markdown**.

---

### 2. Insérer du Markdown dans l’éditeur

```js
const editor = tinymce.get('editor');
editor.execCommand('MarkdownInsert', false, '# Nouveau titre\n\nMon contenu **Markdown**');
```

👉 Ajoute directement du Markdown à l’endroit du curseur.

---

### 3. Conversion automatique au collage

- **Ctrl + Shift + V** (Windows/Linux) ou **Cmd + Shift + V** (Mac)  
- TinyMCE détecte le **Markdown brut** collé et le convertit immédiatement en HTML.  

Exemple : coller  
```markdown
# Titre  
- Élément 1  
- Élément 2  
```
Résulte en une liste formatée dans l’éditeur.

---

### 4. Définir des symboles Markdown personnalisés

Option `markdown_symbols` :

```js
tinymce.init({
  selector: '#editor',
  plugins: 'markdown',
  markdown_symbols: {
    C: '©',
    TM: '™',
    R: '®',
    smile: '😊'
  }
});
```

👉 Lorsque tu tapes `(C)` → `©`, `(TM)` → `™`, etc.

---

### 5. Exporter en Markdown via bouton personnalisé

```js
tinymce.init({
  selector: '#editor',
  plugins: 'markdown',
  toolbar: 'exportMarkdown',
  setup: (editor) => {
    editor.ui.registry.addButton('exportMarkdown', {
      text: 'Exporter en MD',
      onAction: () => {
        const md = editor.plugins.markdown.getContent();
        alert("Contenu en Markdown :\n\n" + md);
      }
    });
  }
});
```

👉 Ajoute un bouton qui permet d’exporter le contenu en Markdown.

---

### 6. Détecter les événements liés à l’édition

Tu peux brancher des événements TinyMCE comme d’habitude :

```js
editor.on('change', function () {
  console.log("Contenu Markdown :", editor.plugins.markdown.getContent());
});
```

---

## 🔹 Résumé des commandes utiles

| Commande / API | Description |
|----------------|-------------|
| `editor.plugins.markdown.getContent()` | Récupère le contenu en **Markdown** |
| `editor.execCommand('MarkdownInsert', false, text)` | Insère du Markdown dans l’éditeur |
| `markdown_symbols` | Définit des substitutions automatiques (ex: `(C)` → ©) |
| Collage Markdown (Ctrl+Shift+V / Cmd+Shift+V) | Convertit du Markdown brut collé en HTML |
| `editor.on('change')` + `getContent()` | Permet de suivre l’évolution du contenu en Markdown |

---

## 🔹 Exemple complet

```html
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <script src="https://cdn.tiny.cloud/1/TON_API_KEY/tinymce/8/tinymce.min.js" referrerpolicy="origin"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      tinymce.init({
        selector: '#editor',
        plugins: 'markdown code',
        toolbar: 'undo redo | bold italic | code | exportMarkdown',
        markdown_symbols: { C: '©', TM: '™' },
        setup: (editor) => {
          editor.ui.registry.addButton('exportMarkdown', {
            text: 'Exporter en MD',
            onAction: () => {
              const md = editor.plugins.markdown.getContent();
              console.log("Markdown :", md);
              alert(md);
            }
          });
        }
      });
    });
  </script>
</head>
<body>
  <textarea id="editor"># Bienvenue 👋</textarea>
</body>
</html>
```

---

## 🔹 Cas d’usage

- **Saisie en WYSIWYG, export en Markdown** (blogs, CMS, wiki)  
- **Importer du Markdown brut** (copy/paste, conversion auto)  
- **Stocker du contenu en Markdown** mais permettre à l’utilisateur d’éditer avec un vrai éditeur visuel  

