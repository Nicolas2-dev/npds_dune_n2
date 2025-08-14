<?php

if (! function_exists('loginbox'))
{ 
    #autodoc loginbox() : Bloc Login <br />=> syntaxe : function#loginbox
    function loginbox()
    {
        global $user;

        $boxstuff = '';

        if (!$user) {
            $boxstuff = '
            <form action="user.php" method="post">
                <div class="mb-3">
                    <label for="uname">' . translate('Identifiant') . '</label>
                    <input class="form-control" type="text" name="uname" maxlength="25" />
                </div>
                <div class="mb-3">
                    <label for="pass">' . translate('Mot de passe') . '</label>
                    <input class="form-control" type="password" name="pass" maxlength="20" />
                </div>
                <div class="mb-3">
                    <input type="hidden" name="op" value="login" />
                    <button class="btn btn-primary" type="submit">' . translate('Valider') . '</button>
                </div>
                <div class="help-block">
                ' . translate('Vous n\'avez pas encore de compte personnel ? Vous devriez') . ' <a href="user.php">' . translate('en créer un') . '</a>. ' . translate('Une fois enregistré') . ' ' . translate('vous aurez certains avantages, comme pouvoir modifier l\'aspect du site,') . ' ' . translate('ou poster des commentaires signés...') . '
                </div>
            </form>';

            global $block_title;
            $title = $block_title == '' ? translate('Se connecter') : $block_title;

            themesidebox($title, $boxstuff);
        }
    }
}