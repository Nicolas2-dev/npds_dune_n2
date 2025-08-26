<?php

/************************************************************************/
/* NPDS V : Net Portal Dynamic System .                                 */
/* ===========================                                          */
/*                                                                      */
/* File Class Manipulation                                              */
/* NPDS Copyright (c) 2002-2024 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

class File
{

    public $Url = '';

    public $Extention = '';

    public $Size = 0;


    public function __construct($Url)
    {
        $this->Url = $Url;
    }

    function Size()
    {
        $this->Size = @filesize($this->Url);
    }

    function Extention()
    {
        $extension = strtolower(substr(strrchr($this->Url, '.'), 1));

        $this->Extention = $extension;
    }

    function Affiche_Extention($Format)
    {
        $this->Extention();

        switch ($Format) {

            case 'IMG':
                if ($ibid = theme_image('images/upload/file_types/' . $this->Extention . '.gif')) {
                    $imgtmp = $ibid;
                } else {
                    $imgtmp = 'assets/images/upload/file_types/' . $this->Extention . '.gif';
                }

                if (@file_exists($imgtmp)) {
                    return '<img src="' . $imgtmp . '" />';
                } else {
                    return '<img src="assets/images/upload/file_types/unknown.gif" />';
                }
                break;

            case "webfont":
                return '<span class="fa-stack">
                    <i class="fa fa-file fa-stack-2x"></i>
                    <span class="fa-stack-1x filetype-text">' . $this->Extention . '</span>
                    </span>';
                break;
        }
    }
}
