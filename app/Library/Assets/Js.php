<?php

namespace App\Library\Assets;


class Js
{

    /**
     * Génère un tableau JavaScript à partir d'une requête SQL et implémente un auto-complete pour un champ input.
     *
     * Dépendances : jquery.min.js, jquery-ui.js
     *
     * @param string $jsArrayName Nom du tableau JavaScript qui contiendra les valeurs.
     * @param string $columnName  Nom de la colonne dans la base de données à utiliser pour l'autocomplete.
     * @param string $tableName   Nom de la table dans la base de données.
     * @param string|null $inputId ID de l'élément input sur lequel appliquer l'autocomplete. 
     *                             Si null, la fonction retourne uniquement le tableau JS.
     * @param int|null $cacheTime Durée de cache de la requête en secondes. Optionnel.
     *
     * @return string Retourne le code JavaScript complet à insérer dans la page si $inputId est défini,
     *                sinon retourne uniquement le tableau JavaScript.
     */
    public static function autoComplete(
        string  $jsArrayName,
        string  $columnName,
        string  $tableName,
        ?string $inputId = null,
        ?int    $cacheTime = null
    ) {
        $list_json = '';
        $list_json .= 'var ' . $jsArrayName . ' = [';

        $res = Q_select("SELECT " . $columnName . " 
                         FROM " . sql_prefix($tableName), $cacheTime);

        foreach ($res as $ar_data) {
            foreach ($ar_data as $val_champ) {
                if ($inputId == '') {
                    $list_json .= '"' . base64_encode($val_champ) . '",';
                } else {
                    $list_json .= '"' . $val_champ . '",';
                }
            }
        }

        $list_json = rtrim($list_json, ',');
        $list_json .= '];';

        if ($inputId === null) {
            $scri_js = $list_json;
        } else {
            $scri_js = '<script type="text/javascript">
            //<![CDATA[
                $(function() {
                ' . $list_json;

            if ($inputId !== null) {
                $scri_js .= '$( "#' . $inputId . '" ).autocomplete({
                    source: ' . $jsArrayName . '
                });';
            }

            $scri_js .= '});
                    //]]>
            </script>';
        }

        return $scri_js;
    }

    /**
     * Génère un script d'auto-complétion multiple pour un champ input HTML.
     * 
     * Crée un pseudo-array JSON à partir d'une requête SQL et implémente
     * un auto-complete sur le champ input. Dépendances : jQuery 2.1.3, jQuery UI.
     *
     * @param string $jsArrayName Nom de la variable JavaScript qui contiendra la liste.
     * @param string $columnName Nom de la colonne SQL à utiliser pour les valeurs.
     * @param string $tableName Nom de la table SQL.
     * @param string $inputId ID du champ input sur lequel appliquer l'auto-complete.
     * @param string $sqlCondition Partie WHERE ou autres conditions SQL à ajouter.
     * 
     * @return string Code HTML <script> pour l'auto-complete.
     */
    public static function autoCompleteMulti(
        string  $jsArrayName,
        string  $columnName,
        string  $tableName,
        string  $inputId,
        ?string  $sqlCondition = null
    ): string {
        $list_json = '';
        $list_json .= $jsArrayName . ' = [';

        $query = "SELECT " . $columnName . " 
                  FROM " . sql_prefix($tableName);

        if ($sqlCondition !== null) {
            $query .= ' ' . $sqlCondition;
        }

        $res = sql_query($query);

        while (list($columnName) = sql_fetch_row($res)) {
            $list_json .= '\'' . $columnName . '\',';
        }

        $list_json = rtrim($list_json, ',');
        $list_json .= '];';

        return '<script type="text/javascript">
            //<![CDATA[
                var ' . $jsArrayName . ';
                $(function() {
                    ' . $list_json . '
                    function split( val ) {
                        return val.split( /,\s*/ );
                    }
                    function extractLast( term ) {
                        return split( term ).pop();
                    }
                    $( "#' . $inputId . '" )
                    // dont navigate away from the field on tab when selecting an item
                    .bind( "keydown", function( event ) {
                        if ( event.keyCode === $.ui.keyCode.TAB && $( this ).autocomplete( "instance" ).menu.active ) {
                            event.preventDefault();
                        }
                    })
                    .autocomplete({
                        minLength: 0,
                        source: function( request, response ) {
                            response( $.ui.autocomplete.filter(
                                ' . $jsArrayName . ', extractLast( request.term ) ) );
                        },
                        focus: function() {
                            return false;
                        },
                        select: function( event, ui ) {
                            var terms = split( this.value );
                            terms.pop();
                            terms.push( ui.item.value );
                            terms.push( "" );
                            this.value = terms.join( ", " );
                            return false;
                        }
                    });
                });
            //]]>
        </script>' . "\n";
    }

    /**
     * Génère les paramètres pour ouvrir une fenêtre popup JavaScript personnalisée.
     *
     * Cette fonction retourne une chaîne formatée pour `window.open()`, 
     * avec des options prédéfinies (taille, barres d'outils, scroll, etc.).
     *
     * @param string $url    URL ou chemin de la page à ouvrir
     * @param string $title  Titre de la fenêtre popup (si vide, un titre basé sur le timestamp est utilisé)
     * @param int    $width  Largeur de la fenêtre en pixels
     * @param int    $height Hauteur de la fenêtre en pixels
     *
     * @return string Chaîne de paramètres à passer à `window.open()`
     */
    public static function javaPopup(
        string  $url,
        string  $title,
        int     $width,
        int     $height
    ): string {
        if ($title === '') {
            $title = '@ ' . time() . ' ';
        }

        return "'$url', '$title', 'menubar=no,location=no,directories=no,status=no,copyhistory=no,height=$height,width=$width,toolbar=no,scrollbars=yes,resizable=yes'";
    }
}
