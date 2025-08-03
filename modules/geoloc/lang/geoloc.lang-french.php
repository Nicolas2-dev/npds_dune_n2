<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/*                                                                      */
/*                                                                      */
/* NPDS Copyright (c) 2002-2025 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/*                                                                      */
/* module geoloc version 4.2                                            */
/* geoloc.lang-french.php file 2007-2025 by Jean Pierre Barbary (jpb)   */
/* dev team : Philippe Revilliod (Phr), A.NICOL                         */
/************************************************************************/

function geoloc_translate($phrase) {
  return (htmlentities($phrase,ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401,'UTF-8'));
}
?>