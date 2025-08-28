<?php

namespace App\Library\Access;


class Access
{

    public static function access_denied()
    {
        include 'admin/die.php';
    }

}
