<?php

// Gestion Ascii étendue
function extend_ascii($ibid)
{
    $tmp = urlencode($ibid);

    $tmp = str_replace("%82", "È", $tmp);
    $tmp = str_replace("%85", "‡", $tmp);
    $tmp = str_replace("%87", "Á", $tmp);
    $tmp = str_replace("%88", "Í", $tmp);
    $tmp = str_replace("%97", "˘", $tmp);
    $tmp = str_replace("%8A", "Ë", $tmp);

    $tmp = urldecode($tmp);

    return $tmp;
}

// Gestion des fichiers autorisés
function fma_filter($type, $filename, $Extension)
{
    $autorise = false;
    $error = '';

    if ($type == 'f') {
        $filename = Hack::removeHack($filename);
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
        if (isset($dirlimit_fma) and array_key_exists($dir, $dirlimit_fma)) {
            $autorise_arbo = $dirlimit_fma[$dir];
        }
    }

    if ($type == 'f') {
        if (array_key_exists($dir, $ficlimit_fma)) {
            $autorise_arbo = $ficlimit_fma[$dir];
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
            $tab_groupe = Groupe::validGroup($user);

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

function imagesize($name, $Max_thumb)
{
    $size = getimagesize($name);

    $h_i = $size[1]; //hauteur
    $w_i = $size[0]; //largeur

    if (($h_i > $Max_thumb) || ($w_i > $Max_thumb)) {
        if ($h_i > $w_i) {
            $convert = $Max_thumb / $h_i;
            $h_i = $Max_thumb;
            $w_i = ceil($w_i * $convert);
        } else {
            $convert = $Max_thumb / $w_i;
            $w_i = $Max_thumb;
            $h_i = ceil($h_i * $convert);
        }
    }

    $s_img['hauteur'][0] = $h_i;
    $s_img['hauteur'][1] = $size[1];
    $s_img['largeur'][0] = $w_i;
    $s_img['largeur'][1] = $size[0];

    return $s_img;
}
function CreateThumb($Image, $Source, $Destination, $Max, $ext)
{
    switch ($ext) {

        case (preg_match('/jpeg|jpg/i', $ext) ? true : false):
            if (function_exists('imagecreatefromjpeg')) {
                $src = @imagecreatefromjpeg($Source . $Image);
            }
            break;

        case (preg_match('/gif/i', $ext) ? true : false):
            if (function_exists('imagecreatefromgif')) {
                $src = @imagecreatefromgif($Source . $Image);
            }
            break;

        case (preg_match('/png/i', $ext) ? true : false):
            if (function_exists('imagecreatefrompng')) {
                $src = @imagecreatefrompng($Source . $Image);
            }
            break;
    }

    $size = imagesize($Source . '/' . $Image, $Max);

    $h_i = $size['hauteur'][0]; //hauteur
    $w_i = $size['largeur'][0]; //largeur

    if ($src) {
        if (function_exists('imagecreatetruecolor')) {
            $im = @imagecreatetruecolor($w_i, $h_i);
        } else {
            $im = @imagecreate($w_i, $h_i);
        }

        @imagecopyresized($im, $src, 0, 0, 0, 0, $w_i, $h_i, $size['largeur'][1], $size['hauteur'][1]);
        @imageinterlace($im, 1);

        switch ($ext) {

            case (preg_match('/jpeg|jpg/i', $ext) ? true : false):
                @imagejpeg($im, $Destination . $Image, 100);
                break;

            case (preg_match('/gif/i', $ext) ? true : false):
                @imagegif($im, $Destination . $Image);
                break;

            case (preg_match('/png/i', $ext) ? true : false):
                @imagepng($im, $Destination . $Image, 6);
                break;
        }

        @chmod($Destination . $Image, 0766);

        $size['gene-img'][0] = true;
    }

    return $size;
}
