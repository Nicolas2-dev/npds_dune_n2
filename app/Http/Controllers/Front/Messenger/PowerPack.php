<?php

namespace App\Http\Controllers\Front\Messenger;

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

            // Instant Members Message
            case 'instant_message':
                Messenger::FormInstantMessage($to_userid);
                break;

            case 'write_instant_message':
                settype($copie, 'string');
                settype($messages, 'string');

                if (isset($user)) {
                    $rowQ1 = Q_Select("SELECT uid 
                                    FROM " . sql_prefix('users') . " 
                                    WHERE uname='$cookie[1]'", 3600);

                    $uid = $rowQ1[0];

                    $from_userid = $uid['uid'];

                    if (($subject != '') or ($message != '')) {
                        $subject = Sanitize::fixQuotes($subject) . '';
                        $messages = Sanitize::fixQuotes($messages) . '';

                        Messenger::dbWritePrivateMessage($to_userid, '', $subject, $from_userid, $message, $copie);
                    }
                }

                Header('Location: index.php');
                break;

        }
    }

}
