<?php

namespace App\Library\Media;

use App\Library\Theme\Theme;


class Smilies
{

    /**
     * Transforme les codes texte de smilies en images.
     *
     * @param string $message Message contenant des codes de smilies (ex: :-)
     * @return string Message avec les smilies remplacés par des <img>
     */
    public static function smilie(string $message): string
    {
        // Tranforme un :-) en IMG
        global $theme;

        if ($ibid = Theme::themeImage('forum/smilies/smilies.php')) {
            $imgtmp = 'themes/' . $theme . '/assets/images/forum/smilies/';
        } else {
            $imgtmp = 'assets/images/forum/smilies/';
        }

        if (file_exists($imgtmp . 'smilies.php')) {

            include($imgtmp . 'smilies.php');

            foreach ($smilies as $tab_smilies) {
                $suffix = strtoLower(substr(strrchr($tab_smilies[1], '.'), 1));

                if (($suffix == 'gif') or ($suffix == 'png')) {
                    $message = str_replace($tab_smilies[0], "<img class='n-smil' src='" . $imgtmp . $tab_smilies[1] . "' loading='lazy' />", $message);
                } else {
                    $message = str_replace($tab_smilies[0], $tab_smilies[1], $message);
                }
            }
        }

        if ($ibid = Theme::themeImage('forum/smilies/more/smilies.php')) {
            $imgtmp = 'themes/' . $theme . '/assets/images/forum/smilies/more/';
        } else {
            $imgtmp = 'assets/images/forum/smilies/more/';
        }

        if (file_exists($imgtmp . 'smilies.php')) {
            include($imgtmp . 'smilies.php');

            foreach ($smilies as $tab_smilies) {
                $message = str_replace($tab_smilies[0], "<img class='n-smil' src='" . $imgtmp . $tab_smilies[1] . "' loading='lazy' />", $message);
            }
        }

        return $message;
    }

    /**
     * Transforme les images de smilies en codes texte (inverse de smilie()).
     *
     * @param string $message Message contenant des <img> de smilies
     * @return string Message avec les images remplacées par leurs codes texte
     */
    public static function smile(string $message): string
    {
        // Tranforme une IMG en :-)
        global $theme;

        if ($ibid = Theme::themeImage('forum/smilies/smilies.php')) {
            $imgtmp = 'themes/' . $theme . '/assets/images/forum/smilies/';
        } else {
            $imgtmp = 'assets/images/forum/smilies/';
        }

        if (file_exists($imgtmp . 'smilies.php')) {
            include($imgtmp . 'smilies.php');

            foreach ($smilies as $tab_smilies) {
                $message = str_replace("<img class='n-smil' src='" . $imgtmp . $tab_smilies[1] . "' loading='lazy' />", $tab_smilies[0], $message);
            }
        }

        if ($ibid = Theme::themeImage('forum/smilies/more/smilies.php')) {
            $imgtmp = 'themes/' . $theme . '/assets/images/forum/smilies/more/';
        } else {
            $imgtmp = 'assets/images/forum/smilies/more/';
        }

        if (file_exists($imgtmp . 'smilies.php')) {
            include($imgtmp . 'smilies.php');

            foreach ($smilies as $tab_smilies) {
                $message = str_replace("<img class='n-smil' src='" . $imgtmp . $tab_smilies[1] . "' loading='lazy' />", $tab_smilies[0],  $message);
            }
        }

        return $message;
    }

    /**
     * Affiche les smilies supplémentaires avec liens pour insertion dans un message.
     *
     * @return void
     */
    public static function putitemsMore(): void
    {
        global $theme, $tmp_theme;

        if (stristr($_SERVER['PHP_SELF'], 'more_emoticon.php')) {
            $theme = $tmp_theme;
        }

        echo '<p align="center">' . translate('Cliquez pour insérer des émoticons dans votre message') . '</p>';

        if ($ibid = Theme::themeImage('forum/smilies/more/smilies.php')) {
            $imgtmp = 'themes/' . $theme . '/assets/images/forum/smilies/more/';
        } else {
            $imgtmp = 'assets/images/forum/smilies/more/';
        }

        if (file_exists($imgtmp . 'smilies.php')) {
            include($imgtmp . 'smilies.php');

            echo '<div>';

            foreach ($smilies as $tab_smilies) {
                if ($tab_smilies[3]) {
                    echo '<span class ="d-inline-block m-2"><a href="#" onclick="javascript: DoAdd(\'true\',\'message\',\' ' . $tab_smilies[0] . '\');"><img src="' . $imgtmp . $tab_smilies[1] . '" width="32" height="32" alt="' . $tab_smilies[2];

                    if ($tab_smilies[2]) {
                        echo ' => ';
                    }

                    echo $tab_smilies[0] . '" loading="lazy" /></a></span>';
                }
            }

            echo '</div>';
        }
    }

    /**
     * Affiche un bouton popover pour insérer des emoji Unicode dans un textarea.
     *
     * @param string $targetarea ID du textarea cible
     * @return void
     */
    public static function putitems(string $targetarea): void
    {
        echo '<div title="' . translate('Cliquez pour insérer des emoji dans votre message') . '" data-bs-toggle="tooltip">
            <button class="btn btn-link ps-0" type="button" id="button-textOne" data-bs-toggle="emojiPopper" data-bs-target="#' . $targetarea . '">
                <i class="far fa-smile fa-lg" aria-hidden="true"></i>
            </button>
        </div>
        <script src="assets/shared/emojipopper/js/emojiPopper.min.js"></script>
        <script type="text/javascript">
            //<![CDATA[
                $(function () {
                    "use strict"
                    var emojiPopper = $(\'[data-bs-toggle="emojiPopper"]\').emojiPopper({
                        url: "assets/shared/emojipopper/php/emojicontroller.php",
                        title:"Choisir un emoji"
                    });
                });
            //]]>
        </script>';
    }
}
