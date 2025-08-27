<?php

namespace App\Library\Encryption;


class Encrypter
{

    #autodoc keyED($txt,$encrypt_key) : Composant des fonctions encrypt et decrypt
    function keyED($txt, $encrypt_key)
    {
        $encrypt_key = md5($encrypt_key);
        $ctr = 0;
        $tmp = '';

        for ($i = 0; $i < strlen($txt); $i++) {
            if ($ctr == strlen($encrypt_key)) {
                $ctr = 0;
            }

            $tmp .= substr($txt, $i, 1) ^ substr($encrypt_key, $ctr, 1);
            $ctr++;
        }

        return $tmp;
    }

    #autodoc encrypt($txt) : retourne une chaine encryptée en utilisant la valeur de $NPDS_Key
    function encrypt($txt)
    {
        global $NPDS_Key;

        return encryptK($txt, $NPDS_Key);
    }

    #autodoc encryptK($txt, $C_key) : retourne une chaine encryptée en utilisant la clef : $C_key
    function encryptK($txt, $C_key)
    {
        // Génération de la graine de manière compatible
        $microtime = microtime(true) * 1000000;

        // Pour PHP 8.1+ qui est strict sur les conversions de types
        if (PHP_VERSION_ID >= 80100) {
            srand((int)round($microtime));
        } else {
            // Pour les versions antérieures à PHP 8.1
            srand((float)$microtime);
        }

        $encrypt_key = md5(rand(0, 32000));
        $ctr = 0;
        $tmp = '';

        for ($i = 0; $i < strlen($txt); $i++) {
            if ($ctr == strlen($encrypt_key)) {
                $ctr = 0;
            }

            $tmp .= substr($encrypt_key, $ctr, 1) . (substr($txt, $i, 1) ^ substr($encrypt_key, $ctr, 1));

            $ctr++;
        }

        return base64_encode(keyED($tmp, $C_key));
    }

    #autodoc decrypt($txt) : retourne une chaine décryptée en utilisant la valeur de $NPDS_Key
    function decrypt($txt)
    {
        global $NPDS_Key;

        return decryptK($txt, $NPDS_Key);
    }

    #autodoc decryptK($txt, $C_key) : retourne une décryptée en utilisant la clef de $C_Key
    function decryptK($txt, $C_key)
    {
        $txt = keyED(base64_decode($txt), $C_key);
        $tmp = '';

        for ($i = 0; $i < strlen($txt); $i++) {
            $md5 = substr($txt, $i, 1);
            $i++;
            $tmp .= (substr($txt, $i, 1) ^ $md5);
        }

        return $tmp;
    }
    
}

