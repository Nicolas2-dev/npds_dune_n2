<?php

namespace App\Library\Password;


class Password
{

    /**
     * Instance singleton du dispatcher.
     *
     * @var self|null
     */
    protected static ?self $instance = null;


    /**
     * Retourne l'instance singleton du dispatcher.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }
    
    /**
     * Calcule le coût optimal pour le hashage d'un mot de passe avec Bcrypt ou autre algorithme compatible.
     *
     * Cette méthode teste les coûts de 8 à 12 et retourne le premier coût dont
     * le temps de calcul dépasse le temps minimum alloué.
     *
     * @param string $pass Mot de passe à hasher.
     * @param int $algoCrypt Constante de l'algorithme de hashage (ex: PASSWORD_BCRYPT).
     * @param int $min_ms Temps minimum en millisecondes pour le calcul du hash (par défaut 100 ms).
     * @return int|null Coût optimal détecté, ou null si aucun coût ne dépasse le temps minimum.
     */
    public function getOptimalBcryptCostParameter(string $pass, int $algoCrypt, int $min_ms = 100): ?int
    {
        for ($i = 8; $i < 13; $i++) {

            $calculCost = [
                'cost' => $i
            ];
            
            $time_start = microtime(true);

            password_hash($pass, $algoCrypt, $calculCost);

            $time_end = microtime(true);

            if (($time_end - $time_start) * 1000 > $min_ms) {
                return $i;
            }
        }

        return null;
    }
    
    /**
     * Génère un mot de passe aléatoire en combinant des syllabes et des chiffres.
     *
     * La méthode choisit 4 éléments aléatoires parmi une liste de syllabes et des chiffres.
     *
     * @return string Mot de passe généré
     */
    public function makePass(): string
    {
        $makepass = '';

        $syllables = 'Er@1,In@2,Ti#a3,D#un4,F_e5,P_re6,V!et7,J!o8,Ne%s9,A%l0,L*en1,So*n2,Ch$a3,I$r4,L^er5,Bo^6,Ok@7,!Tio8,N@ar9,0Sim,1P$le,2B*la,3Te!n,4T~oe,5Ch~o,6Co,7Lat,8Spe,9Ak,0Er,1Po,2Co,3Lor,4Pen,5Cil!,6Li!,7Ght,8_Wh,9_At,T#he0,#He1,@Ck2,Is@3,M1am@,B2o+,3No@,Fi-4,0Ve!,A9ny#,Wa7y$,P8ol%,Iti^6,Cs~5,Ra*,@Dio,+Sou,-Rce,!Sea,#Rch,$Pa,&Per,^Com,~Bo,*Sp,Eak1*,S2t~,Fi^,R3st&,Gr#,O5up@,!Boy,Ea!,Gle*,4Tr*,+A1il,B0i+,_Bl9e,Br8b_,P7ri#,De6e!,$Ka3y,1En$,2Be-,4Se-';
        $syllable_array = explode(',', $syllables);

        srand((float) microtime() * 1000000);

        for ($count = 1; $count <= 4; $count++) {
            if (rand() % 10 === 1) {
                $makepass .= (string) ((rand() % 50) + 1);
            } else {
                $makepass .= $syllable_array[rand() % count($syllable_array)];
            }
        }

        return $makepass;
    }

    /**
     * Génère un mot de passe aléatoire sécurisé.
     *
     * @param int $length Longueur souhaitée du mot de passe
     * @return string Mot de passe généré
     */
    public function makePassword(int $length = 12): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_+=';
        $password = '';

        $maxIndex = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $maxIndex)];
        }

        return $password;
    }

}
