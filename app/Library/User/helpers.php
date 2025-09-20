<?php

use App\Support\Facades\Theme;

if (!function_exists('showImage')) {
    /**
     * Affiche un script JavaScript permettant de changer dynamiquement
     * l'image d'avatar dans le formulaire d'inscription.
     *
     * @return void
     */
    function showImage(): void
    {
        echo "<script type=\"text/javascript\">
            //<![CDATA[
                function showimage() {
                    if (!document.images) {
                        return
                    }

                    document.images.avatar.src=\n";

        if ($ibid = Theme::themeImage('forum/avatar/blank.gif')) {
            $imgtmp = substr($ibid, 0, strrpos($ibid, '/') + 1);
        } else {
            $imgtmp = 'assets/images/forum/avatar/';
        }

        echo "'$imgtmp' + document.Register.user_avatar.options[document.Register.user_avatar.selectedIndex].value\n";
        echo "  }
            //]]>
        </script>";
    }
}
