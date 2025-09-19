<?php

use App\Library\Components\BaseComponent;

/*
<?= Component::smilies(':paf'); ?>
<?= Component::smilies(':-P'); ?>
<?= Component::smilies(':-o'); ?>
*/

class SmiliesComponent extends BaseComponent
{

    /**
     * Liste des smilies avec code et chemin de l'image
     *
     * @var array
     */
    private array $smilies = [
        ':-)' => 'images/forum/smilies/icon_smile.gif',
        ':-]' => 'images/forum/smilies/icon_smile.gif',
        ';-)' => 'images/forum/smilies/icon_wink.gif',
        ';-]' => 'images/forum/smilies/icon_wink.gif',
        ':-(' => 'images/forum/smilies/icon_frown.gif',
        ':-[' => 'images/forum/smilies/icon_frown.gif',
        '8-)' => 'images/forum/smilies/icon_cool.gif',
        '8-]' => 'images/forum/smilies/icon_cool.gif',
        ':-P' => 'images/forum/smilies/icon_razz.gif',
        ':-D' => 'images/forum/smilies/icon_biggrin.gif',
        ':=!' => 'images/forum/smilies/yaisse.gif',
        ':b'  => 'images/forum/smilies/icon_tongue.gif',
        ':D'  => 'images/forum/smilies/icon_grin.gif',
        ':#'  => 'images/forum/smilies/icon_ohwell.gif',
        ':-o' => 'images/forum/smilies/icon_eek.gif',
        ':-?' => 'images/forum/smilies/icon_confused.gif',
        ':-|' => 'images/forum/smilies/icon_mad.gif',
        ':|'  => 'images/forum/smilies/icon_mad2.gif',
        ':paf'=> 'images/forum/smilies/pafmur.gif',
    ];

    /**
     * Rend la liste des smilies ou un smiley spécifique
     *
     * @param array $params Paramètres optionnels
     * @return string HTML généré
     */
    public function render(array|string $params = []): string
    {
        // Initialisation pour éviter les retours vides
        $html = '';

        // Si $params est un string et existe dans les smilies
        if (is_string($params) && isset($this->smilies[$params])) {
            $html = '<img src="' . asset_url($this->smilies[$params]) . '" alt="' . $params . '" />';
        }

        return $html; 
    }

}
