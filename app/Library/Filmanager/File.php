<?php

namespace App\Library\FileManagement;

use App\Library\Theme\Theme;


/**
 * Classe File
 *
 * Fournit des informations sur un fichier : URL, taille, extension,
 * et permet d'afficher une icône ou un indicateur visuel selon le type de fichier.
 */
class File
{

    /**
     * URL ou chemin du fichier
     *
     * @var string
     */
    public $url = '';

    /**
     * Extension du fichier (ex : "jpg", "pdf")
     *
     * @var string
     */
    public $extention = '';

    /**
     * Taille du fichier en octets
     *
     * @var int
     */
    public $size = 0;


    /**
     * Constructeur
     *
     * @param string $url Chemin du fichier
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Récupère la taille du fichier et la stocke dans $this->size
     *
     * @return void
     */
    public function size(): void
    {
        $this->size = @filesize($this->url);
    }

    /**
     * Récupère l'extension du fichier et la stocke dans $this->Extention
     *
     * @return void
     */
    public function extention(): void
    {
        $extension = strtolower(substr(strrchr($this->url, '.'), 1));

        $this->extention = $extension;
    }

    /**
     * Affiche une représentation visuelle de l'extension du fichier
     *
     * @param string $Format Type de représentation : 'IMG' pour image, 'webfont' pour icône webfont
     * @return string Code HTML correspondant à l'affichage de l'extension
     */
    public function afficheExtention(string $Format): string
    {
        $this->extention();

        switch (strtoupper($Format)) {

            case 'IMG':
                if ($ibid = Theme::image('images/upload/file_types/' . $this->extention . '.gif')) {
                    $imgtmp = $ibid;
                } else {
                    $imgtmp = 'assets/images/upload/file_types/' . $this->extention . '.gif';
                }

                if (@file_exists($imgtmp)) {
                    return '<img src="' . $imgtmp . '" />';
                } else {
                    return '<img src="assets/images/upload/file_types/unknown.gif" />';
                }

            case 'WEBFONT':
                return '<span class="fa-stack">
                        <i class="fa fa-file fa-stack-2x"></i>
                        <span class="fa-stack-1x filetype-text">' . $this->extention . '</span>
                    </span>';

            default:
                return '';       
        }
    }
}
