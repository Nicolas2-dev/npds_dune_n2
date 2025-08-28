<?php

namespace App\Library\Access;


class Access
{

    function access_denied()
    {
        include 'admin/die.php';
    }

}
