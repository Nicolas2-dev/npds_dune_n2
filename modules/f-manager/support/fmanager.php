<?php

// Gestion Ascii étendue
function extend_ascii($ibid)
{
    $tmp = urlencode($ibid);

    $tmp = str_replace('%82', 'È', $tmp);
    $tmp = str_replace('%85', '‡', $tmp);
    $tmp = str_replace('%87', 'Á', $tmp);
    $tmp = str_replace('%88', 'Í', $tmp);
    $tmp = str_replace('%97', '˘', $tmp);
    $tmp = str_replace('%8A', 'Ë', $tmp);

    $tmp = urldecode($tmp);

    return $tmp;
}

// Gestion des fichiers autorisés
function fma_filter($type, $filename, $Extension)
{
    $autorise = false;

    $error = '';

    if ($type == 'f') {
        $filename = removeHack($filename);
    }

    $filename = preg_replace('#[/\\\:\*\?"<>|]#i', '', rawurldecode($filename));
    $filename = str_replace('..', '', $filename);

    // Liste des extensions autorisées
    $suffix = strtoLower(substr(strrchr($filename, '.'), 1));

    if (($suffix != '') or ($type == 'd')) {
        if ((in_array($suffix, $Extension)) or ($Extension[0] == '*') or $type == 'd') {

            // Fichiers interdits en fonction de qui est connecté
            if (fma_autorise($type, $filename)) {
                $autorise = true;
            } else {
                $error = fma_translate('Fichier interdit');
            }
        } else {
            $error = fma_translate('Type de fichier interdit');
        }
    } else {
        $error = fma_translate('Fichier interdit');
    }

    $tab[] = $autorise;
    $tab[] = $error;
    $tab[] = $filename;

    return $tab;
}

// Gestion des autorisations sur les répertoires et les fichiers
function fma_autorise($type, $dir)
{
    global $user, $admin, $dirlimit_fma, $ficlimit_fma, $access_fma, $dir_minuscptr, $fic_minuscptr;

    $autorise_arbo = false;

    if ($type == 'a') {
        $autorise_arbo = $access_fma;
    }

    if ($type == 'd') {
        if (is_array($dirlimit_fma)) {
            if (array_key_exists($dir, $dirlimit_fma)) {
                $autorise_arbo = $dirlimit_fma[$dir];
            }
        }
    }

    if ($type == 'f') {
        if (is_array($ficlimit_fma)) {
            if (array_key_exists($dir, $ficlimit_fma)) {
                $autorise_arbo = $ficlimit_fma[$dir];
            }
        }
    }

    if ($autorise_arbo) {
        $auto_dir = '';

        if (($autorise_arbo == 'membre')
            && ($user)
        ) {
            $auto_dir = true;
        } elseif (($autorise_arbo == 'anonyme')
            && (!$user)
        ) {
            $auto_dir = true;
        } elseif (($autorise_arbo == 'admin')
            && ($admin)
        ) {
            $auto_dir = true;
        } elseif (($autorise_arbo != 'membre')
            && ($autorise_arbo != 'anonyme')
            && ($autorise_arbo != 'admin')
            && ($user)
        ) {

            $tab_groupe = validGroup($user);

            if ($tab_groupe) {
                foreach ($tab_groupe as $groupevalue) {
                    $tab_auto = explode(',', $autorise_arbo);

                    foreach ($tab_auto as $gp) {
                        if ($gp > 0) {
                            if ($groupevalue == $gp) {
                                $auto_dir = true;
                                break;
                            }
                        } else {
                            $auto_dir = true;

                            if (-$groupevalue == $gp) {
                                $auto_dir = false;
                                break;
                            }
                        }
                    }

                    if ($auto_dir) {
                        break;
                    }
                }
            }
        }
    } else {
        $auto_dir = true;
    }

    if ($auto_dir != true) {
        if ($type == 'd') {
            $dir_minuscptr++;
        }

        if ($type == 'f') {
            $fic_minuscptr++;
        }
    }

    return $auto_dir;
}

function chmod_pres($ibid, $champ)
{
    $options = [
        '400' => 'r--------',
        '444' => 'r-x------',
        '500' => 'r--------',
        '544' => 'r-xr--r--',
        '600' => 'rw-------',
        '644' => 'rw-r--r--',
        '655' => 'rw-r-xr-x',
        '666' => 'rw-rw-rw-',
        '700' => 'rwx------',
        '744' => 'rwxr--r--',
        '755' => 'rwxr-xr-x',
        '766' => 'rwxrw-rw-',
        '770' => 'rwxrwx---',
        '777' => 'rwxrwxrwx'
    ];

    $chmod = '';

    $current_value = isset($ibid[0]) ? $ibid[0] : '';

    foreach ($options as $value => $description) {
        $selected = ($current_value == $value) ? ' selected="selected"' : '';

        $chmod .= '<option value="' . $value . '"' . $selected . '> ' . $value . ' (' . $description . ')</option>';
    }

    return $chmod;
}
