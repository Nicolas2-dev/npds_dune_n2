<?php

namespace App\Http\Controllers\Admin\Config;


use App\Support\Facades\Log;
use App\Support\Facades\Validation;
use App\Http\Controllers\Core\AdminBaseController;


class ConfigFiles extends AdminBaseController
{
    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        //$f_meta_nom = 'ConfigFiles';
        //$f_titre = adm_translate('Les fichiers de configuration');

        // controle droit
        //admindroits($aid, $f_meta_nom);

        //global $language;
        //$hlpfile = 'admin/manuels/' . $language . '/configfiles.html';

        /*
        switch ($op) {

            case 'ConfigFiles_load':
                if ($files == 'header_before') {
                    if (file_exists('themes/base/bootstrap/header_before.php')) {
                        $fp = fopen('themes/base/bootstrap/header_before.php', 'r');
                        $Xcontents = fread($fp, filesize('themes/base/bootstrap/header_before.php'));
                        fclose($fp);
                        ConfigFiles($Xcontents, $files);
                    } else {
                        copy_sample($files);
                    }
                } elseif ($files == 'header_head') {
                    if (file_exists('themes/base/bootstrap/header_head.php')) {
                        $fp = fopen('themes/base/bootstrap/header_head.php', 'r');
                        $Xcontents = fread($fp, filesize('themes/base/bootstrap/header_head.php'));
                        fclose($fp);
                        ConfigFiles($Xcontents, $files);
                    } else {
                        copy_sample($files);
                    }
                } elseif ($files == 'body_onload') {
                    if (file_exists('themes/base/bootstrap/body_onload.php')) {
                        $fp = fopen('themes/base/bootstrap/body_onload.php', 'r');
                        $Xcontents = fread($fp, filesize('themes/base/bootstrap/body_onload.php'));
                        fclose($fp);
                        ConfigFiles($Xcontents, $files);
                    } else {
                        copy_sample($files);
                    }
                } elseif ($files == 'header_after') {
                    if (file_exists('themes/base/bootstrap/header_after.php')) {
                        $fp = fopen('themes/base/bootstrap/header_after.php', 'r');
                        $Xcontents = fread($fp, filesize('themes/base/bootstrap/header_after.php'));
                        fclose($fp);
                        ConfigFiles($Xcontents, $files);
                    } else {
                        copy_sample($files);
                    }
                } elseif ($files == 'footer_before') {
                    if (file_exists('themes/base/bootstrap/footer_before.php')) {
                        $fp = fopen('themes/base/bootstrap/footer_before.php', 'r');
                        $Xcontents = fread($fp, filesize('themes/base/bootstrap/footer_before.php'));
                        fclose($fp);
                        ConfigFiles($Xcontents, $files);
                    } else {
                        copy_sample($files);
                    }
                } elseif ($files == 'footer_after') {
                    if (file_exists('themes/base/bootstrap/footer_after.php')) {
                        $fp = fopen('themes/base/bootstrap/footer_after.php', 'r');
                        $Xcontents = fread($fp, filesize('themes/base/bootstrap/footer_after.php'));
                        fclose($fp);
                        ConfigFiles($Xcontents, $files);
                    } else {
                        copy_sample($files);
                    }
                } elseif ($files == 'new_user') {
                    if (file_exists('themes/base/bootstrap/new_user.php')) {
                        $fp = fopen('themes/base/bootstrap/new_user.php', 'r');
                        $Xcontents = fread($fp, filesize('themes/base/bootstrap/new_user.php'));
                        fclose($fp);
                        ConfigFiles($Xcontents, $files);
                    } else {
                        copy_sample($files);
                    }
                } elseif ($files == 'user') {
                    if (file_exists('themes/base/bootstrap/user.php')) {
                        $fp = fopen('themes/base/bootstrap/user.php', 'r');
                        $Xcontents = fread($fp, filesize('themes/base/bootstrap/user.php'));
                        fclose($fp);
                        ConfigFiles($Xcontents, $files);
                    } else {
                        copy_sample($files);
                    }
                } elseif ($files == 'cache.config') {
                    if (file_exists('config/cache.config.php')) {
                        $fp = fopen('config/cache.config.php', 'r');
                        $Xcontents = fread($fp, filesize('config/cache.config.php'));
                        fclose($fp);
                        ConfigFiles($Xcontents, $files);
                    }
                } elseif ($files == 'robots') {
                    if (file_exists('robots.txt')) {
                        $fp = fopen('robots.txt', 'r');
                        $Xcontents = fread($fp, filesize('robots.txt'));
                        fclose($fp);
                        ConfigFiles($Xcontents, $files);
                    }
                } elseif ($files == 'humans') {
                    if (file_exists('humans.txt')) {
                        $fp = fopen('humans.txt', 'r');
                        $Xcontents = fread($fp, filesize('humans.txt'));
                        fclose($fp);
                        ConfigFiles($Xcontents, $files);
                    }
                }
                break;

            case 'ConfigFiles_save':
                ConfigFiles_save($Xtxt, $Xfiles);
                break;

            case 'ConfigFiles_create':
                ConfigFiles_create($modele);
                break;

            case 'delete_configfile':
                delete_configfile($file);
                break;

            case 'ConfigFiles_delete':
                ConfigFiles_delete($file);
                break;

            default:
                ConfigFiles('', '');
                break;
        }

        // ConfigFiles
        case 'ConfigFiles':
        case 'ConfigFiles_load':
        case 'ConfigFiles_save':
        case 'ConfigFiles_create':
        case 'delete_configfile':
        case 'ConfigFiles_delete':
            include 'admin/configfiles.php';
            break;

        */

        parent::initialize();        
    }

    public function configFiles($contents, $files)
    {
        //global $hlpfile, $f_meta_nom, $f_titre, $adminimg;

        //include 'header.php';

        //GraphicAdmin($hlpfile);
        //adminhead($f_meta_nom, $f_titre, $adminimg);

        if ($contents == '') {
            echo '<hr />
            <table id="tad_cfile" data-toggle="table" data-striped="true" data-show-toggle="true" data-buttons-class="outline-secondary" data-icons="icons" data-icons-prefix="fa">
                <thead>
                    <tr>
                        <th class="n-t-col-xs-4" data-halign="center" data-align="center" >' . adm_translate('Nom') . '</th>
                        <th class="n-t-col-xs-6" data-halign="center" >' . adm_translate('Description') . '</th>
                        <th class="n-t-col-xs-2" data-halign="center" data-align="center" >' . adm_translate('Fonctions') . '</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3"><span><b>/modules/include</b></span></td>
                    </tr>
                    <tr>
                        <td><code>header_before.inc</code></td>
                        <td>' . adm_translate('Ce fichier est appelé avant que de commencer la génération de la page HTML') . '</td>
                        <td>
                            <a href="admin.php?op=ConfigFiles_load&amp;files=header_before"><i class="fa fa-edit fa-lg" title="' . adm_translate('Editer') . '" data-bs-toggle="tooltip"></i></a>
                            <a href="admin.php?op=delete_configfile&amp;file=header_before"><i class="fas fa-trash fa-lg text-danger ms-3" title="' . adm_translate('Supprimer') . '" data-bs-toggle="tooltip" ></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td><code>header_head.inc</code></td>
                        <td>' . adm_translate('Ce fichier est appelé entre le HEAD et /HEAD lors de la génération de la page HTML') . '</td>
                        <td>
                            <a href="admin.php?op=ConfigFiles_load&amp;files=header_head"><i class="fa fa-edit fa-lg" title="' . adm_translate('Editer') . '" data-bs-toggle="tooltip"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td><code>body_onload.inc</code></td>
                        <td>' . adm_translate('Ce fichier est appelé dans l\'évènement ONLOAD de la balise BODY => JAVASCRIPT') . '</td>
                        <td>
                            <a href="admin.php?op=ConfigFiles_load&amp;files=body_onload"><i class="fa fa-edit fa-lg" title="' . adm_translate('Editer') . '" data-bs-toggle="tooltip"></i></a>
                            <a href="admin.php?op=delete_configfile&amp;file=body_onload"><i class="fas fa-trash fa-lg text-danger ms-3" title="' . adm_translate('Supprimer') . '" data-bs-toggle="tooltip" ></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td><code>header_after.inc</code></td>
                        <td>' . adm_translate('Ce fichier est appelé à la fin du header du thème') . '</td>
                        <td>
                            <a href="admin.php?op=ConfigFiles_load&amp;files=header_after"><i class="fa fa-edit fa-lg" title="' . adm_translate('Editer') . '" data-bs-toggle="tooltip"></i></a>
                            <a href="admin.php?op=delete_configfile&amp;file=header_after"><i class="fas fa-trash fa-lg text-danger ms-3" title="' . adm_translate('Supprimer') . '" data-bs-toggle="tooltip" ></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td><code>footer_before.inc</code></td>
                        <td>' . adm_translate('Ce fichier est appelé avant le début du footer du thème') . '</td>
                        <td>
                            <a href="admin.php?op=ConfigFiles_load&amp;files=footer_before"><i class="fa fa-edit fa-lg" title="' . adm_translate('Editer') . '" data-bs-toggle="tooltip"></i></a>
                            <a href="admin.php?op=delete_configfile&amp;file=footer_before"><i class="fas fa-trash fa-lg text-danger ms-3" title="' . adm_translate('Supprimer') . '" data-bs-toggle="tooltip" ></i></a></td>
                    </tr>
                    <tr>
                        <td><code>footer_after.inc</code></td>
                        <td>' . adm_translate('Ce fichier est appelé après la fin de la génération de la page HTML') . '</td>
                        <td>
                            <a href="admin.php?op=ConfigFiles_load&amp;files=footer_after"><i class="fa fa-edit fa-lg" title="' . adm_translate('Editer') . '" data-bs-toggle="tooltip"></i></a>
                            <a href="admin.php?op=delete_configfile&amp;file=footer_after"><i class="fas fa-trash fa-lg text-danger ms-3" title="' . adm_translate('Supprimer') . '" data-bs-toggle="tooltip" ></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td><code>new_user.inc</code></td>
                        <td>' . adm_translate('Ce fichier permet d\'envoyer un MI personnalisé lorsqu\'un nouveau membre s\'inscrit') . '</td>
                        <td>
                            <a href="admin.php?op=ConfigFiles_load&amp;files=new_user"><i class="fa fa-edit fa-lg" title="' . adm_translate('Editer') . '" data-bs-toggle="tooltip"></i></a>
                            <a href="admin.php?op=delete_configfile&amp;file=new_user"><i class="fas fa-trash fa-lg text-danger ms-3" title="' . adm_translate('Supprimer') . '" data-bs-toggle="tooltip" ></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td><code>user.inc</code></td>
                        <td>' . adm_translate('Ce fichier permet l\'affichage d\'informations complémentaires dans la page de login') . '</td>
                        <td>
                            <a href="admin.php?op=ConfigFiles_load&amp;files=user"><i class="fa fa-edit fa-lg" title="' . adm_translate('Editer') . '" data-bs-toggle="tooltip"></i></a>
                            <a href="admin.php?op=delete_configfile&amp;file=user"><i class="fas fa-trash fa-lg text-danger ms-3" title="' . adm_translate('Supprimer') . '" data-bs-toggle="tooltip" ></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td><code>cache.config.php</code></td>
                        <td>' . adm_translate('Ce fichier permet la configuration technique de SuperCache') . ' ( / )</td>
                        <td><a href="admin.php?op=ConfigFiles_load&amp;files=cache.config"><i class="fa fa-edit fa-lg" title="' . adm_translate('Editer') . '" data-bs-toggle="tooltip"></i></a></td>
                    </tr>
                    <tr>
                        <td><code>robots.txt</code></td>
                        <td>( / )</td>
                        <td><a href="admin.php?op=ConfigFiles_load&amp;files=robots"><i class="fa fa-edit fa-lg" title="' . adm_translate('Editer') . '" data-bs-toggle="tooltip"></i></a></td>
                    </tr>
                    <tr>
                        <td><code>humans.txt</code></td>
                        <td>( / )</td>
                        <td><a href="admin.php?op=ConfigFiles_load&amp;files=humans"><i class="fa fa-edit fa-lg" title="' . adm_translate('Editer') . '" data-bs-toggle="tooltip"></i></a></td>
                    </tr>
                </tbody>
            </table>';
        } else {
            echo '<hr />
            <h3 class="my-3">' . adm_translate('Modification de') . ' : <span class="text-body-secondary">' . $files . '</span></h3>
            <form action="admin.php?op=ConfigFiles_save" method="post">
                <code><textarea class="form-control" name="Xtxt" rows="20" cols="70">';

            echo htmlspecialchars($contents, ENT_COMPAT | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8');

            echo '</textarea></code>
                <input type="hidden" name="Xfiles" value="' . $files . '" />
                <div class="mb-3 mt-3">
                    <button class="btn btn-primary" type="submit" name="confirm">' . adm_translate('Sauver les modifications') . '</button> 
                    <button href="admin.php?op=ConfigFiles" class="btn btn-secondary">' . adm_translate('Abandonner') . '</button>
                </div>
            </form>
            ';
        }

        Validation::adminFoot('', '', '', '');
    }

    public function configFilesSave($Xtxt, $Xfiles)
    {
        if ($Xfiles == 'header_before') {
            $fp = fopen('themes/base/bootstrap/header_before.php', 'w');
            fputs($fp, stripslashes($Xtxt));
            fclose($fp);
        } elseif ($Xfiles == 'header_head') {
            $fp = fopen('themes/base/bootstrap/header_head.php', 'w');
            fputs($fp, stripslashes($Xtxt));
            fclose($fp);
        } elseif ($Xfiles == 'body_onload') {
            $fp = fopen('themes/base/bootstrap/body_onload.php', 'w');
            fputs($fp, stripslashes($Xtxt));
            fclose($fp);
        } elseif ($Xfiles == 'header_after') {
            $fp = fopen('themes/base/bootstrap/header_after.php', 'w');
            fputs($fp, stripslashes($Xtxt));
            fclose($fp);
        } elseif ($Xfiles == 'footer_before') {
            $fp = fopen('themes/base/bootstrap/footer_before.php', 'w');
            fputs($fp, stripslashes($Xtxt));
            fclose($fp);
        } elseif ($Xfiles == 'footer_after') {
            $fp = fopen('themes/base/bootstrap/footer_after.php', 'w');
            fputs($fp, stripslashes($Xtxt));
            fclose($fp);
        } elseif ($Xfiles == 'new_user') {
            $fp = fopen('themes/base/bootstrap/new_user.php', 'w');
            fputs($fp, stripslashes($Xtxt));
            fclose($fp);
        } elseif ($Xfiles == 'user') {
            $fp = fopen('themes/base/bootstrap/user.php', 'w');
            fputs($fp, stripslashes($Xtxt));
            fclose($fp);
        } elseif ($Xfiles == 'cache.config') {
            $fp = fopen('config/cache.config.php', 'w');
            fputs($fp, stripslashes($Xtxt));
            fclose($fp);
        } elseif ($Xfiles == 'robots') {
            $fp = fopen('robots.txt', 'w');
            fputs($fp, stripslashes($Xtxt));
            fclose($fp);
        } elseif ($Xfiles == 'humans') {
            $fp = fopen('humans.txt', 'w');
            fputs($fp, stripslashes($Xtxt));
            fclose($fp);
        }

        global $aid;
        Log::ecrireLog('security', sprintf('SaveConfigFile(%s) by AID : %s', $Xfiles, $aid), '');

        header('location: admin.php?op=ConfigFiles');
    }

    public function deleteConfigFile($fileX)
    {
        //global $hlpfile, $f_meta_nom, $f_titre, $adminimg;

        //include 'header.php';

        //GraphicAdmin($hlpfile);
        //adminhead($f_meta_nom, $f_titre, $adminimg);

        echo '<div class="alert alert-danger" role="alert">
            <p><strong>' . adm_translate('Supprimer le fichier') . ' ' . $fileX . ' ? </strong><br /><br /><a class="btn btn-danger btn-sm" href="admin.php?op=ConfigFiles_delete&amp;file=' . $fileX . '">' . adm_translate('Oui') . '</a>&nbsp;&nbsp;<a class="btn btn-secondary btn-sm" href="admin.php?op=ConfigFiles" >' . adm_translate('Non') . '</a></p>
        </div>';

        Validation::adminFoot('', '', '', '');
    }

    public function configFilesDelete($modele)
    {
        if ($modele == 'header_before') {
            @unlink('themes/base/bootstrap/header_before.php');
        } elseif ($modele == 'header_head') {
            @unlink('themes/base/bootstrap/header_head.php');
        } elseif ($modele == 'body_onload') {
            @unlink('themes/base/bootstrap/body_onload.php');
        } elseif ($modele == 'header_after') {
            @unlink('themes/base/bootstrap/header_after.php');
        } elseif ($modele == 'footer_before') {
            @unlink('themes/base/bootstrap/footer_before.php');
        } elseif ($modele == 'footer_after') {
            @unlink('themes/base/bootstrap/footer_after.php');
        } elseif ($modele == 'new_user') {
            @unlink('themes/base/bootstrap/new_user.php');
        } elseif ($modele == 'user') {
            @unlink('themes/base/bootstrap/user.php');
        }

        global $aid;
        Log::ecrireLog('security', sprintf('DeleteConfigFile(%s) by AID : %s', $modele, $aid), '');

        header('location: admin.php?op=ConfigFiles');
    }

    public function copySample($fileX)
    {
        //global $hlpfile, $f_meta_nom, $f_titre, $adminimg, $header;

        //if ($header != 1) {
        //    include 'header.php';
        //}

        //GraphicAdmin($hlpfile);
        //adminhead($f_meta_nom, $f_titre, $adminimg);

        echo '<hr />
        <div class="card card-body">
            <p>' . adm_translate('Créer le fichier en utilisant le modèle') . ' ? <br /><br /><a class="btn btn-primary" href="admin.php?op=ConfigFiles_create&amp;modele=' . $fileX . '" >' . adm_translate('Oui') . '</a>&nbsp;&nbsp;<a class="btn btn-secondary" href="admin.php?op=ConfigFiles" >' . adm_translate('Non') . '</a></p>
        </div>';

        Validation::adminFoot('', '', '', '');
    }

    public function configFilesCreate($modele)
    {
        @umask(0000);

        if ($modele == 'header_before') {
            @copy('themes/base/bootstrap/stub/sample.header_before.php', 'themes/base/bootstrap/header_before.php');
            @chmod('themes/base/bootstrap/header_before.php', 0766);
        } elseif ($modele == 'header_head') {
            @copy('themes/base/bootstrap/stub/sample.header_head.php', 'themes/base/bootstrap/header_head.php');
            @chmod('themes/base/bootstrap/header_head.php', 0766);
        } elseif ($modele == 'body_onload') {
            @copy('themes/base/bootstrap/stub/sample.body_onload.php', 'themes/base/bootstrap/body_onload.php');
            @chmod('themes/base/bootstrap/body_onload.php', 0766);
        } elseif ($modele == 'header_after') {
            @copy('themes/base/bootstrap/stub/sample.header_after.php', 'themes/base/bootstrap/header_after.php');
            @chmod('themes/base/bootstrap/header_after.php', 0766);
        } elseif ($modele == 'footer_before') {
            copy('themes/base/bootstrap/stub/sample.footer_before.php', 'themes/base/bootstrap/footer_before.php');
            chmod('themes/base/bootstrap/footer_before.php', 0766);
        } elseif ($modele == 'footer_after') {
            @copy('themes/base/bootstrap/stub/sample.footer_after.php', 'themes/base/bootstrap/footer_after.php');
            @chmod('themes/base/bootstrap/footer_after.php', 0766);
        } elseif ($modele == 'new_user') {
            @copy('themes/base/bootstrap/stub/sample.new_user.php', 'themes/base/bootstrap/new_user.php');
            @chmod('themes/base/bootstrap/new_user.php', 0766);
        } elseif ($modele == 'user') {
            @copy('themes/base/bootstrap/stub/sample.user.php', 'themes/base/bootstrap/user.php');
            @chmod('themes/base/bootstrap/user.php', 0766);
        }

        global $aid;
        Log::ecrireLog('security', sprintf('CreateConfigFile(%s) by AID : %s', $modele, $aid), '');

        header('location: admin.php?op=ConfigFiles');
    }

}
