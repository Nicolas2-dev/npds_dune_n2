<?php

/************************************************************************/
/* DUNE by NPDS / SUPER-CACHE engine                                    */
/*                                                                      */
/* NPDS Copyright (c) 2002-2024 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/
/************************************************************************/
/*  Original Autor : Francisco Echarte [patxi@eslomas.com]              */
/*  Revision : 2004-03-15 Version: 1.1 / multi-language support by Dev  */
/*  Revision : 2004-08-10 Version: 1.2 / SQL support by Dev             */
/*  Revision : 2006-01-28 Version: 1.3 / .common support by Dev         */
/*  Revision : 2009-03-12 Version: 1.4 / clean_limit mods by Dev        */
/*  Revision : 2018 Version: 1.5 / support php 7                        */
/************************************************************************/

class SuperCacheEmpty
{

    /**
     * Indique si le cache est en cours de génération
     */
    public bool $genereting_output = false;

    
    public function __construct(bool $genereting_output = false)
    {
        $this->genereting_output = $genereting_output;
    }

}
