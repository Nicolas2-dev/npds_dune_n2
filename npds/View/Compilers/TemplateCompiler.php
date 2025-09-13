<?php

namespace Npds\View\Compilers;

use Npds\Support\Arr;
use Npds\View\Compilers\Compiler;
use Npds\View\Contracts\CompilerInterface;


class TemplateCompiler extends Compiler implements CompilerInterface
{

    /**
     * Liste des extensions personnalisées enregistrées pour le compilateur.
     *
     * Chaque extension est généralement un callable ou une closure
     * qui permet d'ajouter des règles de compilation.
     *
     * @var array<int, callable>
     */
    protected array $extensions = [];

    /**
     * Chemin du fichier de vue à compiler.
     *
     * @var string|null
     */
    protected ?string $path = null;

    /**
     * Liste des types de compilateurs à appliquer aux vues.
     *
     * @var string[]
     */
    protected array $compilers = [
        'Extensions',
        'Statements',
        'Comments',
        'Echos',
    ];

    /**
     * Délimiteurs pour les expressions échappées automatiquement.
     *
     * Exemple : {{ $variable }}
     *
     * @var array{0: string, 1: string}
     */
    protected array $contentTags = ['{{', '}}'];

    /**
     * Délimiteurs pour les expressions **non échappées** (raw output).
     *
     * Exemple : {{{ $variable }}}
     *
     * @var array{0: string, 1: string}
     */
    protected array $escapedTags = ['{{{', '}}}'];


    /**
     * Lignes de pied de page à ajouter après compilation.
     *
     * @var string[]
     */
    protected array $footer = [];

    /**
     * Placeholder temporaire pour les blocs verbatim.
     *
     * @var string
     */
    protected string $verbatimPlaceholder = '@__verbatim__@';

    /**
     * Blocs verbatim stockés temporairement.
     *
     * @var string[]
     */
    protected array $verbatimBlocks = [];


    /**
     * Compile le fichier de vue donné.
     *
     * @param  string|null  $path Chemin du fichier de vue.
     * @return void
     */
    public function compile(?string $path = null): void
    {
        if (! is_null($path)) {
            $this->setPath($path);
        }

        $contents = $this->compileString($this->files->get($path));

        if ( ! is_null($this->cachePath)) {
            $this->files->put($this->getCompiledPath($this->getPath()), $contents);
        }
    }

    /**
     * Obtenir le chemin du fichier de vue courant.
     *
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Définir le chemin du fichier de vue courant.
     *
     * @param string $path
     * @return void
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * Compile une chaîne de vue en PHP exécutable.
     *
     * @param string $value Contenu de la vue.
     * @return string Contenu compilé.
     */
    public function compileString(string $value): string
    {
        $result = '';

        if (strpos($value, '@verbatim') !== false) {
            $value = $this->storeVerbatimBlocks($value);
        }

        $this->footer = array();

        // Ici, nous allons parcourir tous les jetons renvoyés par le lexer Zend et analyser chacun d'eux dans le PHP valide correspondant.
        // Nous aurons alors ce modèle comme PHP correctement rendu et pouvant être rendu nativement.
        foreach (token_get_all($value) as $token) {
            $result .= is_array($token) ? $this->parseToken($token) : $token;
        }

        if (! empty($this->verbatimBlocks)) {
            $result = $this->restoreVerbatimBlocks($result);
        }

        // Si des lignes de pied de page doivent être ajoutées à un modèle, nous les ajouterons ici à la fin du modèle.
        // Ceci est utilisé principalement pour l'héritage du modèle via le mot-clé extends qui doit être ajouté.
        if (count($this->footer) > 0) {
            $result = ltrim($result, PHP_EOL) .PHP_EOL .implode(PHP_EOL, array_reverse($this->footer));
        }

        return $result;
    }

    /**
     * Stocke les blocs verbatim et les remplace par un placeholder.
     *
     * Cette méthode capture les blocs `@verbatim ... @endverbatim` et les
     * remplace par un placeholder temporaire tout en stockant leur contenu
     * dans `$this->verbatimBlocks`.
     *
     * @param string $value Contenu de la vue à traiter.
     * @return string Contenu avec les blocs verbatim remplacés par le placeholder.
     */
    protected function storeVerbatimBlocks(string $value): string
    {
        return preg_replace_callback('/(?<!@)@verbatim(.*?)@endverbatim/s', function ($matches) {
            $this->verbatimBlocks[] = $matches[1];

            return $this->verbatimPlaceholder;
        }, $value);
    }

    /**
     * Restaure les blocs verbatim dans le contenu compilé.
     *
     * Remplace les placeholders par les blocs verbatim originaux.
     *
     * @param string $result Contenu contenant les placeholders.
     * @return string Contenu final avec les blocs verbatim restaurés.
     */
    protected function restoreVerbatimBlocks(string $result): string
    {
        $result = preg_replace_callback(
            '/' . preg_quote($this->verbatimPlaceholder) . '/',
            function () {
                return array_shift($this->verbatimBlocks);
            },
            $result
        );

        $this->verbatimBlocks = [];

        return $result;
    }

    /**
     * Analyse un token de la vue et compile son contenu si nécessaire.
     *
     * @param array $token Tableau contenant l'ID du token et son contenu.
     *                     Format : [int|string $id, string $content]
     * @return string Contenu compilé du token.
     */
    protected function parseToken(array $token): string
    {
        [$id, $content] = $token;

        if ($id === T_INLINE_HTML) {
            foreach ($this->compilers as $type) {
                $method = 'compile' . $type;

                $content = call_user_func([$this, $method], $content);
            }
        }

        return $content;
    }

    /**
     * Compile toutes les extensions enregistrées sur une chaîne donnée.
     *
     * @param string $value Contenu de la vue à compiler.
     * @return string Contenu compilé après application des extensions.
     */
    protected function compileExtensions(string $value): string
    {
        foreach ($this->extensions as $compiler) {
            $value = call_user_func($compiler, $value, $this);
        }

        return $value;
    }

    /**
     * Compile les commentaires Blade en commentaires PHP.
     *
     * @param string $value Contenu de la vue.
     * @return string Contenu compilé avec commentaires PHP.
     */
    protected function compileComments(string $value): string
    {
        $pattern = sprintf('/%s--((.|\s)*?)--%s/', $this->contentTags[0], $this->contentTags[1]);

        return preg_replace($pattern, '<?php /*$1*/ ?>', $value);
    }

    /**
     * Compile les expressions d'écho Blade en PHP.
     *
     * @param string $value Contenu de la vue.
     * @return string Contenu compilé avec échos PHP.
     */
    protected function compileEchos(string $value): string
    {
        $difference = strlen($this->contentTags[0]) - strlen($this->escapedTags[0]);

        if ($difference > 0) {
            return $this->compileEscapedEchos($this->compileRegularEchos($value));
        }

        return $this->compileRegularEchos($this->compileEscapedEchos($value));
    }

    /**
     * Compile les instructions Blade (@directive).
     *
     * @param string $value Contenu de la vue.
     * @return string Contenu compilé avec directives PHP.
     */
    protected function compileStatements(string $value): string
    {
        $callback = function($match)
        {
            if (method_exists($this, $method = 'compile' .ucfirst($match[1]))) {
                $match[0] = call_user_func(array($this, $method), Arr::get($match, 3));
            }

            return isset($match[3]) ? $match[0] : $match[0] .$match[2];
        };

        return preg_replace_callback('/\B@(\w+)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', $callback, $value);
    }

    /**
     * Compile les échos réguliers {{ ... }}.
     *
     * @param string $value Contenu de la vue.
     * @return string Contenu compilé.
     */
    protected function compileRegularEchos(string $value): string
    {
        $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->contentTags[0], $this->contentTags[1]);

        $callback = function($matches)
        {
            $whitespace = empty($matches[3]) ? '' : $matches[3] .$matches[3];

            return $matches[1] ? substr($matches[0], 1) : '<?php echo ' .$this->compileEchoDefaults($matches[2]) .'; ?>' .$whitespace;
        };

        return preg_replace_callback($pattern, $callback, $value);
    }

    /**
     * Compile les échos échappés {{{ ... }}}.
     *
     * @param string $value Contenu de la vue.
     * @return string Contenu compilé.
     */
    protected function compileEscapedEchos(string $value): string
    {
        $pattern = sprintf('/%s\s*(.+?)\s*%s(\r?\n)?/s', $this->escapedTags[0], $this->escapedTags[1]);

        $callback = function($matches)
        {
            $whitespace = empty($matches[2]) ? '' : $matches[2] .$matches[2];

            return '<?php echo e('.$this->compileEchoDefaults($matches[1]).'); ?>'.$whitespace;
        };

        return preg_replace_callback($pattern, $callback, $value);
    }

    /**
     * Transforme les expressions de type "$var or default" en code PHP valide.
     *
     * @param string $value Expression à transformer.
     * @return string Expression PHP.
     */
    public function compileEchoDefaults(string $value): string
    {
        return preg_replace('/^(?=\$)(.+?)(?:\s+or\s+)(.+?)$/s', 'isset($1) ? $1 : $2', $value);
    }

}
