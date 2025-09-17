<?php

namespace App\Http\Controllers\Front\Page;

use App\Http\Controllers\Core\FrontBaseController;


class Pages extends FrontBaseController
{

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        parent::initialize();
    }

    public function index()
    {
        $pdst = $npds;

        $remp = '';

        //include 'header.php';

        echo '<div id="static_cont">';

        if (($op != '') && ($op)) {

            // Troll Control for security
            if (
                preg_match('#^[a-z0-9_\.-]#i', $op)
                && !stristr($op, '.*://')
                && !stristr($op, '..')
                && !stristr($op, '../')
                && !stristr($op, 'script')
                && !stristr($op, 'cookie')
                && !stristr($op, 'iframe')
                && !stristr($op, 'applet')
                && !stristr($op, 'object')
                && !stristr($op, 'meta')
            ) {

                if (file_exists('storage/static/' . $op)) {
                    if (!$metalang and !$nl) {
                        include 'storage/static/' . $op;
                    } else {
                        ob_start();
                        include 'storage/static/' . $op;
                        $remp = ob_get_contents();
                        ob_end_clean();

                        if ($metalang) {
                            $remp = Metalang::metaLang(Code::affCode(Language::affLangue($remp)));
                        }

                        if ($nl) {
                            $remp = nl2br(str_replace(' ', '&nbsp;', htmlentities($remp, ENT_QUOTES, 'UTF-8')));
                        }

                        echo $remp;
                    }

                    echo '<div class=" my-3"><a href="print.php?sid=static:' . $op . '&amp;metalang=' . $metalang . '&amp;nl=' . $nl . '" data-bs-toggle="tooltip" data-bs-placement="right" title="' . translate('Page spéciale pour impression') . '"><i class="fa fa-2x fa-print"></i></a></div>';

                    // Si vous voulez tracer les appels au pages statiques : 
                    // supprimer les // devant la ligne ci-dessous.
                    // Log::ecrireLog('security', 'static/'. $op, '');
                } else {
                    echo '<div class="alert alert-danger">' . translate('Merci d\'entrer l\'information en fonction des spécifications') . '</div>';
                }
            } else {
                echo '<div class="alert alert-danger">' . translate('Merci d\'entrer l\'information en fonction des spécifications') . '</div>';
            }
        }

        echo '</div>';

        //include 'footer.php';
    }

}
