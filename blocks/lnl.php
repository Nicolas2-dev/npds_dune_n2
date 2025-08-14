<?php

if (! function_exists('lnlbox'))
{ 
    #autodoc lnlbox() : Bloc Little News-Letter <br />=> syntaxe : function#lnlbox
    function lnlbox()
    {
        global $block_title;

        $title = $block_title == '' ? translate('La lettre') : $block_title;

        $boxstuff = '<form id="lnlblock" action="lnl.php" method="get">
            <div class="mb-3">
                <select name="op" class=" form-select">
                    <option value="subscribe">' . translate('Abonnement') . '</option>
                    <option value="unsubscribe">' . translate('Désabonnement') . '</option>
                </select>
            </div>
            <div class="form-floating mb-3">
                <input type="email" id="email_block" name="email" maxlength="254" class="form-control" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" required="required"/>
                <label for="email_block">' . translate('Votre adresse Email') . '</label>
                <span class="help-block">' . translate('Recevez par mail les nouveautés du site.') . '</span>
            </div>
            <button type="submit" class="btn btn-outline-primary btn-block btn-sm"><i class ="fa fa-check fa-lg me-2"></i>' . translate('Valider') . '</button>
        </form>';

        themesidebox($title, $boxstuff);
    }
}