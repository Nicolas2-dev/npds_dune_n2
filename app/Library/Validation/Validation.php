<?php

namespace App\Library\Validation;

use Npds\Config\Config;
use Npds\Support\Facades\View;
use App\Support\Facades\Language;


class Validation
{

    /**
     * Instance singleton du dispatcher.
     *
     * @var self|null
     */
    protected static ?self $instance = null;


    /**
     * Retourne l'instance singleton du dispatcher.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }
    
    /**
     * Génère le footer de l'administration avec support du validateur de formulaire et inclusion des scripts JS.
     *
     * Cette fonction inclut :
     * - Les scripts nécessaires à FormValidation (Bootstrap 5 et locales)
     * - Le validateur personnalisé pour les mots de passe
     * - La configuration des champs passés en paramètre
     * - La fermeture de la div admin et l'inclusion de footer.php selon le paramètre `$foo`
     *
     * @param string $fv Indique si le validateur de formulaire doit être inclus ('fv') ou non.
     * @param string $fv_parametres Paramètres JS des champs du formulaire séparés par '!###!'. La première partie est utilisée pour l'objet `fields`, la seconde pour le code à exécuter après l'initialisation.
     * @param string $arg1 JS pur injecté au début du script FormValidation.
     * @param string $foo Détermine le comportement final :
     *                    - '' : ferme la div admin et inclut footer.php
     *                    - 'foo' : inclut seulement footer.php
     * @return void
     */
    public function adminFoot(string $fv, string $fv_parametres, string $arg1, string $foo): void
    {
        $content = '';
        
        if ($fv == 'fv') {

            if ($fv_parametres != '') {
                $fv_parametres = explode('!###!', $fv_parametres);
            }

            $content .= '<script type="text/javascript" src="' . asset_url('shared/es6/es6-shim.min.js') .'"></script>
            <script type="text/javascript" src="' . asset_url('shared/formvalidation/dist/js/FormValidation.full.min.js') .'"></script>
            <script type="text/javascript" src="' . asset_url('shared/formvalidation/dist/js/locales/' . Language::languageIso(1, "_", 1) . '.min.js') .'"></script>
            <script type="text/javascript" src="' . asset_url('shared/formvalidation/dist/js/plugins/Bootstrap5.min.js') .'"></script>
            <script type="text/javascript" src="' . asset_url('shared/formvalidation/dist/js/plugins/L10n.min.js') .'"></script>
            <script type="text/javascript" src="' . asset_url('js/npds_checkfieldinp.js') .'"></script>
            <script type="text/javascript">
            //<![CDATA[
            ' . $arg1 . '
            var diff;
            document.addEventListener("DOMContentLoaded", function(e) {
                // validateur pour mots de passe
                const strongPassword = function() {
                    return {
                        validate: function(input) {
                        let score=0;
                        const value = input.value;
                        if (value === "") {
                            return {
                                valid: true,
                                meta:{score:null},
                            };
                        }
                        if (value === value.toLowerCase()) {
                            return {
                                valid: false,
                                message: "' . translate('Le mot de passe doit contenir au moins un caractère en majuscule.') . '",
                                meta:{score: score-1},
                            };
                        }
                        if (value === value.toUpperCase()) {
                            return {
                                valid: false,
                                message: "' . translate('Le mot de passe doit contenir au moins un caractère en minuscule.') . '",
                                meta:{score: score-2},
                            };
                        }
                        if (value.search(/[0-9]/) < 0) {
                            return {
                                valid: false,
                                message: "' . translate('Le mot de passe doit contenir au moins un chiffre.') . '",
                                meta:{score: score-3},
                            };
                        }
                        if (value.search(/[@\+\-!#$%&^~*_]/) < 0) {
                            return {
                                valid: false,
                                message: "' . translate('Le mot de passe doit contenir au moins un caractère non alphanumérique.') . '",
                                meta:{score: score-4},
                            };
                        }
                        if (value.length < 8) {
                            return {
                                valid: false,
                                message: "' . translate('Le mot de passe doit contenir') . ' ' . Config::get('password.minpass') . ' ' . translate('caractères au minimum') . '",
                                meta:{score: score-5},
                            };
                        }
                        score += ((value.length >= ' . Config::get('password.minpass') . ') ? 1 : -1);
                        if (/[A-Z]/.test(value)) score += 1;
                        if (/[a-z]/.test(value)) score += 1; 
                        if (/[0-9]/.test(value)) score += 1;
                        if (/[@\+\-!#$%&^~*_]/.test(value)) score += 1; 
                        return {
                            valid: true,
                            meta:{score: score},
                        };
                        },
                    };
                };
                FormValidation.validators.checkPassword = strongPassword;
                formulid.forEach(function(item, index, array) {
                    const fvitem = FormValidation.formValidation(
                        document.getElementById(item),{
                        locale: "' . Language::languageIso(1, "_", 1) . '",
                        localization: FormValidation.locales.' . Language::languageIso(1, "_", 1) . ',
                        fields: {';

            if ($fv_parametres != '') {
                $content .= '' . $fv_parametres[0];
            }

            $content .= '},
                    plugins: {
                        declarative: new FormValidation.plugins.Declarative({
                            html5Input: true,
                        }),
                        trigger: new FormValidation.plugins.Trigger(),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5({rowSelector: ".mb-3"}),
                        icon: new FormValidation.plugins.Icon({
                            valid: "fa fa-check",
                            invalid: "fa fa-times",
                            validating: "fa fa-sync",
                            onPlaced: function(e) {
                                e.iconElement.addEventListener("click", function() {
                                fvitem.resetField(e.field);
                                });
                            },
                        }),
                    },
                    })
                    .on("core.validator.validated", function(e) {
                    if ((e.field === "add_pwd" || e.field === "chng_pwd" || e.field === "pass" || e.field === "add_pass" || e.field === "code" || e.field === "passwd") && e.validator === "checkPassword") {
                        var score = e.result.meta.score;
                        const barre = document.querySelector("#passwordMeter_cont");
                        const width = (score < 0) ? score * -18 + "%" : "100%";
                        barre.style.width = width;
                        barre.classList.add("progress-bar","progress-bar-striped","progress-bar-animated","bg-success");
                        barre.setAttribute("aria-valuenow", width);
                        if (score === null) {
                            barre.style.width = "100%";
                            barre.setAttribute("aria-valuenow", "100%");
                            barre.classList.replace("bg-success","bg-danger");
                        } else 
                            barre.classList.replace("bg-danger","bg-success");
                    }
                    if (e.field === "B1" && e.validator === "promise") {
                        if (e.result.valid && e.result.meta && e.result.meta.source) {
                            $("#ava_perso").removeClass("border-danger").addClass("border-success")
                        } else if (!e.result.valid) {
                            $("#ava_perso").addClass("border-danger")
                        }
                    }
                    });';

            if ($fv_parametres != '') {
                if (array_key_exists(1, $fv_parametres)) {
                    $content .= '' . $fv_parametres[1];
                }
            }

            $content .= '})
                });
            //]]>
            </script>';
        }

        switch ($foo) {

            case '':
                $content .= '</div>';
                //include 'footer.php';
                break;

            //case 'foo':
            //    include 'footer.php';
            //    break;
        }

        View::share('validation', $content);
    }
}
