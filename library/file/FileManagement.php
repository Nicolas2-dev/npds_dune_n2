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

// class pour php7
class FileManagement
{

    public $units = array('B', 'KB', 'MB', 'GB', 'TB');


    function file_size_format($fileName, $precision)
    {
        $bytes = $fileName;
        $bytes = max($bytes, 0);

        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($this->units) - 1);

        $bytes /= pow(1024, $pow);
        $retValue = round($bytes, $precision) . ' ' . $this->units[$pow];

        return $retValue;
    }

    function file_size_auto($fileName, $precision)
    {
        $bytes = @filesize($fileName);
        $bytes = max($bytes, 0);

        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($this->units) - 1);

        $bytes /= pow(1024, $pow);
        $retValue = round($bytes, $precision) . ' ' . $this->units[$pow];

        return $retValue;
    }

    function file_size_option($fileName, $unitType)
    {
        switch ($unitType) {

            case $this->units[0]:
                $fileSize = number_format((filesize(trim($fileName))), 1);
                break;

            case $this->units[1]:
                $fileSize = number_format((filesize(trim($fileName)) / 1024), 1);
                break;

            case $this->units[2]:
                $fileSize = number_format((filesize(trim($fileName)) / 1024 / 1024), 1);
                break;

            case $this->units[3]:
                $fileSize = number_format((filesize(trim($fileName)) / 1024 / 1024 / 1024), 1);
                break;

            case $this->units[4]:
                $fileSize = number_format((filesize(trim($fileName)) / 1024 / 1024 / 1024 / 1024), 1);
                break;
        }

        $retValue = $fileSize . ' ' . $unitType;

        return $retValue;
    }
}
