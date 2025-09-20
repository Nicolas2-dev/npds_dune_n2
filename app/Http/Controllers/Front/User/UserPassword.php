<?php

namespace App\Http\Controllers\Front\User;

use App\Support\Facades\Log;
use App\Support\Security\Hack;
use App\Support\Facades\Mailer;
use App\Library\User\UserMessage;
use App\Support\Facades\Password;
use Npds\Support\Facades\Request;
use App\Support\Facades\Encrypter;
use App\Support\Facades\Validation;
use App\Http\Controllers\Core\FrontBaseController;


class UserPassword extends FrontBaseController
{

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        /*
        switch ($op) {

            case 'forgetpassword':
                $this->ForgetPassword();
                break;

            case 'mailpasswd':
                if ($uname != '' and $code != '') {
                    if (strlen($code) >= $minpass) {
                        $this->mail_password($uname, $code);
                    } else {
                        $this->message_error("<i class=\"fa fa-exclamation\"></i>&nbsp;" . translate('Mot de passe erroné, refaites un essai.') . "<br /><br />", "");
                    }
                } else {
                    $this->main($user);
                }
                break;

            case 'validpasswd':
                if ($code != '') {
                    $this->valid_password($code);
                } else {
                    $this->main($user);
                }
                break;

            case 'updatepasswd':
                if ($code != '' and $passwd != '') {
                    $this->update_password($code, $passwd);
                } else {
                    $this->main($user);
                }
                break;
        }
        */

        parent::initialize();
    }

    function ForgetPassword()
    {
        //include 'header.php';

        echo '<h2 class="mb-3">' . translate('Utilisateur') . '</h2>
        <div class="card card-body">
            <div class="alert alert-danger lead"><i class="fa fa-exclamation me-2"></i>' . translate('Vous avez perdu votre mot de passe ?') . '</div>
            <div class="alert alert-success"><i class="fa fa-exclamation me-2"></i>' . translate('Pas de problème. Saisissez votre identifiant et le nouveau mot de passe que vous souhaitez utiliser puis cliquez sur envoyer pour recevoir un Email de confirmation.') . '</div>
            <form id="forgetpassword" action="user.php" method="post">
                <div class="row g-2">
                    <div class="col-sm-6 ">
                    <div class="mb-3 form-floating">
                        <input type="text" class="form-control" name="uname" id="inputuser" placeholder="' . translate('Identifiant') . '" required="required" />
                        <label for="inputuser">' . translate('Identifiant') . '</label>
                    </div>
                    </div>
                    <div class="col-sm-6">
                    <div class="mb-3 form-floating">
                        <input type="password" class="form-control" name="code" id="inputpassuser" placeholder="' . translate('Mot de passe') . '" required="required" />
                        <label for="inputpassuser">' . translate('Mot de passe') . '</label>
                    </div>
                    <div class="progress" style="height: 0.4rem;">
                        <div id="passwordMeter_cont" class="progress-bar bg-danger" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                    </div>
                    </div>
                </div>
                <input type="hidden" name="op" value="mailpasswd" />
                <input class="btn btn-primary btn-lg my-3" type="submit" value ="' . translate('Envoyer') . '" />
            </form>
        </div>';

        $fv_parametres = '
            code: {
                validators: {
                    checkPassword: {
                    message: "Le mot de passe est trop simple."
                    },
                }
            },';

        $arg1 = 'var formulid = ["forgetpassword"];';

        Validation::adminFoot('fv', $fv_parametres, $arg1, 'foo');
    }

    function mail_password($uname, $code)
    {
        global $sitename, $nuke_url;

        $uname = Hack::removeHack(stripslashes(htmlspecialchars(urldecode($uname), ENT_QUOTES, 'UTF-8')));

        $result = sql_query("SELECT uname, email, pass 
                            FROM " . sql_prefix('users') . " 
                            WHERE uname='$uname'");

        $tmp_result = sql_fetch_row($result);

        if (!$tmp_result) {
            UserMessage::error(translate('Désolé, aucune information correspondante pour cet utlilisateur n\'a été trouvée') . "<br /><br />", '');
        } else {
            $host_name = Request::getip();

            list($uname, $email, $pass) = $tmp_result;

            // On envoie une URL avec dans le contenu : username, email, le MD5 du passwd retenu et le timestamp
            $url = "$nuke_url/user.php?op=validpasswd&code=" . urlencode(Encrypter::encrypt($uname) . "#fpwd#" . Encrypter::encryptK($email . "#fpwd#" . $code . "#fpwd#" . time(), $pass));

            $message = translate('Le compte utilisateur') . ' ' . $uname . ' ' . translate('at') . ' ' . $sitename . ' ' . translate('est associé à votre Email.') . "\n\n";
            $message .= translate('Un utilisateur web ayant l\'adresse IP ') . " $host_name " . translate('vient de demander une confirmation pour changer de mot de passe.') . "\n\n" . translate('Votre url de confirmation est :') . " <a href=\"$url\">$url</a> \n\n" . translate('Si vous n\'avez rien demandé, ne vous inquiétez pas. Effacez juste ce Email. ') . "\n\n";

            include 'config/signat.php';

            $subject = translate('Confirmation du code pour') . ' ' . $uname;

            Mailer::sendEmail($email, $subject, $message, '', true, 'html', '');

            UserMessage::pass('<div class="alert alert-success lead text-center"><i class="fa fa-exclamation"></i>&nbsp;' . translate('Confirmation du code pour') . ' ' . $uname . ' ' . translate('envoyée par courrier.') . '</div>');

            Log::ecrireLog('security', 'Lost_password_request : ' . $uname, '');
        }
    }

    function valid_password($code)
    {
        $ibid = explode("#fpwd#", $code);

        $result = sql_query("SELECT email, pass 
                            FROM " . sql_prefix('users') . " 
                            WHERE uname='" . Encrypter::decrypt($ibid[0]) . "'");

        list($email, $pass) = sql_fetch_row($result);

        if ($email != '') {
            $ibid = explode('#fpwd#', Encrypter::decryptK($ibid[1], $pass));

            if ($email == $ibid[0]) {
                //include 'header.php';

                echo '<p class="lead">' . translate('Vous avez perdu votre mot de passe ?') . '</p>
                <div class="card border rounded p-3">
                    <div class="row">
                        <div class="col-sm-7">
                        <div class="blockquote">' . translate('Pour valider votre nouveau mot de passe, merci de le re-saisir.') . '<br />' . translate('Votre mot de passe est : ') . ' <strong>' . $ibid[1] . '</strong></div>
                        </div>
                        <div class="col-sm-5">
                        <form id="lostpassword" action="user.php" method="post">
                            <div class="mb-3 row">
                                <label class="col-form-label col-sm-12" for="passwd">' . translate('Mot de passe') . '</label>
                                <div class="col-sm-12">
                                    <input type="password" class="form-control" name="passwd" placeholder="' . $ibid[1] . '" required="required" />
                                </div>
                            </div>
                            <input type="hidden" name="op" value="updatepasswd" />
                            <input type="hidden" name="code" value="' . $code . '" />
                            <div class="mb-3 row">
                                <div class="col-sm-12">
                                    <input class="btn btn-primary" type="submit" value="' . translate('Valider') . '" />
                                </div>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>';

                //include 'footer.php';
            } else {
                UserMessage::pass('<div class="alert alert-danger lead text-center">' . translate('Erreur') . '</div>');

                Log::ecrireLog('security', 'Lost_password_valid NOK Mail not match : ' . $ibid[0], '');
            }
        } else {
            UserMessage::pass('<div class="alert alert-danger lead text-center">' . translate('Erreur') . '</div>');

            Log::ecrireLog('security', 'Lost_password_valid NOK Bad hash : ' . $ibid[0], '');
        }
    }

    function update_password($code, $passwd)
    {
        $ibid = explode("#fpwd#", $code);

        $uname = urlencode(Encrypter::decrypt($ibid[0]));

        $result = sql_query("SELECT email, pass 
                            FROM " . sql_prefix('users') . " 
                            WHERE uname='$uname'");

        list($email, $pass) = sql_fetch_row($result);

        if ($email != '') {
            $ibid = explode('#fpwd#', Encrypter::decryptK($ibid[1], $pass));

            if ($email == $ibid[0]) {

                // Le lien doit avoir été généré dans les 24H00
                if ((time() - $ibid[2]) < 86400) {

                    // le mot de passe est-il identique
                    if ($ibid[1] == $passwd) {

                        $AlgoCrypt  = PASSWORD_BCRYPT;
                        $min_ms     = 250;
                        $options    = ['cost' => Password::getOptimalBcryptCostParameter($ibid[1], $AlgoCrypt, $min_ms),];
                        $hashpass   = password_hash($ibid[1], $AlgoCrypt, $options);
                        $cryptpass  = crypt($ibid[1], $hashpass);

                        sql_query("UPDATE " . sql_prefix('users') . " 
                                SET pass='$cryptpass', hashkey='1' 
                                WHERE uname='$uname'");

                        UserMessage::pass('<div class="alert alert-success lead text-center"><a class="alert-link" href="user.php"><i class="fa fa-exclamation me-2"></i>' . translate('Mot de passe mis à jour. Merci de vous re-connecter') . '<i class="fas fa-sign-in-alt fa-lg ms-2"></i></a></div>');

                        Log::ecrireLog('security', 'Lost_password_update OK : ' . $uname, '');
                    } else {
                        UserMessage::pass('<div class="alert alert-danger lead text-center">' . translate('Erreur') . ' : ' . translate('Les mots de passe sont différents. Ils doivent être identiques.') . '</div>');

                        Log::ecrireLog('security', 'Lost_password_update Password not match : ' . $uname, '');
                    }
                } else {
                    UserMessage::pass('<div class="alert alert-danger lead text-center">' . translate('Erreur') . ' : ' . translate('Votre url de confirmation est expirée') . ' > 24 h</div>');

                    Log::ecrireLog('security', 'Lost_password_update NOK Time > 24H00 : ' . $uname, '');
                }
            } else {
                UserMessage::pass('<div class="alert alert-danger lead text-center">' . translate('Erreur : Email invalide') . '</div>');

                Log::ecrireLog('security', 'Lost_password_update NOK Mail not match : ' . $uname, '');
            }
        } else {
            UserMessage::pass('<div class="alert alert-danger lead text-center">' . translate('Erreur') . '</div>');

            Log::ecrireLog('security', 'Lost_password_update NOK Empty Mail or bad user : ' . $uname, '');
        }
    }

}
