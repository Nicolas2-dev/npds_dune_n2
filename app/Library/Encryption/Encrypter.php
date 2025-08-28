<?php

namespace App\Library\Encryption;


class Encrypter
{

    /**
     * Composant utilisé pour les fonctions d'encrypt et decrypt.
     *
     * Applique un XOR entre chaque caractère du texte et la clé MD5.
     *
     * @param string $txt Texte à chiffrer ou déchiffrer.
     * @param string $encrypt_key Clé de chiffrement.
     * @return string Texte transformé.
     */
    public static function keyED(string $txt, string $encrypt_key): string
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

    /**
     * Retourne une chaîne encryptée en utilisant la valeur globale de $NPDS_Key.
     *
     * @param string $txt Texte à chiffrer.
     * @return string Texte encrypté.
     */
    public static function encrypt(string $txt): string
    {
        global $NPDS_Key;

        return static::encryptK($txt, $NPDS_Key);
    }

    /**
     * Retourne une chaîne encryptée en utilisant une clé spécifique $C_key.
     *
     * @param string $txt Texte à chiffrer.
     * @param string $C_key Clé de chiffrement.
     * @return string Texte encrypté.
     */
    public static function encryptK(string $txt, string $C_key): string
    {
        // note : srand() est optionnel depuis PHP 4.2.0 ne sert plus a rien !

        // Génération de la graine de manière compatible
        //$microtime = microtime(true) * 1000000;
        //
        //// Pour PHP 8.1+ qui est strict sur les conversions de types
        //if (PHP_VERSION_ID >= 80100) {
        //    srand((int) round($microtime));
        //} else {
        //    // Pour les versions antérieures à PHP 8.1
        //    srand((float) $microtime);
        //}
        //
        //$encrypt_key = md5(rand(0, 32000));

        $encrypt_key = md5((string) random_int(0, 32000));

        $ctr = 0;
        $tmp = '';

        for ($i = 0; $i < strlen($txt); $i++) {
            if ($ctr == strlen($encrypt_key)) {
                $ctr = 0;
            }

            $tmp .= substr($encrypt_key, $ctr, 1) . (substr($txt, $i, 1) ^ substr($encrypt_key, $ctr, 1));

            $ctr++;
        }

        return base64_encode(static::keyED($tmp, $C_key));
    }

    /**
     * Retourne une chaîne décryptée en utilisant la valeur globale de $NPDS_Key.
     *
     * @param string $txt Texte à déchiffrer.
     * @return string Texte décrypté.
     */
    public static function decrypt(string $txt): string
    {
        global $NPDS_Key;

        return static::decryptK($txt, $NPDS_Key);
    }

    /**
     * Retourne une chaîne décryptée en utilisant une clé spécifique $C_key.
     *
     * @param string $txt Texte à déchiffrer.
     * @param string $C_key Clé de déchiffrement.
     * @return string Texte décrypté.
     */
    public static function decryptK(string $txt, string $C_key): string
    {
        $txt = static::keyED(base64_decode($txt), $C_key);
        $tmp = '';

        for ($i = 0; $i < strlen($txt); $i++) {
            $md5 = substr($txt, $i, 1);
            $i++;
            $tmp .= (substr($txt, $i, 1) ^ $md5);
        }

        return $tmp;
    }
    
}

