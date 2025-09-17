<?php

namespace App\Http\Controllers\Front\Chat;

use App\Http\Controllers\Core\FrontBaseController;


class PowerPack extends FrontBaseController
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
        global $powerpack;
        $powerpack = true;

        settype($op, 'string');

        switch ($op) {

            // Purge Chat Box
            case 'admin_chatbox_write':
                if ($admin) {
                    $adminX = base64_decode($admin);
                    $adminR = explode(':', $adminX);

                    $Q = sql_fetch_assoc(sql_query("SELECT * 
                                                    FROM " . sql_prefix('authors') . " 
                                                    WHERE aid='$adminR[0]' 
                                                    LIMIT 1"));

                    if ($Q['radminsuper'] == 1)
                        if ($chatbox_clearDB == 'OK') {
                            sql_query("DELETE FROM " . sql_prefix('chatbox') . " 
                                    WHERE date <= " . (time() - (60 * 5)));
                        }
                }

                Header('Location: index.php');
                break;
        }
    }

}
