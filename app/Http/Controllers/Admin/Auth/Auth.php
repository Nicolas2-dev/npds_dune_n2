<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Core\AdminBaseController;

class Auth extends AdminBaseController
{


    public function __construct()
    {
        //
    }

    // controller auth admin
    public function login()
    {
        include 'header.php';

        echo '<h1>' . adm_translate('Administration') . '</h1>
        <div id ="adm_men">
            <h2 class="mb-3"><i class="fas fa-sign-in-alt fa-lg align-middle me-2"></i>' . adm_translate('Connexion') . '</h2>
            <form action="admin.php" method="post" id="adminlogin" name="adminlogin">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="mb-3 form-floating">
                            <input id="aid" class="form-control" type="text" name="aid" maxlength="20" placeholder="' . adm_translate('Administrateur ID') . '" required="required" />
                            <label for="aid">' . adm_translate('Administrateur ID') . '</label>
                        </div>
                        <span class="help-block text-end"><span id="countcar_aid"></span></span>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3 form-floating">
                            <input id="pwd" class="form-control" type="password" name="pwd" maxlength="18" placeholder="' . adm_translate('Mot de Passe') . '" required="required" />
                            <label for="pwd">' . adm_translate('Mot de Passe') . '</label>
                        </div>
                        <span class="help-block text-end"><span id="countcar_pwd"></span></span>
                    </div>
                </div>
                <button class="btn btn-primary btn-lg" type="submit">' . adm_translate('Valider') . '</button>
                <input type="hidden" name="op" value="login" />
            </form>
            <script type="text/javascript">
                //<![CDATA[
                    document.adminlogin.aid.focus();
                    $(document).ready(function() {
                        inpandfieldlen("pwd",18);
                        inpandfieldlen("aid",20);
                    });
                //]]>
            </script>';

        $arg1 = 'var formulid =["adminlogin"];';

        Validation::adminFoot('fv', '', $arg1, '');
    }

}
