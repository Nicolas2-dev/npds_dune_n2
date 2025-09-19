<?php

namespace App\Components;

use Npds\Config\Config;
use Npds\Events\Dispatcher;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::Banner(); ?>
*/

class BannerComponent extends BaseComponent
{

    /**
     * Indique si une impression a déjà été comptée pour cette page.
     *
     * @var bool
     */
    protected static bool $impressionCounted = false;

    
    /**
     * Rendu du composant Banner.
     *
     * Cette méthode vérifie si les bannières sont activées dans la configuration,
     * comptabilise l'impression uniquement une fois par page et retourne le HTML
     * de la bannière via l'événement 'render.banner'.
     *
     * @param array|string $params Paramètres optionnels passés au composant
     *
     * @return string Le HTML de la bannière
     */
    public function render(array|string $params = []): string
    {
        //global $hlpfile;

        // $hlpfile n’est plus nécessaire puisque la gestion des bannières passe désormais par le contrôleur 
        // front et est désormais déclenchée via un event, n’ayant plus d’usage dans l’admin.
        //if (Config::get('banner.banners') && !$hlpfile) {
        //    ob_start();
        //        include 'banners.php'; 
        //        $output = ob_get_contents();
        //    ob_end_clean();
        //    return $output;
        //}

        //if (Config::get('banner.banners')) {
        //    // Fire l'événement banner et récupère le contenu du listeners
        //    return Dispatcher::getInstance()->until('render.banner') ?? '';
        //}

        //return '';

        if (!Config::get('banner.banners')) {
            return '';
        }

        // Vérifie si on a déjà compté une impression pour cette page
        if (!self::$impressionCounted) {
            // Ici tu peux exécuter la logique qui incrémente impmade
            Dispatcher::getInstance()->until('render.banner');
            self::$impressionCounted = true;
        }

        // Ensuite, tu peux afficher le HTML partout sans compter d’autres impressions
        return Dispatcher::getInstance()->until('render.banner') ?? '';

    }
}
