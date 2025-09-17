<?php

namespace App\Http\Controllers\Front\Forum;

use App\Http\Controllers\Core\FrontBaseController;


class MoreEmoticon extends FrontBaseController
{

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        parent::initialize();
    }

    public function index()
    {
        if (isset($user)) {
            if ($cookie[9] == '') {
                $cookie[9] = $Default_Theme;
            }

            if (isset($theme)) {
                $cookie[9] = $theme;
            }

            $tmp_theme = $cookie[9];

            if (!$file = @opendir('themes/' . $cookie[9])) {
                $tmp_theme = $Default_Theme;
            }
        } else {
            $tmp_theme = $Default_Theme;
        }

        include 'storage/meta/meta.php';

        echo '<link rel="stylesheet" href="assets/skins/default/bootstrap.min.css">';

        echo Css::importCss($tmp_theme, $language, '', '', '');

        include 'library/formhelp.java.php';

        echo '</head>
            <body class="p-2">
            ' . Smilies::putitemsMore() . '
            </body>
        </html>';
    }

}
