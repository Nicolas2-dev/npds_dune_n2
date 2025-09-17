<?php

namespace App\Http\Controllers\Front\Friend;

use App\Http\Controllers\Core\FrontBaseController;


class Friend extends FrontBaseController
{

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        parent::initialize();
    }

    function FriendSend($sid, $archive)
    {
        settype($sid, 'integer');
        settype($archive, 'integer');

        $result = sql_query("SELECT title, aid 
                            FROM " . sql_prefix('stories') . " 
                            WHERE sid='$sid'");

        list($title, $aid) = sql_fetch_row($result);

        if (!$aid) {
            header('Location: index.php');
        }

        //include 'header.php';

        echo '<div class="card card-body">
        <h2><i class="fa fa-at fa-lg text-body-secondary"></i>&nbsp;' . translate('Envoi de l\'article à un ami') . '</h2>
        <hr />
        <p class="lead">' . translate('Vous allez envoyer cet article') . ' : <strong>' . Language::affLangue($title) . '</strong></p>
        <form id="friendsendstory" action="friend.php" method="post">
            <input type="hidden" name="sid" value="' . $sid . '" />';

        $yn = '';
        $ye = '';

        global $user;
        if ($user) {
            global $cookie;

            $result = sql_query("SELECT name, email 
                                FROM " . sql_prefix('users') . " 
                                WHERE uname='$cookie[1]'");

            list($yn, $ye) = sql_fetch_row($result);
        }

        echo '<div class="form-floating mb-3">
                <input type="text" class="form-control" id="fname" name="fname" maxlength="100" required="required" />
                <label for="fname">' . translate('Nom du destinataire') . '</label>
                <span class="help-block text-end"><span class="muted" id="countcar_fname"></span></span>
            </div>
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="fmail" name="fmail" maxlength="254" required="required" />
                <label for="fmail">' . translate('Email du destinataire') . '</label>
                <span class="help-block text-end"><span class="muted" id="countcar_fmail"></span></span>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="yname" name="yname" value="' . $yn . '" maxlength="100" required="required" />
                <label for="yname">' . translate('Votre nom') . '</label>
                <span class="help-block text-end"><span class="muted" id="countcar_yname"></span></span>
            </div>
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="ymail" name="ymail" value="' . $ye . '" maxlength="254" required="required" />
                <label for="ymail">' . translate('Votre Email') . '</label>
                <span class="help-block text-end"><span class="muted" id="countcar_ymail"></span></span>
            </div>';

        echo '' . Spam::questionSpambot();

        echo '<input type="hidden" name="archive" value="' . $archive . '" />
            <input type="hidden" name="op" value="SendStory" />
            <button type="submit" class="btn btn-primary" title="' . translate('Envoyer') . '"><i class="fa fa-lg fa-at"></i>&nbsp;' . translate('Envoyer') . '</button>
        </form>';

        $arg1 = 'var formulid = ["friendsendstory"];
            inpandfieldlen("yname",100);
            inpandfieldlen("ymail",254);
            inpandfieldlen("fname",100);
            inpandfieldlen("fmail",254);';

        Validation::adminFoot('fv', '', $arg1, '');
    }

    function SendStory($sid, $yname, $ymail, $fname, $fmail, $archive, $asb_question, $asb_reponse)
    {
        global $user;

        if (!$user) {
            //anti_spambot
            if (!Spam::reponseSpambot($asb_question, $asb_reponse, '')) {
                Log::ecrireLog('security', sprintf('Send-Story Anti-Spam : name=%s / mail=%s', $yname, $ymail), '');

                Url::redirectUrl('index.php');
                die();
            }
        }

        global $sitename, $nuke_url;

        settype($sid, 'integer');
        settype($archive, 'integer');

        $result2 = sql_query("SELECT title, time, topic 
                            FROM " . sql_prefix('stories') . " 
                            WHERE sid='$sid'");

        list($title, $time, $topic) = sql_fetch_row($result2);

        $result3 = sql_query("SELECT topictext 
                            FROM " . sql_prefix('topics') . " 
                            WHERE topicid='$topic'");

        list($topictext) = sql_fetch_row($result3);

        $subject = html_entity_decode(translate('Article intéressant sur'), ENT_COMPAT | ENT_HTML401, 'UTF-8') . " $sitename";
        $fname = Hack::removeHack($fname);

        $message = nl2br(
            translate('Bonjour') . " $fname :\n\n"
                . translate('Votre ami') . " $yname "
                . translate('a trouvé cet article intéressant et a souhaité vous l\'envoyer.') . "\n\n"
                . Language::affLangue($title) . "\n"
                . translate('Date :') . " $time\n"
                . translate('Sujet : ') . Language::affLangue($topictext) . "\n\n"
                . translate('L\'article') . " : <a href=\"$nuke_url/article.php?sid=$sid&amp;archive=$archive\">"
                . "$nuke_url/article.php?sid=$sid&amp;archive=$archive</a>\n\n"
        );

        include 'config/signat.php';

        $fmail = Hack::removeHack($fmail);
        $subject = Hack::removeHack($subject);
        $message = Hack::removeHack($message);
        $yname = Hack::removeHack($yname);
        $ymail = Hack::removeHack($ymail);

        $stop = false;

        if ((!$fmail) || ($fmail == '') || (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $fmail))) {
            $stop = true;
        }

        if ((!$ymail) || ($ymail == '') || (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $ymail))) {
            $stop = true;
        }

        if (!$stop) {
            Mailer::sendEmail($fmail, $subject, $message, $ymail, false, 'html', '');
        } else {
            $title = '';
            $fname = '';
        }

        $title = urlencode(Language::affLangue($title));
        $fname = urlencode($fname);

        Header('Location: friend.php?op=StorySent&title=' . $title . '&fname=' . $fname);
    }

    function StorySent($title, $fname)
    {
        //include 'header.php';

        $title = urldecode($title);
        $fname = urldecode($fname);

        if ($fname == '') {
            echo '<div class="alert alert-danger">' . translate('Erreur : Email invalide') . '</div>';
        } else {
            echo '<div class="alert alert-success">' . translate('L\'article') . ' <strong>' . stripslashes($title) . '</strong> ' . translate('a été envoyé à') . '&nbsp;' . $fname . '<br />' . translate('Merci') . '</div>';
        }

        //include 'footer.php';
    }

    function RecommendSite()
    {
        global $user;

        if ($user) {
            global $cookie;

            $result = sql_query("SELECT name, email 
                                FROM " . sql_prefix('users') . " 
                                WHERE uname='$cookie[1]'");

            list($yn, $ye) = sql_fetch_row($result);
        } else {
            $yn = '';
            $ye = '';
        }

        //include 'header.php';

        echo '<div class="card card-body">
        <h2>' . translate('Recommander ce site à un ami') . '</h2>
        <hr />
        <form id="friendrecomsite" action="friend.php" method="post">
            <input type="hidden" name="op" value="SendSite" />
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="yname" name="yname" value="' . $yn . '" required="required" maxlength="100" />
                <label for="yname">' . translate('Votre nom') . '</label>
                <span class="help-block text-end"><span class="muted" id="countcar_yname"></span></span>
            </div>
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="ymail" name="ymail" value="' . $ye . '" required="required" maxlength="100" />
                <label for="ymail">' . translate('Votre Email') . '</label>
            </div>
            <span class="help-block text-end"><span class="muted" id="countcar_ymail"></span></span>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="fname" name="fname" required="required" maxlength="100" />
                <label for="fname">' . translate('Nom du destinataire') . '</label>
                <span class="help-block text-end"><span class="muted" id="countcar_fname"></span></span>
            </div>
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="fmail" name="fmail" required="required" maxlength="100" />
                <label for="fmail">' . translate('Email du destinataire') . '</label>
                <span class="help-block text-end"><span class="muted" id="countcar_fmail"></span></span>
            </div>
            ' . Spam::questionSpambot() . '
            <div class="mb-3 row">
                <div class="col-sm-8 ms-sm-auto">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-lg fa-at"></i>&nbsp;' . translate('Envoyer') . '</button>
                </div>
            </div>
        </form>';

        $arg1 = 'var formulid = ["friendrecomsite"];
            inpandfieldlen("yname",100);
            inpandfieldlen("ymail",100);
            inpandfieldlen("fname",100);
            inpandfieldlen("fmail",100);';

        Validation::adminFoot('fv', '', $arg1, '');
    }

    function SendSite($yname, $ymail, $fname, $fmail, $asb_question, $asb_reponse)
    {
        global $user;

        if (!$user) {
            //anti_spambot
            if (!Spam::reponseSpambot($asb_question, $asb_reponse, '')) {
                Log::ecrireLog('security', sprintf('Friend Anti-Spam : name=%s / mail=%s', $yname, $ymail), '');

                Url::redirectUrl('index.php');
                die();
            }
        }

        global $sitename, $nuke_url;

        $subject = html_entity_decode(translate('Site à découvrir : '), ENT_COMPAT | ENT_HTML401, 'UTF-8') . " $sitename";

        $fname = Hack::removeHack($fname);
        $message = translate('Bonjour') . " $fname :\n\n" . translate('Votre ami') . " $yname " . translate('a trouvé notre site') . " $sitename " . translate('intéressant et a voulu vous le faire connaître.') . "\n\n$sitename : <a href=\"$nuke_url\">$nuke_url</a>\n\n";

        include 'config/signat.php';

        $fmail = Hack::removeHack($fmail);
        $subject = Hack::removeHack($subject);
        $message = Hack::removeHack($message);
        $yname = Hack::removeHack($yname);
        $ymail = Hack::removeHack($ymail);

        $stop = false;

        if ((!$fmail) || ($fmail == '') || (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $fmail))) {
            $stop = true;
        }

        if ((!$ymail) || ($ymail == '') || (!preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $ymail))) {
            $stop = true;
        }

        if (!$stop) {
            Mailer::sendEmail($fmail, $subject, $message, $ymail, false, 'html', '');
        } else {
            $fname = '';
        }

        Header('Location: friend.php?op=SiteSent&fname=' . $fname);
    }

    function SiteSent($fname)
    {
        //include 'header.php';

        if ($fname == '') {
            echo '<div class="alert alert-danger lead" role="alert">
                    <i class="fa fa-exclamation-triangle fa-lg"></i>&nbsp;
                    ' . translate('Erreur : Email invalide') . '
                </div>';
        } else {
            echo '
            <div class="alert alert-success lead" role="alert">
                <i class="fa fa-exclamation-triangle fa-lg"></i>&nbsp;
                ' . translate('Nos références ont été envoyées à ') . ' ' . $fname . ', <br />
                <strong>' . translate('Merci de nous avoir recommandé') . '</strong>
            </div>';
        }

        //include 'footer.php';
    }

}

/*
switch ($op) {

    case 'FriendSend':
        FriendSend($sid, $archive);
        break;

    case 'SendStory':
        SendStory($sid, $yname, $ymail, $fname, $fmail, $archive, $asb_question, $asb_reponse);
        break;

    case 'StorySent':
        StorySent($title, $fname);
        break;

    case 'SendSite':
        SendSite($yname, $ymail, $fname, $fmail, $asb_question, $asb_reponse);
        break;

    case 'SiteSent':
        SiteSent($fname);
        break;

    default:
        RecommendSite();
        break;
}
*/
