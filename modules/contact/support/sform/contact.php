<?php

/************************************************************************/
/* SFORM Extender for NPDS Contact Example .                            */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2024 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/
/* Dont modify this file if you dont know what you make                 */
/************************************************************************/

use App\Library\Sform\Sform;

global $ModPath, $ModStart;

global $m;
$m = new Sform();

$m->addFormTitle('contact');

$m->addFormId('formcontact');

$m->addFormMethod('post');

$m->addFormCheck('false');

$m->addUrl('modules.php');

$m->addField('ModStart', '', $ModStart, 'hidden', false);

$m->addField('ModPath', '', $ModPath, 'hidden', false);

$m->addSubmitValue('subok');

$m->addField('subok', '', 'Submit', 'hidden', false);

include 'modules/' . $ModPath . '/support/sform/formulaire.php';

adminFoot('fv', '', 'var formulid = ["' . $m->form_id . '"];', '1');

// Manage the <form>
switch ($subok) {

    case 'Submit':

        settype($message, 'string');
        settype($sformret, 'string');

        if (!$sformret) {
            $m->makeResponse();

            //anti_spambot
            if (!reponseSpambot($asb_question, $asb_reponse, $message)) {
                ecrireLog('security', 'Contact', '');

                $subok = '';
            } else {
                $message = $m->affResponse('', 'not_echo', '');

                global $notify_email;
                sendEmail($notify_email, 'Contact site', affLangue($message), '', '', 'html', '');

                echo '<div class="alert alert-success">
                ' . affLangue("[french]Votre demande est prise en compte. Nous y r&eacute;pondrons au plus vite[/french][english]Your request is taken into account. We will answer it as fast as possible.[/english][chinese]&#24744;&#30340;&#35831;&#27714;&#24050;&#34987;&#32771;&#34385;&#22312;&#20869;&#12290; &#25105;&#20204;&#20250;&#23613;&#24555;&#22238;&#22797;[/chinese][spanish]Su solicitud es tenida en cuenta. Le responderemos lo m&aacute;s r&aacute;pido posible.[/spanish][german]Ihre Anfrage wird ber&uuml;cksichtigt. Wir werden so schnell wie m&ouml;glich antworten[/german]") . '
                </div>';
                break;
            }
        } else {
            $subok = '';
        }

    default:
        echo affLangue($m->printForm(''));
        break;
}
