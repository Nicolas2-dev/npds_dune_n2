<?php

namespace App\Library\Http;


class Response
{

    #autodoc file_contents_exist() : Controle de réponse// c'est pas encore assez fin not work with https probably
    function file_contents_exist($url, $response_code = 200)
    {
        $headers = get_headers($url);

        if (substr($headers[0], 9, 3) == $response_code) {
            return true;
        } else {
            return false;
        }
    }
    
}
