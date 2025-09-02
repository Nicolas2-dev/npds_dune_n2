<?php

namespace Modules\Upload\Support;


class UploadForm
{

    /* Fonction permettant de créer une checkbox                            */
    public static function getCheckBox($name, $value = 1, $current, $text = '', $cla = ' ')
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

    /* Fonction permettant une liste de choix                               */
    public static function getListBox($name, $items, $selected = '', $multiple = 0, $onChange = '')
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
