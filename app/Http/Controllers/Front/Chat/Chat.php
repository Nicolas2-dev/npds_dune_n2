<?php

namespace App\Http\Controllers\Front\Chat;

use App\Http\Controllers\Core\FrontBaseController;


class Chat extends FrontBaseController
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
        $meta_op = '';
        $meta_doctype = '<!DOCTYPE html>';

        $Titlesitename = 'NPDS';

        include 'storage/meta/meta.php';

        echo '
            <link rel="shortcut icon" href="assets/images/favicon/favicon.ico" type="image/x-icon" />
            </head>  
            <div style="height:1vh;" class="">
                <iframe src="chatrafraich.php?repere=0&amp;aff_entetes=1&amp;connectes=-1&amp;id=' . $id . '&amp;auto=' . $auto . '" frameborder="0" scrolling="no" noresize="noresize" name="rafraich" width="100%" height="100%"></iframe>
            </div>
            <div style="height:58vh;" class="">
                <iframe src="chattop.php" frameborder="0" scrolling="yes" noresize="noresize" name="haut" width="100%" height="100%"></iframe>
            </div>
            <div style="height:39vh;" class="">
                <iframe src="chatinput.php?id=' . $id . '&amp;auto=' . $auto . '" frameborder="0" scrolling="yes" noresize="noresize" name="bas" width="100%" height="100%"></iframe>
            </div>
        </html>';
    }

}
