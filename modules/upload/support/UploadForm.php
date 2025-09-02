<?php

namespace Modules\Upload\Support;


class UploadForm
{

    /**
     * Crée une checkbox HTML.
     *
     * @param string $name Nom de l'input
     * @param mixed $value Valeur de l'input (par défaut 1)
     * @param mixed $current Valeur actuelle pour déterminer si cochée
     * @param string $text Texte affiché à côté de la checkbox
     * @param string $cla Classes CSS supplémentaires
     * @return string HTML de la checkbox
     */
    public static function getCheckBox(string $name, mixed $value = 1, mixed $current, string $text = '', string $cla = ' '): string
    {
        $p =  sprintf(
            '<input class="form-check-input ' . $cla . '" type="checkbox" name="%s" value="%s"%s />%s',
            $name,
            $value,
            ("$current" == "$value") ? ' checked="checked"' : '',
            (empty($text)) ? '' : " $text"
        );

        return $p;
    } 

    /**
     * Crée une liste déroulante HTML (select).
     *
     * @param string $name Nom de l'élément select
     * @param array $items Tableau clé => valeur pour les options
     * @param mixed $selected Valeur sélectionnée par défaut
     * @param int $multiple Si 1, active la sélection multiple
     * @param string $onChange Code JS pour l'événement onchange
     * @return string HTML du select
     */
    public static function getListBox(string $name, array $items, mixed $selected = '', int $multiple = 0, string $onChange = ''): string
    {
        $oc = empty($onChange) ? '' : ' onchange="' . $onChange . '"';

        $p = sprintf(
            '<select class="form-select form-select-sm mx-auto" name="%s%s"%s%s>',
            $name,
            ($multiple == 1) ? '[]' : '',
            ($multiple == 1) ? ' multiple' : '',
            $oc
        );

        if (is_array($items)) {
            foreach ($items as $k => $v) {
                $p .= sprintf('<option value="%s"%s>%s</option>', $k, strcmp($selected, $k) ? '' : ' selected="selected"', $v);
            }
        }

        return $p . '</select>';
    }

}
