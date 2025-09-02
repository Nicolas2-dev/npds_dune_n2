<?php

namespace Modules\Upload\Support;


enum UploadMode: int {

    /**
     * Lien classique.
     * Le fichier ne peut pas être affiché en ligne,
     * il sera proposé en téléchargement ou sous forme d’icône.
     */
    case LINK = 1;

    /**
     * Image.
     * Le fichier sera affiché directement comme une image dans la page.
     * Formats supportés : GIF, PNG, JPEG, SVG, etc.
     */
    case IMG = 2;

    /**
     * HTML embarqué.
     * Le fichier HTML sera rendu directement dans la page.
     * Attention à la sécurité XSS.
     */
    case HTML = 3;

    /**
     * Texte préformaté.
     * Affichage du contenu texte avec balises <pre> pour garder la mise en forme.
     */
    case PLAINTEXT = 4;

    /**
     * Flash.
     * Le fichier sera rendu comme animation Shockwave Flash (SWF).
     * Note : Flash est obsolète dans la plupart des navigateurs modernes.
     */
    case SWF = 5;

    /**
     * Vidéo.
     * Le fichier sera affiché avec un lecteur vidéo intégré.
     * Formats supportés : MPEG, MP4, etc.
     */
    case VIDEO = 6;

    /**
     * Audio.
     * Le fichier sera lu avec un lecteur audio intégré.
     * Formats supportés : MP3, etc.
     */
    case AUDIO = 7;
    
}