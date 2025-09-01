<?php

namespace App\Library\Media;


class Base64Image
{

    /**
     * Convertit les images encodées en base64 dans une chaîne HTML
     * en fichiers physiques, puis remplace les balises `<img>` avec
     * le chemin du fichier généré.
     *
     * @param string $base64String Chaîne HTML contenant des images en base64.
     * @param string $outputPath   Chemin du dossier où enregistrer les images.
     * @return string              HTML modifié avec les URLs des fichiers images.
     *
     * @throws RuntimeException    Si une image est invalide ou non supportée.
     */
    public static function dataImageToFileUrl(string $base64String, string $outputPath): string
    {
        $rechdataimage = '#src=\\\"(data:image/[^"]+)\\\"#m';

        preg_match_all($rechdataimage, $base64String, $dataimages);

        $j = 0;
        $timgw = 800;
        $timgh = 600;
        $ra = rand(1, 999);

        foreach ($dataimages[1] as $imagedata) {

            $datatodecode = explode(',', $imagedata);
            $bin = base64_decode($datatodecode[1]);
            $im = imageCreateFromString($bin);

            if (!$im) {
                die('Image non valide');
            }

            $size = getImageSizeFromString($bin);
            $ext = substr($size['mime'], 6);

            if (!in_array($ext, ['png', 'gif', 'jpeg'])) {
                die('Image non supportée');
            }

            $output_file = $outputPath . $j . '_' . $ra . '_' . time() . '.' . $ext;
            $base_64_string = preg_replace($rechdataimage, 'class="img-fluid" src="' . $output_file . '" loading="lazy"', $base64String, 1);

            if ($size[0] > $timgw or $size[1] > $timgh) {
                $timgh = round(($timgw / $size[0]) * $size[1]);
                $timgw = round(($timgh / $size[1]) * $size[0]);

                $th = imagecreatetruecolor($timgw, $timgh);
                imagecopyresampled($th, $im, 0, 0, 0, 0, $timgw, $timgh, $size[0], $size[1]);

                $args = [$th, $output_file];
            } else {
                $args = [$im, $output_file];
            }

            if ($ext == 'png') {
                $args[] = 0;
            } elseif ($ext == 'jpeg') {
                $args[] = 100;
            }

            $fonc = "image{$ext}";

            call_user_func_array($fonc, $args);

            $j++;
        }

        return $base_64_string;
    }
}
