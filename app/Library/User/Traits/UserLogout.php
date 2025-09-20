<?php

namespace App\Library\User\Traits;


trait UserLogout
{

    public function logout()
    {
        global $user, $cookie;

        if ($cookie[1] != '') {
            sql_query("DELETE FROM " . sql_prefix('session') . " 
                    WHERE username='$cookie[1]'");
        }

        setcookie('user', '', 0);
        unset($user);

        setcookie('user_language', '', 0);
        unset($user_language);

        Header('Location: index.php');
    }

}
