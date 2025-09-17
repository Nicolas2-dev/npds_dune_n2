<?php

namespace App\Http\Controllers\Front\Chat;

use App\Http\Controllers\Core\FrontBaseController;


class ChatTop extends FrontBaseController
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
        $nuke_url = '';
        $meta_op = '';

        include 'storage/meta/meta.php';

        echo '</head>
            <body>
            </body>
        </html>';
    }

}
