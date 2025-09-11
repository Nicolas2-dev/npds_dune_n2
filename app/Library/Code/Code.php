<?php

namespace App\Library\Code;


class Code
{

    /**
     * Instance singleton du dispatcher.
     *
     * @var self|null
     */
    protected static ?self $instance = null;

    /**
     * Retourne l'instance singleton du dispatcher.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }

    /**
     * Convertit les pseudo-balises [code]...[/code] et leur contenu en HTML.
     *
     * Cette fonction est utilisée comme callback pour `preg_replace_callback()`.
     * Elle échappe le contenu du code pour l'afficher correctement dans le HTML.
     *
     * @param array $matches Tableau contenant les parties capturées par l'expression régulière.
     *                       Indexs utilisés : 
     *                       - [2] : nom de la balise HTML (ex: pre, code)
     *                       - [3] : langage du code (ex: php, js)
     *                       - [5] : contenu du code
     * @return string HTML généré pour le bloc de code
     */
    public function changeCode(array $matches): string
    {
        return '<' . $matches[2] . ' class="language-' . $matches[3] . '">' .
            htmlentities($matches[5], ENT_COMPAT | ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8') .
            '</' . $matches[2] . '>';
    }

    /**
     * Analyse le contenu d'une chaîne et convertit les pseudo-balises [code]...[/code]
     * et leur contenu en HTML.
     *
     * @param string $content Chaîne à analyser
     * @param bool|null $convertNewlines Indique si les sauts de ligne doivent être convertis en <br />. Par défaut false.
     * @return string Chaîne transformée avec les balises [code] converties en HTML
     */
    public function afCode(string $content, ?bool $convertNewlines = false): string
    {
        $pattern = '#(\[)(\w+)\s+([^\]]*)(\])(.*?)\1/\2\4#s';

        $content = preg_replace_callback($pattern, [self::class, 'changeCode'], $content, -1, $count);

        if ($convertNewlines) {
            $content = nl2br($content);
        }

        return $content;
    }

    /**
     * Convertit le contenu HTML des balises <code class="language-...">...</code>
     * en pseudo-balises [code]...[/code].
     *
     * @param string $content Contenu à analyser
     * @return string Contenu avec les balises HTML converties en pseudo-balises
     */
    public function desafCode(string $content): string
    {
        $pattern = '#(<)(\w+)\s+(class="language-)([^">]*)(">)(.*?)\1/\2>#';

        $content = preg_replace_callback(
            $pattern,
            function (array $matches): string {
                return '[' . $matches[2] . ' ' . $matches[4] . ']' . $matches[6] . '[/' . $matches[2] . ']';
            },
            $content
        );

        return $content;
    }

    /**
     * Analyse le contenu d'une chaîne et convertit les balises [code]...[/code]
     * en code HTML syntaxé via `highlight_string`.
     *
     * @param string $content Contenu à analyser
     * @return string Contenu avec les balises [code] remplacées par du HTML coloré
     */
    public function affCode(string $content): string
    {
        $pasfin = true;

        while ($pasfin) {
            $pos_deb = strpos($content, '[code]', 0);
            $pos_fin = strpos($content, '[/code]', 0);

            // ne pas confondre la position ZERO et NON TROUVE !
            if ($pos_deb === false) {
                $pos_deb = -1;
            }

            if ($pos_fin === false) {
                $pos_fin = -1;
            }

            if (($pos_deb >= 0) and ($pos_fin >= 0)) {
                ob_start();
                highlight_string(substr($content, $pos_deb + 6, ($pos_fin - $pos_deb - 6)));
                $fragment = ob_get_contents();
                ob_end_clean();

                $content = str_replace(substr($content, $pos_deb, ($pos_fin - $pos_deb + 7)), $fragment, $content);
            } else {
                $pasfin = false;
            }
        }

        return $content;
    }
}
