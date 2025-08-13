<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/*                                                                      */
/* NPDS Copyright (c) 2002-2024 by Philippe Brunier                     */
/*                                                                      */
/* api NPDS proto 02                                                    */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/
if (!stristr($_SERVER['PHP_SELF'], 'admin.php')) include('admin/die.php');

function alerte_api()
{
    global sql_prefix(''), $admin;
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $result = sql_query("SELECT * FROM " . sql_prefix('') . "fonctions WHERE fid='$id'");
        if (isset($result)) {
            $row = sql_fetch_assoc($result);
            if (count($row) > 0)
                $data = $row;
        }
        echo json_encode($data);
    }
}

function alerte_update()
{
    global sql_prefix(''), $admin;
    $Xadmin = base64_decode($admin);
    $Xadmin = explode(':', $Xadmin);
    $aid = urlencode($Xadmin[0]);
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $result = sql_query("SELECT * FROM " . sql_prefix('') . "fonctions WHERE fid=" . $id . "");
        $row = sql_fetch_assoc($result);
        $newlecture = $aid . '|' . $row['fdroits1_descr'];
        sql_query("UPDATE " . sql_prefix('') . "fonctions SET fdroits1_descr='" . $newlecture . "' WHERE fid=" . $id . "");
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

switch ($op) {

    case "alerte_api":
        alerte_api();
        break;
    case "alerte_update":
        alerte_update();
        break;
}
