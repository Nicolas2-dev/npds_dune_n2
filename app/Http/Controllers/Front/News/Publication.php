<?php

namespace App\Http\Controllers\Front\News;

use IntlDateFormatter;
use App\Support\Facades\Code;
use App\Support\Facades\Date;
use App\Support\Facades\News;
use App\Support\Facades\Language;
use App\Http\Controllers\Core\FrontBaseController;


class Publication extends FrontBaseController
{

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        parent::initialize();
    }

    function code_aff($subject, $story, $bodytext, $notes)
    {
        global $local_user_language;

        $subjectX   = Code::affCode(Language::previewLocalLangue($local_user_language, $subject));
        $storyX     = Code::affCode(Language::previewLocalLangue($local_user_language, $story));
        $bodytextX  = Code::affCode(Language::previewLocalLangue($local_user_language, $bodytext));
        $notesX     = Code::affCode(Language::previewLocalLangue($local_user_language, $notes));

        News::themePreview($subjectX, $storyX, $bodytextX, $notesX);
    }

    function publication($dd_pub, $fd_pub, $dh_pub, $fh_pub, $epur)
    {
        global $gmt;

        $today = getdate(time() + ((int)$gmt * 3600));

        settype($dd_pub, 'string');
        settype($fd_pub, 'string');
        settype($dh_pub, 'string');
        settype($fh_pub, 'string');

        if (!$dd_pub) {
            $dd_pub .= $today['year'] . '-';

            if ($today['mon'] < 10) {
                $dd_pub .= '0' . $today['mon'] . '-';
            } else {
                $dd_pub .= $today['mon'] . '-';
            }

            if ($today['mday'] < 10) {
                $dd_pub .= '0' . $today['mday'];
            } else {
                $dd_pub .= $today['mday'];
            }
        }

        if (!$fd_pub) {
            $fd_pub .= ($today['year'] + 99) . '-';

            if ($today['mon'] < 10) {
                $fd_pub .= '0' . $today['mon'] . '-';
            } else {
                $fd_pub .= $today['mon'] . '-';
            }

            if ($today['mday'] < 10) {
                $fd_pub .= '0' . $today['mday'];
            } else {
                $fd_pub .= $today['mday'];
            }
        }

        if (!$dh_pub) {
            if ($today['hours'] < 10) {
                $dh_pub .= '0' . $today['hours'] . ':';
            } else {
                $dh_pub .= $today['hours'] . ':';
            }

            if ($today['minutes'] < 10) {
                $dh_pub .= '0' . $today['minutes'];
            } else {
                $dh_pub .= $today['minutes'];
            }
        }

        if (!$fh_pub) {
            if ($today['hours'] < 10) {
                $fh_pub .= '0' . $today['hours'] . ':';
            } else {
                $fh_pub .= $today['hours'] . ':';
            }

            if ($today['minutes'] < 10) {
                $fh_pub .= '0' . $today['minutes'];
            } else {
                $fh_pub .= $today['minutes'];
            }
        }

        echo '<hr />
            <p class="small text-end">
            ' . Date::formatTimes(time(), IntlDateFormatter::FULL, IntlDateFormatter::SHORT) . '
            </p>';

        if ($dd_pub != -1 and $dh_pub != -1) {
            echo '<div class="row mb-3">
                    <div class="col-sm-5 mb-2">
                        <label class="form-label" for="dd_pub">' . translate('Date de publication') . '</label>
                        <input type="text" class="form-control flatpi" id="dd_pub" name="dd_pub" value="' . $dd_pub . '" />
                    </div>
                    <div class="col-sm-3 mb-2">
                        <label class="form-label" for="dh_pub">' . translate('Heure') . '</label>
                        <div class="input-group clockpicker">
                            <span class="input-group-text"><i class="far fa-clock fa-lg"></i></span>
                            <input type="text" class="form-control" placeholder="Heure" id="dh_pub" name="dh_pub" value="' . $dh_pub . '" />
                        </div>
                    </div>
                </div>';
        }

        echo '<div class="row mb-3">
                <div class="col-sm-5 mb-2">
                    <label class="form-label" for="fd_pub">' . translate('Date de fin de publication') . '</label>
                    <input type="text" class="form-control flatpi" id="fd_pub" name="fd_pub" value="' . $fd_pub . '" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label class="form-label" for="fh_pub">' . translate('Heure') . '</label>
                    <div class="input-group clockpicker">
                        <span class="input-group-text"><i class="far fa-clock fa-lg"></i></span>
                        <input type="text" class="form-control" placeholder="Heure" id="fh_pub" name="fh_pub" value="' . $fh_pub . '" />
                    </div>
                </div>
            </div>
            <script type="text/javascript" src="assets/shared/flatpickr/dist/flatpickr.min.js"></script>
            <script type="text/javascript" src="assets/shared/flatpickr/dist/l10n/' . Language::languageIso(1, '', '') . '.js"></script>
            <script type="text/javascript" src="assets/shared/clockpicker/bootstrap-clockpicker.min.js"></script>
            <script type="text/javascript">
            //<![CDATA[
                $(document).ready(function() {
                    $("<link>").appendTo("head").attr({type: "text/css", rel: "stylesheet",href: "assets/shared/flatpickr/dist/themes/npds.css"});
                    $("<link>").appendTo("head").attr({type: "text/css", rel: "stylesheet",href: "assets/shared/clock/bootstrap-clockpicker.min.css"});
                    $(".clockpicker").clockpicker({
                        placement: "bottom",
                        align: "top",
                        autoclose: "true"
                    });

                })
                const fp = flatpickr(".flatpi", {
                    altInput: true,
                    altFormat: "l j F Y",
                    dateFormat:"Y-m-d",
                    "locale": "' . Language::languageIso(1, '', '') . '",
                });
            //]]>
            </script>
            <div class="mb-3 row">
                <label class="col-form-label">' . translate('Epuration de la new à la fin de sa date de validité') . '</label>';

        $sel1 = '';
        $sel2 = '';

        if (!$epur) {
            $sel2 = 'checked="checked"';
        } else {
            $sel1 = 'checked="checked"';
        }

        echo '<div class="col-sm-8 my-2">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="epur_y" name="epur" value="1" ' . $sel1 . ' />
                        <label class="form-check-label" for="epur_y">' . translate('Oui') . '</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="epur_n" name="epur" value="0" ' . $sel2 . ' />
                        <label class="form-check-label" for="epur_n">' . translate('Non') . '</label>
                    </div>
                </div>
            </div>
            <hr />';
    }

}
