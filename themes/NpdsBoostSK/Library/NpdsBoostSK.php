<?php

namespace Themes\NpdsBoostSK\Library;

use Npds\Config\Config;
use App\Library\Theme\Theme;
use App\Support\Facades\Auth;
use App\Support\Facades\Block;
use Npds\Support\Facades\View;
use App\Support\Facades\Language;


class NpdsBoostSK
{

    /**
     * Instance singleton de la classe.
     *
     * @var self|null
     */
    protected static ?self $instance = null;

    /**
     * Instance du thème.
     *
     * @var Theme
     */
    protected Theme $theme;

    /**
     * Nombre de messages privés non lus.
     *
     * @var int
     */
    protected int $nbmes = 0;


    /**
     * Constructeur.
     *
     * Initialise l'instance du thème.
     */
    public function __construct()
    {
        $this->theme = Theme::getInstance();
    }

    /**
     * Retourne l'instance singleton de la classe.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }
    
        return static::$instance = new static();
    }

    /**
     * Génère la structure HTML des blocs de gauche selon le type de page.
     *
     * @param string $pdst Type de page ou disposition des colonnes.
     *
     * @return void
     */
    public function leftBlock(string $pdst): void
    {
        $moreclass = Config::get('theme_npdsboostsk.theme.moreclass.left');

        echo '<div id="corps" class="container-fluid n-hyphenate">
            <div class="row g-3">';

        switch ($pdst) {

            case '-1':
                echo '<div id="col_princ" class="col-12">';
                break;

            case '1':
                $this->theme->colsyst('#col_LB');

                echo '<div id="col_LB" class="collapse show col-lg-3">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

                Block::leftBlocks($moreclass);

                echo '</div>
                    </div>
                <div id="col_princ" class="col-lg-6">';
                break;

            case '2':
            case '6':
                echo '<div id="col_princ" class="col-lg-9">';
                break;

            case '3':
                $this->theme->colsyst('#col_LB');

                echo '<div id="col_LB" class="collapse show col-lg-3">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

                Block::leftBlocks($moreclass);

                echo '</div>
                </div>';

                $this->theme->colsyst('#col_RB');

                echo '<div id="col_RB" class="collapse show col-lg-3">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

                Block::rightBlocks($moreclass);

                echo '</div>
                    </div>
                <div id="col_princ" class="col-lg-6">';
                break;

            case '4':
                echo '<div id="col_princ" class="col-lg-6">';
                break;

            case '5':
                $this->theme->colsyst('#col_RB');

                echo '<div id="col_RB" class="collapse show col-lg-3">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

                Block::rightBlocks($moreclass);

                echo '</div>
                    </div>
                    <div id="col_princ" class="col-lg-9">';
                break;

            default:
                $this->theme->colsyst('#col_LB');

                echo '<div id="col_LB" class="collapse show col-lg-3">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

                Block::leftBlocks($moreclass);

                echo '</div>
                    </div>
                <div id="col_princ" class="col-lg-9">';
                break;
        }
    }

    /**
     * Génère la structure HTML des blocs de droite selon le type de page.
     *
     * @param string $pdst Type de page ou disposition des colonnes.
     *
     * @return self
     */
    public function rightBlock(string $pdst): self
    {
        $moreclass = Config::get('theme_npdsboostsk.theme.moreclass.right');

        switch ($pdst) {

            case '-1':
            case '3':
            case '5':
                echo '</div>
                        </div>
                    </div>';
                break;

            case '1':
            case '2':
                echo '</div>';

                $this->theme->colsyst('#col_RB');

                echo '<div id="col_RB" class="collapse show col-lg-3 ">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

                Block::rightBlocks($moreclass);

                echo '</div>
                        </div>
                    </div>
                </div>';
                break;

            case '4':
                echo '</div>';

                $this->theme->colsyst('#col_LB');

                echo '<div id="col_LB" class="collapse show col-lg-3">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

                Block::leftBlocks($moreclass);

                echo '</div>
                </div>';

                $this->theme->colsyst('#col_RB');

                echo '<div id="col_RB" class="collapse show col-lg-3">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

                Block::rightBlocks($moreclass);

                echo '</div>
                        </div>
                    </div>
                </div>';
                break;

            case '6':
                echo '</div>';

                $this->theme->colsyst('#col_LB');

                echo '<div id="col_LB" class="collapse show col-lg-3">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

                Block::leftBlocks($moreclass);

                echo '</div>
                        </div>
                    </div>
                </div>';
                break;

            default:
                echo '</div>
                    </div>
                </div>';
                break;
        } 
        
        return $this;
    }

    /**
     * Génère l'en-tête de la page.
     *
     * @return self
     */
    public function header(): self 
    {
        // faire listener ou middleware
        //refererUpdate();

        // faire listener ou middleware
        //counterUpdate();

        if (View::exists('Themes/NpdsBoostSK::Bootstrap/Hody_onload.php')) {
            $onload_init = ' onload="init();"';
        } else {
            $onload_init = '';
        }

        $ContainerGlobal = Config::get('theme_npdsboostsk.theme.ContainerGlobal.header');

        $theme_darkness = Config::get('theme_npdsboostsk.theme.theme_darkness', config('theme.theme_darkness'));

        if (!$ContainerGlobal) {
            echo '<body' . $onload_init . ' class="body" data-bs-theme="' . $theme_darkness . '">';
        } else {
            echo '<body' . $onload_init . ' data-bs-theme="' . $theme_darkness . '">';
            echo $ContainerGlobal;
        }

        $Start_Page = str_replace('/', '', Config::get('app.Start_Page'));

        // landing page
        if (stristr($_SERVER['REQUEST_URI'], $Start_Page) && View::exists('Themes/NpdsBoostSK::Partials/Header/HeaderLanding')) {
            $Xcontent = View::make('Themes/NpdsBoostSK::Partials/Header/HeaderLanding');
        } else {
            $Xcontent = View::make('Themes/NpdsBoostSK::Partials/Header/Header');
        }

        //echo Metalang::metaLang(Language::affLangue($Xcontent));
        echo Language::affLangue($Xcontent);

        if (View::exists($theme_file = 'Themes/NpdsBoostSK::Bootstrap/Header_after')) {
            echo View::make($theme_file);
        }

        return $this;
    }

    /**
     * Génère le pied de page.
     *
     * @return void
     */
    public function footer(): void
    {
        if (View::exists($theme_file = 'Themes/NpdsBoostSK::Bootstrap/Footer_before')) {
            echo View::make($theme_file);
        }

        $Xcontent = View::make('Themes/NpdsBoostSK::Partials/Footer/Footer');

        $ContainerGlobal = Config::get('theme_npdsboostsk.theme.ContainerGlobal.footrer');

        if (! empty($ContainerGlobal)) {
            $Xcontent .= $ContainerGlobal;
        }

        //echo Metalang::metaLang(Language::affLangue($Xcontent));
        echo Language::affLangue($Xcontent);

        if (View::exists($theme_file = 'Bootstrap/Footer_after')) {
            echo View::make($theme_file);
        }

        if (View::exists($theme_file = 'Themes/NpdsBoostSK::Bootstrap/Footer_after')) {
            echo View::make($theme_file);
        }
    }

    /**
     * Génère le bouton de connexion/déconnexion.
     *
     * @return string HTML du bouton.
     */
    public function boiteConnection(): string
    {
        if (Auth::autorisation(-1)) {
            $btn_con = '<a class="dropdown-item" href="user.php">
                <i class="fas fa-sign-in-alt fa-lg me-2 align-middle"></i>' . translate("Connexion") . '
            </a>';
        } elseif (Auth::autorisation(1)) {
            $btn_con = '<a class="dropdown-item" href="user.php?op=logout">
                <i class="fas fa-sign-out-alt fa-lg text-danger me-2"></i>' . translate("Déconnexion") . '
            </a>';
        }

        return $btn_con;
    }

    /**
     * Génère le menu utilisateur avec ses options.
     *
     * @return string|null HTML du menu ou null si non autorisé.
     */
    public function menuUser(): ?string
    {
        global $cookie;

        if (Auth::autorisation(1)) {
            list($nbmes) = sql_fetch_row(sql_query("SELECT COUNT(*) 
                                                    FROM " . sql_prefix('priv_msgs') . " 
                                                    WHERE to_userid='" . $cookie[0] . "' 
                                                    AND read_msg='0'"));

            $this->nbmes = $nbmes;

            $cl = $nbmes > 0 ? ' faa-shake animated ' : '';

            return '<li><a class="dropdown-item" href="user.php?op=edituser" title="' . translate("Vous") . '"  ><i class="fa fa-user-edit fa-lg me-2"></i>' . translate("Vous") . '</a></li>
                <li><a class="dropdown-item" href="user.php?op=editjournal" title="' . translate("Editer votre journal") . '" ><i class="fa fa-edit fa-lg me-2"></i>' . translate("Journal") . '</a></li>
                <li><a class="dropdown-item" href="user.php?op=edithome" title="' . translate("Editer votre page principale") . '" ><i class="fa fa-edit fa-lg me-2 "></i>' . translate("Page") . '</a></li>
                <li><a class="dropdown-item" href="user.php?op=chgtheme" title="' . translate("Changer le thème") . '" ><i class="fa fa-paint-brush fa-lg me-2"></i>' . translate("Thème") . '</a></li>
                <li><a class="dropdown-item" href="modules.php?ModPath=reseaux-sociaux&amp;ModStart=reseaux-sociaux" title="' . translate("Réseaux sociaux") . '" ><i class="fa fa-share-alt-square fa-lg me-2"></i>' . translate("Réseaux sociaux") . '</a></li>
                <li><a class="dropdown-item" href="viewpmsg.php" title="' . translate("Message personnel") . '" ><i class="fa fa-envelope fa-lg me-2 ' . $cl . '"></i>' . translate("Message") . '</a></li>';
        }

        return null;
    }

    /**
     * Génère l'icône du messager avec le nombre de messages non lus.
     *
     * @return string|null HTML du lien ou null si pas de messages.
     */
    public function boiteMessenger(): ?string
    {
        if (Auth::autorisation(1) && $this->nbmes > 0) {
            return '<li class="nav-item">
                <a class="nav-link" href="viewpmsg.php">
                    <i class="fa fa-envelope fs-4 faa-shake animated" title="' 
                        . translate("Message personnel") 
                        . ' <span class=\'badge rounded-pill bg-danger ms-2\'>' 
                        . $this->nbmes 
                        . '</span>" data-bs-html="true" data-bs-toggle="tooltip" data-bs-placement="right"></i>
                </a>
            </li>';
        }

        return null;
    }

    /**
     * Génère l'avatar de l'utilisateur.
     *
     * @return string HTML de l'avatar.
     */
    public function userAvatar(): string
    {
        global $cookie;

        if (Auth::autorisation(-1)) {
            $ava = '<a class="dropdown-item" href="user.php"><i class="fa fa-user fa-3x text-body-secondary"></i></a>';
        } elseif (Auth::autorisation(1)) {

            $username = $cookie[1];

            list($user_avatar) = sql_fetch_row(sql_query("SELECT user_avatar 
                                                        FROM " . sql_prefix('users') . " 
                                                        WHERE uname='" . $username . "'"));

            if (!$user_avatar) {
                $imgtmp = 'images/forum/avatar/blank.gif';
            } else if (stristr($user_avatar, 'users_private')) {
                $imgtmp = $user_avatar;
            } else {
                if ($ibid = $this->theme->themeImage('forum/avatar/' . $user_avatar)) {
                    $imgtmp = $ibid;
                } else {
                    $imgtmp = 'images/forum/avatar/' . $user_avatar;
                }

                if (!file_exists($imgtmp)) {
                    $imgtmp = 'images/forum/avatar/blank.gif';
                }
            }

            $ava = '<a class="dropdown-item" href="user.php" >
                <img src="' . asset_url($imgtmp) . '" class="n-ava-64" alt="avatar" title="' . translate("Votre compte") . '" data-bs-toggle="tooltip" data-bs-placement="right" />
                </a><li class="dropdown-divider"></li>';
        } 
        
        return $ava;
    }

    /**
     * Retourne le nom d'utilisateur actuel.
     *
     * @return string|null Nom d'utilisateur ou null si non connecté.
     */
    public function userUsername(): ?string
    {
        global $cookie, $user;

        if (!empty($user) && isset($cookie[1])) {
            return $cookie[1];
        }

        return null;
    }

    /**
     * Génère le lien vers l'administration si l'utilisateur est admin.
     *
     * @return string|null HTML du lien ou null si non autorisé.
     */
    public function adminLink(): ?string
    {
        if (Auth::autorisation(-127)) {
            return '<div class="d-flex float-end">
                <a href="admin.php" title="[french]Administration[/french][english]Administration[/english][chinese]&#31649;&#29702;[/chinese][spanish]Administraci&oacute;n[/spanish][german]Verwaltung[/german]" data-bs-toggle="tooltip" data-bs-placement="left">
                    <i id="cogs" class="fa fa-cogs fa-lg"></i>
                </a>
            </div>';
        }

        return null;
    }

}