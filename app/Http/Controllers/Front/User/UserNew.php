<?php

namespace App\Http\Controllers\Front\User;

use App\Support\Facades\Log;
use App\Support\Facades\Date;
use App\Support\Facades\Spam;
use App\Support\Facades\Mailer;
use App\Library\User\UserMessage;
use App\Support\Facades\Language;
use App\Support\Facades\Metalang;
use App\Support\Facades\Password;
use App\Library\User\UserValidator;
use App\Support\Facades\Validation;
use App\Library\User\Traits\HiddenFormTrait;
use App\Http\Controllers\Core\FrontBaseController;


class UserNews extends FrontBaseController
{

    use HiddenFormTrait;

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        /*
        switch ($op) {

            case 'new user':
                // CheckBox
                settype($user_viewemail, 'integer');
                settype($user_lnl, 'integer');
                settype($pass, 'string');
                settype($vpass, 'string');

                $this->confirmNewUser($uname, $name, $email, $user_avatar, $user_occ, $user_from, $user_intrest, $user_sig, $user_viewemail, $pass, $vpass, $user_lnl, $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1);
                break;

            case 'finish':
                $this->finishNewUser($uname, $name, $email, $user_avatar, $user_occ, $user_from, $user_intrest, $user_sig, $user_viewemail, $pass, $user_lnl, $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1);
                break;

            case 'only_newuser':
                global $CloseRegUser;
                if ($CloseRegUser == 0) {
                    $this->Only_NewUser();
                } else {
                    include 'header.php';

                    if (file_exists('storage/static/closed.txt')) {
                        include 'storage/static/closed.txt';
                    }

                    include 'footer.php';
                }
                break;

        }
        */

        parent::initialize();
    }

    function Only_NewUser()
    {
        global $user, $memberpass;

        if (!$user) {
            global $smilies, $short_user, $memberpass;
            global $uname, $name, $email, $user_avatar, $user_occ, $user_from, $user_intrest, $user_sig, $user_viewemail, $pass, $vpass, $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1;

            //include 'header.php';

            $this->showimage();

            echo '<div>
            <h2 class="mb-3">' . translate('Utilisateur') . '</h2>
            <div class="card card-body mb-3">
                <h3>' . translate('Notes') . '</h3>
                <p>
                ' . translate('Les préférences de compte fonctionnent sur la base des cookies.') . ' ' . translate('Nous ne vendons ni ne communiquons vos informations personnelles à autrui.') . ' ' . translate('En tant qu\'utilisateur enregistré vous pouvez') . ' : 
                    <ul>
                        <li>' . translate('Poster des commentaires signés') . '</li>
                        <li>' . translate('Proposer des articles en votre nom') . '</li>
                        <li>' . translate('Disposer d\'un bloc que vous seul verrez dans le menu (pour spécialistes, nécessite du code html)') . '</li>
                        <li>' . translate('Télécharger un avatar personnel') . '</li>
                        <li>' . translate('Sélectionner le nombre de news que vous souhaitez voir apparaître sur la page principale.') . '</li>
                        <li>' . translate('Personnaliser les commentaires') . '</li>
                        <li>' . translate('Choisir un look différent pour le site (si plusieurs proposés)') . '</li>
                        <li>' . translate('Gérer d\'autres options et applications') . '</li>
                    </ul>
                </p>';

            if (!$memberpass) {
                echo '<div class="alert alert-success lead"><i class="fa fa-exclamation me-2"></i>' . translate('Le mot de passe vous sera envoyé à l\'adresse Email indiquée.') . '</div>';
            }

            echo '</div>
            <div class="card card-body mb-3">';

            include 'library/sform/extend-user/extend-user.php';

            echo '</div>';

            Validation::adminFoot('fv', $fv_parametres, $arg1, '');
        } else {
            header('location: user.php');
        }
    }

    function confirmNewUser($uname, $name, $email, $user_avatar, $user_occ, $user_from, $user_intrest, $user_sig, $user_viewemail, $pass, $vpass, $user_lnl, $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1)
    {
        global $smilies, $short_user, $minpass, $memberpass;

        $uname = strip_tags($uname);

        if ($user_viewemail != 1) {
            $user_viewemail = '0';
        }

        //$stop = $this->userCheck($uname, $email);

        if ($memberpass) {
            if ((isset($pass)) and ($pass != $vpass)) {
                $stop = '<i class="fa fa-exclamation me-2"></i>' . translate('Les mots de passe sont différents. Ils doivent être identiques.');
            } elseif (strlen($pass) < $minpass) {
                $stop = '<i class="fa fa-exclamation me-2"></i>' . translate('Désolé, votre mot de passe doit faire au moins') . ' <strong>' . $minpass . '</strong> ' . translate('caractères');
            }
        }

        //$stop = $this->userCheck($uname, $email);
        $stop = UserValidator::validateUser($uname, $email);

        if (!$stop) {
            //include 'header.php';

            echo '<h2>' . translate('Utilisateur') . '</h2>
            <hr />
            <h3 class="mb-3"><i class="fa fa-user me-2"></i>' . translate('Votre fiche d\'inscription') . '</h3>
            <div class="card">
                <div class="card-body">';

            include 'library/sform/extend-user/aff_extend-user.php';

            echo '</div>
            </div>';

            // HiddenFormTrait class 
            $this->hidden_form();

            global $charte;
            if (!$charte) {
                echo '<div class="alert alert-danger lead mt-3">
                        <i class="fa fa-exclamation me-2"></i>' . translate('Vous devez accepter la charte d\'utilisation du site') . '
                    </div>
                    <input type="hidden" name="op" value="only_newuser" />
                    <input class="btn btn-secondary mt-1" type="submit" value="' . translate('Retour en arrière') . '" />
                    </form>';
            } else {
                echo '<input type="hidden" name="op" value="finish" /><br />
                    <input class="btn btn-primary mt-2" type="submit" value="' . translate('Terminer') . '" />
                    </form>';
            }

            //include 'footer.php';
        } else {
            //$this->message_error($stop, 'new user');
            UserMessage::error($stop, 'new user');
        }
    }

    function finishNewUser($uname, $name, $email, $user_avatar, $user_occ, $user_from, $user_intrest, $user_sig, $user_viewemail, $pass, $user_lnl, $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1)
    {
        global $makepass, $adminmail, $sitename, $autoRegUser, $memberpass, $gmt, $NPDS_Key, $nuke_url;

        if (!isset($_SERVER['HTTP_REFERER'])) {
            Log::ecrireLog('security', 'Ghost form in user.php registration. => NO REFERER', '');

            Spam::logSpambot('', false);

            include 'admin/die.php';
            die();
        } else if ($_SERVER['HTTP_REFERER'] . $NPDS_Key !== $nuke_url . '/user.php' . $NPDS_Key) {
            Log::ecrireLog('security', 'Ghost form in user.php registration. => ' . $_SERVER['HTTP_REFERER'], '');

            Spam::logSpambot('', false);

            include 'admin/die.php';
            die();
        }

        $user_regdate = time() + ((int)$gmt * 3600);
        $stop = $this->userCheck($uname, $email);

        if (!$stop) {
            //include 'header.php';

            if (!$memberpass) {
                $makepass = $this->makepass();
            } else {
                $makepass = $pass;
            }

            $AlgoCrypt  = PASSWORD_BCRYPT;
            $min_ms     = 100;
            $options    = ['cost' => Password::getOptimalBcryptCostParameter($makepass, $AlgoCrypt, $min_ms)];
            $hashpass   = password_hash($makepass, $AlgoCrypt, $options);
            $cryptpass  = crypt($makepass, $hashpass);

            // $hashkey = 1; // ne sert a rien içi

            $result = sql_query("INSERT INTO " . sql_prefix('users') . " 
                                VALUES (NULL,'$name','$uname','$email','','','$user_avatar','$user_regdate','$user_occ','$user_from','$user_intrest','$user_sig','$user_viewemail','','','$cryptpass', '1', '10','','0','0','0','','0','','$Default_Theme+$Default_Skin','10','0','0','1','0','','','$user_lnl')");

            list($usr_id) = sql_fetch_row(sql_query("SELECT uid 
                                                    FROM " . sql_prefix('users') . " 
                                                    WHERE uname='$uname'"));

            $result = sql_query("INSERT INTO " . sql_prefix('users_extend') . " 
                                VALUES ('$usr_id', '$C1', '$C2', '$C3', '$C4', '$C5', '$C6', '$C7', '$C8', '$M1', '$M2', '$T1', '$T2', '$B1')");

            $attach = $user_sig ? 1 : 0;

            if (($autoRegUser == 1) or (!isset($autoRegUser))) {
                $result = sql_query("INSERT INTO " . sql_prefix('users_status') . " 
                                    VALUES ('$usr_id', '0', '$attach', '0', '1', '1', '')");
            } else {
                $result = sql_query("INSERT INTO " . sql_prefix('users_status') . " 
                                    VALUES ('$usr_id', '0', '$attach', '0', '1', '0', '')");
            }

            if ($result) {
                if ($memberpass) {
                    echo '<h2>' . translate('Utilisateur') . '</h2>
                    <hr />
                    <h2><i class="fa fa-user me-2"></i>' . translate('Inscription') . '</h2>
                    <p class="lead">' . translate('Votre mot de passe est : ') . '<strong>' . $makepass . '</strong></p>
                    <p class="lead">' . translate('Vous pourrez le modifier après vous être connecté sur') . ' : <br /><a href="user.php?op=login&amp;uname=' . $uname . '&amp;pass=' . urlencode($makepass) . '"><i class="fas fa-sign-in-alt fa-lg me-2"></i><strong>' . $sitename . '</strong></a></p>';

                    $message = translate('Bienvenue sur') . " $sitename !\n\n" . translate('Vous, ou quelqu\'un d\'autre, a utilisé votre Email identifiant votre compte') . " ($email) " . translate('pour enregistrer un compte sur') . " $sitename.\n\n" . translate('Informations sur l\'utilisateur :') . " : \n\n";

                    $message .= translate('ID utilisateur (pseudo)') . ' : ' . $uname . "\n" .
                        translate('Véritable adresse Email') . ' : ' . $email . "\n";

                    if ($name != '') {
                        $message .= translate('Votre véritable identité') . ' : ' . $name . "\n";
                    }

                    if ($user_from != '') {
                        $message .= translate('Votre situation géographique') . ' : ' . $user_from . "\n";
                    }

                    if ($user_occ != '') {
                        $message .= translate('Votre activité') . ' : ' . $user_occ . "\n";
                    }

                    if ($user_intrest != '') {
                        $message .= translate('Vos centres d\'intérêt') . ' : ' . $user_intrest . "\n";
                    }

                    if ($user_sig != '') {
                        $message .= translate('Signature') . ' : ' . $user_sig . "\n";
                    }

                    if (isset($C1) and $C1 != '') {
                        $message .= Language::affLangue('[french]Activit&#x00E9; professionnelle[/french][english]Professional activity[/english][spanish]Actividad profesional[/spanish][german]Berufliche T&#xE4;tigkeit[/german]') . ' : ' . $C1 . "\n";
                    }

                    if (isset($C2) and $C2 != '') {
                        $message .= Language::affLangue('[french]Code postal[/french][english]Postal code[/english][spanish]C&#xF3;digo postal[/spanish][german]Postleitzahl[/german]') . ' : ' . $C2 . "\n";
                    }

                    if (isset($T1) and $T1 != '') {
                        $message .= Language::affLangue('[french]Date de naissance[/french][english]Birth date[/english][spanish]Fecha de nacimiento[/spanish][german]Geburtsdatum[/german]') . ' : ' . $T1 . "\n";
                    }

                    $message .= "\n\n\n" . Language::affLangue("[french]Conform&eacute;ment aux articles 38 et suivants de la loi fran&ccedil;aise n&deg; 78-17 du 6 janvier 1978 relative &agrave; l'informatique, aux fichiers et aux libert&eacute;s, tout membre dispose d&rsquo; un droit d&rsquo;acc&egrave;s, peut obtenir communication, rectification et/ou suppression des informations le concernant.[/french][english]In accordance with Articles 38 et seq. Of the French law n &deg; 78-17 of January 6, 1978 relating to data processing, files and freedoms, any member has a right of access, can obtain communication, rectification and / or deletion of information about him.[/english][chinese]&#26681;&#25454;1978&#24180;1&#26376;6&#26085;&#20851;&#20110;&#25968;&#25454;&#22788;&#29702;&#65292;&#26723;&#26696;&#21644;&#33258;&#30001;&#30340;&#27861;&#22269;78-17&#21495;&#27861;&#24459;&#65292;&#20219;&#20309;&#25104;&#21592;&#37117;&#26377;&#26435;&#36827;&#20837;&#65292;&#21487;&#20197;&#33719;&#24471;&#36890;&#20449;&#65292;&#32416;&#27491;&#21644;/&#25110; &#21024;&#38500;&#26377;&#20851;&#20182;&#30340;&#20449;&#24687;&#12290;[/chinese][spanish]De conformidad con los art&iacute;culos 38 y siguientes de la ley francesa n &deg; 78-17 del 6 de enero de 1978, relativa al procesamiento de datos, archivos y libertades, cualquier miembro tiene derecho de acceso, puede obtener comunicaci&oacute;n, rectificaci&oacute;n y / o supresi&oacute;n de informaci&oacute;n sobre &eacute;l.[/spanish][german]Gem&auml;&szlig; den Artikeln 38 ff. Des franz&ouml;sischen Gesetzes Nr. 78-17 vom 6. Januar 1978 in Bezug auf Datenverarbeitung, Akten und Freiheiten hat jedes Mitglied ein Recht auf Zugang, kann Kommunikation, Berichtigung und / oder L&ouml;schung von Informationen &uuml;ber ihn.[/german]");
                    $message .= "\n\n\n" . Language::affLangue("[french]Ce message et les pi&egrave;ces jointes sont confidentiels et &eacute;tablis &agrave; l'attention exclusive de leur destinataire (aux adresses sp&eacute;cifiques auxquelles il a &eacute;t&eacute; adress&eacute;). Si vous n'&ecirc;tes pas le destinataire de ce message, vous devez imm&eacute;diatement en avertir l'exp&eacute;diteur et supprimer ce message et les pi&egrave;ces jointes de votre syst&egrave;me.[/french][english]This message and any attachments are confidential and intended to be received only by the addressee. If you are not the intended recipient, please notify immediately the sender by reply and delete the message and any attachments from your system.[/english][chinese]&#27492;&#28040;&#24687;&#21644;&#20219;&#20309;&#38468;&#20214;&#37117;&#26159;&#20445;&#23494;&#30340;&#65292;&#24182;&#19988;&#25171;&#31639;&#30001;&#25910;&#20214;&#20154;&#25509;&#25910;&#12290; &#22914;&#26524;&#24744;&#19981;&#26159;&#39044;&#26399;&#25910;&#20214;&#20154;&#65292;&#35831;&#31435;&#21363;&#36890;&#30693;&#21457;&#20214;&#20154;&#24182;&#22238;&#22797;&#37038;&#20214;&#21644;&#31995;&#32479;&#20013;&#30340;&#25152;&#26377;&#38468;&#20214;&#12290;[/chinese][spanish]Este mensaje y cualquier adjunto son confidenciales y est&aacute;n destinados a ser recibidos por el destinatario. Si no es el destinatario deseado, notif&iacute;quelo al remitente de inmediato y responda al mensaje y cualquier archivo adjunto de su sistema.[/spanish][german]Diese Nachricht und alle Anh&auml;nge sind vertraulich und sollen vom Empf&auml;nger empfangen werden. Wenn Sie nicht der beabsichtigte Empf&auml;nger sind, benachrichtigen Sie bitte sofort den Absender und antworten Sie auf die Nachricht und alle Anlagen von Ihrem System.[/german]") . "\n\n\n";

                    include 'config/signat.php';

                    $subject = html_entity_decode(translate('Inscription'), ENT_COMPAT | ENT_HTML401, 'UTF-8') . ' ' . $uname;

                    Mailer::sendEmail($email, $subject, $message, '', true, 'html', '');
                } else {
                    $message = translate('Bienvenue sur') . " $sitename !\n\n" . translate('Vous, ou quelqu\'un d\'autre, a utilisé votre Email identifiant votre compte') . " ($email) " . translate('pour enregistrer un compte sur') . " $sitename.\n\n" . translate('Informations sur l\'utilisateur :') . "\n" . translate('-Identifiant : ') . " $uname\n" . translate('-Mot de passe : ') . " $makepass\n\n";

                    include 'config/signat.php';

                    $subject = html_entity_decode(translate('Mot de passe utilisateur pour'), ENT_COMPAT | ENT_HTML401, 'UTF-8') . ' ' . $uname;

                    Mailer::sendEmail($email, $subject, $message, '', true, 'html', '');

                    echo '<h2>' . translate('Utilisateur') . '</h2>
                    <h2><i class="fa fa-user me-2"></i>Inscription</h2>
                    <div class="alert alert-success lead"><i class="fa fa-exclamation me-2"></i>' . translate('Vous êtes maintenant enregistré. Vous allez recevoir un code de confirmation dans votre boîte à lettres électronique.') . '</div>';
                }

                //------------------------------------------------
                if (file_exists('themes/base/bootstrap/new_user.php')) {
                    include 'themes/base/bootstrap/new_user.php';

                    $time = Date::getPartOfTime(time(), 'yyyy-MM-dd H:mm:ss');

                    $message = Metalang::metaLang(AddSlashes(str_replace("\n", "<br />", $message)));

                    $sql = "INSERT INTO " . sql_prefix('priv_msgs') . " (msg_image, subject, from_userid, to_userid, msg_time, msg_text) 
                            VALUES ('', '$sujet', '$emetteur_id', '$usr_id', '$time', '$message')";

                    sql_query($sql);
                }

                //------------------------------------------------
                $subject = html_entity_decode(translate('Inscription'), ENT_COMPAT | ENT_HTML401, 'UTF-8') . ' : ' . $sitename;

                Mailer::sendEmail(
                    $adminmail,
                    $subject,
                    "Infos :
                    Nom : $name
                    ID : $uname
                    Email : $email",
                    '',
                    false,
                    "text",
                    ''
                );
            }

            //include 'footer.php';
        } else {
            //$this->message_error($stop, 'finish');
            UserMessage::error($stop, 'finish');
        }
    }

}
