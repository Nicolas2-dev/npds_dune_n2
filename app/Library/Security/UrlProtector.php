<?php

namespace App\Library\Security;


class UrlProtector
{

    function url_protect($arr, $key)
    {
        // include url_protect Bad Words and create the filter function
        include 'config/url_protect.php';

        // mieux faire face aux techniques d'évasion de code : base64_decode(utf8_decode(bin2hex($arr))));
        $arr = rawurldecode($arr);
        $RQ_tmp = strtolower($arr);
        $RQ_tmp_large = strtolower($key) . '=' . $RQ_tmp;

        if (
            in_array($RQ_tmp, $bad_uri_content)
            or
            in_array($RQ_tmp_large, $bad_uri_content)
            or
            in_array($key, $bad_uri_key, true)
            or
            count($badname_in_uri) > 0
        ) {
            unset($bad_uri_content);
            unset($bad_uri_key);
            unset($badname_in_uri);

            access_denied();
        }
    }

}
