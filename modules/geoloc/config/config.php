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
/* geoloc.conf file 2008-2025 by Jean Pierre Barbary (jpb)              */
/* dev team : Philippe Revilliod (Phr), A.NICOL                         */
/************************************************************************/

// clef api Azure maps 
$api_key_azure = ""; 

// clef api mapbox 
$api_key_mapbox = ""; 

// Champ lat dans sql 
$ch_lat = "C7"; 

// Champ long dans sql 
$ch_lon = "C8"; 

// interface carte 

// Type de carte
$cartyp = "sat-google";  

// Coordinates Units
$co_unit = "dms"; 

// Chemin des images 
$ch_img = "modules/geoloc/assets/images/"; 

// Autorisation de géolocalisation des IP 
$geo_ip = 1; 

// Clef API pour provider IP ipdata 
$api_key_ipdata = ""; 

// Clef API pour provider IP extreme-ip-lookup
$key_lookup = "";  

// Nom fichier image anonyme géoréférencé en ligne 
$nm_img_acg = "acg.png"; 

// Nom fichier image membre géoréférencé en ligne
$nm_img_mbcg = "mbcg.png";  

// Nom fichier image membre géoréférencé 
$nm_img_mbg = "mbg.png"; 

// Type de marker
$mark_typ = 0;  

// Largeur icone des markers 
$w_ico = "28"; 

// Hauteur icone des markers
$h_ico = "28"; 

// Font SVG
$f_mbg = "user";  

// Echelle du Font SVG du membre 
$mbg_sc = "24"; 

// Epaisseur trait Font SVG du membre
$mbg_t_ep = "1";  

// Couleur trait SVG du membre 
$mbg_t_co = "rgba(241,13,13,1)"; 

// Opacité trait SVG du membre
$mbg_t_op = "1";  

// Couleur fond SVG du membre
$mbg_f_co = "rgba(237,68,151,0.89)";  

// Opacité fond SVG du membre 
$mbg_f_op = "1"; 

// Echelle du Font SVG du membre géoréférencé 
$mbgc_sc = "32"; 

// Epaisseur trait Font SVG du membre géoréférencé
$mbgc_t_ep = "1";  

// Couleur trait SVG du membre géoréférencé 
$mbgc_t_co = "rgba(255,255,255,1)"; 

// Opacité trait SVG du membre géoréférencé
$mbgc_t_op = "1";  

// Couleur fond SVG du membre géoréférencé
$mbgc_f_co = "rgba(225,29,75,0.87)";  

// Opacité fond SVG du membre géoréférencé
$mbgc_f_op = "1";  

// Echelle du Font SVG pour anonyme en ligne 
$acg_sc = "24"; 

// Epaisseur trait Font SVG pour anonyme en ligne
$acg_t_ep = "1";  

// Couleur trait SVG pour anonyme en ligne 
$acg_t_co = "rgba(255,250,247,1)"; 

// Opacité trait SVG pour anonyme en ligne
$acg_t_op = "1";  

// Couleur fond SVG pour anonyme en ligne
$acg_f_co = "rgba(32,32,26,0.89)";  

// Opacité fond SVG pour anonyme en ligne 
$acg_f_op = "1"; 

// interface bloc 

// Type de carte pour le bloc
$cartyp_b = "World_Shaded_Relief";  

// Nom fichier image membre géoréférencé pour le bloc
$img_mbgb = "mbcg.png";  

// Largeur icone marker dans le bloc
$w_ico_b = "28";  

// Hauteur icone marker dans le bloc
$h_ico_b = "28"; 

// hauteur carte dans bloc
$h_b = "240"; 

// facteur zoom carte dans bloc
$z_b = "4"; 
