<?php

namespace App\Components;

use App\Library\Components\BaseComponent;

/* Exemple d'appel :
    <?= Component::login(); ?>
*/

class LoginComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        global $user;

        if(isset($user)) {
            return '<h5><a class="text-danger" href="user.php?op=logout"><i class="fas fa-sign-out-alt fa-lg align-middle text-danger me-2"></i>'
                   .translate("Déconnexion").'</a></h5>';
        }

        return '
        <div class="card card-body m-3">
            <h5><a href="user.php?op=only_newuser" role="button" title="'.translate("Nouveau membre").'"><i class="fa fa-user-plus"></i>&nbsp;'.translate("Nouveau membre").'</a></h5>
        </div>
        <div class="card card-body m-3">
            <h5 class="mb-3"><i class="fas fa-sign-in-alt fa-lg"></i>&nbsp;'.translate("Connexion").'</h5>
            <form action="user.php" method="post" name="userlogin_b">
                <div class="row g-2">
                    <div class="col-12">
                        <div class="mb-3 form-floating">
                            <input type="text" class="form-control" name="uname" id="inputuser_b" placeholder="'.translate("Identifiant").'" required="required" />            
                            <label for="inputuser_b" >'.translate("Identifiant").'</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-0 form-floating">
                            <input type="password" class="form-control" name="pass" id="inputPassuser_b" placeholder="'.translate("Mot de passe").'" required="required" />
                            <label for="inputPassuser_b">'.translate("Mot de passe").'</label>
                            <span class="help-block small"><a href="user.php?op=forgetpassword" role="button" title="'.translate("Vous avez perdu votre mot de passe ?").'">'.translate("Vous avez perdu votre mot de passe ?").'</a></span>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="op" value="login" />
                <div class="mb-3 row">
                    <div class="ms-sm-auto">
                        <button class="btn btn-primary" type="submit" title="'.translate("Valider").'">'.translate("Valider").'</button>
                    </div>
                </div>
            </form>
        </div>';
    }
}
