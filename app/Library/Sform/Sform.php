<?php
################################################################################################
// Simple Form generator  SFORM / version 1.6 for DUNE
// Class to manage several Form in a single database(MySql) in XML Format
// P.Brunier 2001 - 2024
//
// This program is free software. You can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 3 of the License.
//
// Based on Form_Handler 19-4-01 Copyright Drs. Jelte 'YeeHaW' Werkhoven
//
// Mod by Didier (Jireck) Hoen Xhtml + form_id
// Mod by Dev 2011 - Rajout d'un textarea de type 'textarea_no_mceEditor' pour pouvoir associer
// dans un même FORMULAIRE des champs avec ET sans TinyMce / Rajout de l'anti_spambot
################################################################################################

namespace App\Library\Sform;

use App\Library\Spam\Spam;
use App\Library\Language\Language;


class Sform
{

    /**
     * Constante représentant un saut de ligne (Carriage Return + Line Feed).
     *
     * Utile pour séparer les lignes dans les textes ou fichiers, 
     * notamment lors de la génération de contenu multi-lignes.
     */
    public const CRLF = "\n";

    /**
     * Champs du formulaire.
     *
     * @var array
     */
    public array $form_fields = [];

    /**
     * Titre du formulaire.
     *
     * @var string|null
     */
    public ?string $title = null;

    /**
     * Message obligatoire.
     *
     * @var string|null
     */
    public ?string $mess = null;

    /**
     * Titre du formulaire (duplicate?).
     *
     * @var string|null
     */
    public ?string $form_title = null;

    /**
     * ID du formulaire (pour CSS personnalisé).
     *
     * @var string|null
     */
    public ?string $form_id = null;

    /**
     * Méthode du formulaire (POST ou GET).
     *
     * @var string|null
     */
    public ?string $form_method = null;

    /**
     * Clé du formulaire (pour stockage MySQL).
     *
     * @var string|null
     */
    public ?string $form_key = null;

    /**
     * Valeur de la clé du formulaire (pour stockage MySQL).
     *
     * @var string|null
     */
    public ?string $form_key_value = null;

    /**
     * Statut de la clé (open ou close).
     *
     * @var string
     */
    public string $form_key_status = 'open';

    /**
     * Nom de tous les boutons submit du formulaire.
     *
     * @var string
     */
    public string $submit_value = '';

    /**
     * Protection du formulaire avec un mot de passe.
     *
     * @var string
     */
    public string $form_password_access = '';

    /**
     * Tableau des réponses.
     *
     * @var array
     */
    public array $answer = [];

    /**
     * Chaîne insérée dans la fonction de vérification JavaScript.
     *
     * @var string
     */
    public string $form_check = 'true';

    /**
     * Chemin utilisé dans l'attribut 'action' du formulaire.
     *
     * @var string|null
     */
    public ?string $url = null;

    /**
     * Valeur de l'attribut size d'un champ du formulaire.
     *
     * @var int
     */
    public int $field_size = 50;


    /**
     * Interroge le formulaire pour identifier la position d'un champ par son nom.
     *
     * @param string $ibid Nom du champ à rechercher
     * @return int|string Retourne l'index du champ dans $form_fields ou 'no' si non trouvé
     */
    private function interroFields(string $ibid): int|string
    {
        $number = 'no';

        for (Reset($this->form_fields), $node = 0; $node < count($this->form_fields); Next($this->form_fields), $node++) {
            if (array_key_exists('name', $this->form_fields[$node])) {
                if ($ibid == $this->form_fields[$node]['name']) {
                    $number = $node;
                    break;
                }
            }
        }

        return $number;
    }

    /**
     * Interroge un tableau pour identifier la position d'un élément par sa valeur 'en'.
     *
     * @param array<int, array<string, mixed>> $ibid0 Tableau à parcourir
     * @param string $ibid1 Valeur à rechercher dans la clé 'en'
     * @return int|string Retourne l'index de l'élément ou 'no' si non trouvé
     */
    private function interroArray(array $ibid0, string $ibid1): int|string
    {
        $number = 'no';

        foreach ($ibid0 as $key => $val) {
            if ($ibid1 == $val['en']) {
                $number = $key;
                break;
            }
        }

        return $number;
    }

    /**
     * Change la taille par défaut des champs du formulaire.
     *
     * @param int|string $en
     * @return void
     */
    public function addFormFieldSize(int|string $en): void
    {
        $this->field_size = $en;
    }

    /**
     * Définit le titre du formulaire (identifiant unique en base).
     *
     * @param string $en
     * @return void
     */
    public function addFormTitle(string $en): void
    {
        $this->form_title = $en;
    }

    /**
     * Définit l'ID du formulaire.
     *
     * @param string $en
     * @return void
     */
    public function addFormId(string $en): void
    {
        $this->form_id = $en;
    }

    /**
     * Définit la méthode HTTP du formulaire (GET ou POST).
     *
     * @param string $en
     * @return void
     */
    public function addFormMethod(string $en): void
    {
        $this->form_method = $en;
    }

    /**
     * Active la vérification des champs obligatoires après soumission.
     *
     * @param bool $en
     * @return void
     */
    public function addFormCheck(bool $en): void
    {
        $this->form_check = $en;
    }

    /**
     * Définit l'URL de redirection après soumission.
     *
     * @param string $en
     * @return void
     */
    public function addUrl(string $en): void
    {
        $this->url = $en;
    }

    /**
     * Définit le champ clé du formulaire pour la base de données.
     *
     * @param string $en
     * @return void
     */
    public function addKey(string $en): void
    {
        $this->form_key = $en;
    }

    /**
     * Définit le nom pour tous les boutons submit du formulaire.
     *
     * @param string $en
     * @return void
     */
    public function addSubmitValue(string $en): void
    {
        $this->submit_value = $en;
    }

    /**
     * Verrouille ou déverrouille la clé du formulaire.
     *
     * @param string $en 'open' pour déverrouiller, autre pour verrouiller
     * @return void
     */
    public function keyLock(string $en): void
    {
        if ($en == 'open') {
            $this->form_key_status = 'open';
        } else {
            $this->form_key_status = 'close';
        }
    }

    /**
     * Ajoute un message général au formulaire.
     *
     * @param string $en
     * @return void
     */
    public function addMess(string $en): void
    {
        $this->mess = $en;
    }

    /**
     * Ajoute un champ texte, textarea, password, submit, reset, email ou hidden.
     *
     * @param string $name
     * @param string $en
     * @param string $value
     * @param string $type
     * @param bool $obligation
     * @param int|string $size
     * @param int|string $diviseur
     * @param string $ctrl
     * @return void
     */
    public function addField(
        string      $name,
        string      $en,
        string      $value = '',
        string      $type = 'text',
        bool        $obligation = false,
        int|string  $size = 50,
        int|string  $diviseur = 5,
        string      $ctrl = ''
    ): void {
        if ($type == 'submit') {
            $name = $this->submit_value;
        }

        $this->form_fields[count($this->form_fields)] = array(
            'name'          => $name,
            'type'          => $type,
            'en'            => $en,
            'value'         => $value,
            'size'          => $size,
            'diviseur'      => $diviseur,
            'obligation'    => $obligation,
            'ctrl'          => $ctrl
        );
    }

    /**
     * Ajoute un champ checkbox.
     *
     * @param string $name
     * @param string $en
     * @param string $value
     * @param bool $obligation
     * @param bool $checked
     * @return void
     */
    public function addCheckbox(string $name, string $en, string $value = '', bool $obligation = false, bool $checked = false): void
    {
        $this->form_fields[count($this->form_fields)] = array(
            'name'          => $name,
            'en'            => $en,
            'value'         => $value,
            'type'          => 'checkbox',
            'checked'       => $checked,
            'obligation'    => $obligation
        );
    }

    /**
     * Ajoute un champ select.
     *
     * @param string $name
     * @param string $en
     * @param array<int, string> $values
     * @param bool $obligation
     * @param int $size
     * @param bool $multiple
     * @return void
     */
    public function addSelect(string $name, string $en, array $values, bool $obligation = false, int $size = 1, bool $multiple = false): void
    {
        $this->form_fields[count($this->form_fields)] = array(
            'name'          => $name,
            'en'            => $en,
            'type'          => 'select',
            'value'         => $values,
            'size'          => $size,
            'multiple'      => $multiple,
            'obligation'    => $obligation
        );
    }

    /**
     * Ajoute un champ radio.
     *
     * @param string $name
     * @param string $en
     * @param array<int, string> $values
     * @param bool $obligation
     * @return void
     */
    public function addRadio(string $name, string $en, array $values, bool $obligation = false): void
    {
        $this->form_fields[count($this->form_fields)] = array(
            'name'          => $name,
            'en'            => $en,
            'type'          => 'radio',
            'value'         => $values,
            'obligation'    => $obligation
        );
    }

    /**
     * Ajoute un champ date ou timestamp.
     *
     * @param string $name
     * @param string $en
     * @param string|int $value
     * @param string $type
     * @param string $modele
     * @param bool $obligation
     * @param int|string $size
     * @return void
     */
    public function addDate(
        string      $name,
        string      $en,
        string|int  $value,
        string      $type = 'date',
        string      $modele = 'm/d/Y',
        bool        $obligation = false,
        int|string  $size = 10
    ): void {
        $this->form_fields[count($this->form_fields)] = array(
            'name'          => $name,
            'type'          => $type,
            'model'         => $modele,
            'en'            => $en,
            'value'         => $value,
            'size'          => $size,
            'obligation'    => $obligation,
            'ctrl'          => 'date'
        );
    }

    /**
     * Définit le titre de l'onglet HTML.
     *
     * @param string $en
     * @return void
     */
    public function addTitle(string $en): void
    {
        $this->title = $en;
    }

    /**
     * Ajoute un commentaire dans le formulaire.
     *
     * @param string $en
     * @return void
     */
    public function addComment(string $en): void
    {
        $this->form_fields[count($this->form_fields)] = array(
            'en'        => $en,
            'type'      => 'comment'
        );
    }

    /**
     * Ajoute un contenu extra dans le formulaire.
     *
     * @param string $en
     * @return void
     */
    public function addExtra(string $en): void
    {
        $this->form_fields[count($this->form_fields)] = array(
            'en'        => $en,
            'type'      => 'extra'
        );
    }

    /**
     * Ajoute un contenu extra caché dans le formulaire (affiché mais non dans la réponse).
     *
     * @param string $en
     * @return void
     */
    public function addExtraHidden(string $en): void
    {
        $this->form_fields[count($this->form_fields)] = array(
            'en'        => $en,
            'type'      => 'extra-hidden'
        );
    }

    /**
     * Ajoute le champ anti-spam Q_spambot.
     *
     * @return void
     */
    public function addQspam(): void
    {
        $this->form_fields[count($this->form_fields)] = array(
            'en'        => '',
            'type'      => 'Qspam'
        );
    }

    /**
     * Ajoute un champ EXTENDER (JavaScript et HTML).
     *
     * @param string $name
     * @param string $javas
     * @param string $html
     * @return void
     */
    public function addExtender(string $name, string $javas, string $html): void
    {
        $this->form_fields[count($this->form_fields)] = array(
            'name'      => $name . 'extender',
            'javas'     => $javas,
            'html'      => $html
        );
    }

    /**
     * Ajoute un champ upload (design seulement, pas de mécanisme d'upload intégré).
     *
     * @param string $name
     * @param string $en
     * @param int|string $size
     * @param int $file_size
     * @return void
     */
    public function addUpload(string $name, string $en, int|string $size = 50, int $file_size = 0): void
    {
        $this->form_fields[count($this->form_fields)] = array(
            'name'      => $name,
            'en'        => $en,
            'value'     => '',
            'type'      => 'upload',
            'size'      => $size,
            'file_size' => $file_size
        );
    }

    /**
     * Génère le code HTML d'un formulaire basé sur les propriétés de l'objet.
     *
     * Si `form_method` n'est pas défini, le <form></form> n'est pas généré.
     * Utile pour insérer SFORM dans un formulaire existant.
     *
     * @param string $bg Paramètre utilisé pour le style de fond (non utilisé dans le code actuel).
     * @return string Le HTML généré du formulaire.
     */
    public function printForm(string $bg): string
    {
        if (isset($this->form_id)) {
            $id_form = 'id="' . $this->form_id . '"';
        } else {
            $id_form = '';
        }

        $str = '';

        if ($this->form_method != '') {
            $str .= "\n<form action=\"" . $this->url . "\" " . $id_form . "  method=\"" . $this->form_method . "\" name=\"" . $this->form_title . "\" enctype=\"multipart/form-data\"";

            if ($this->form_check == 'true') {
                $str .= ' onsubmit="return check();">';
            } else {
                $str .= '>';
            }
        }

        // todo utilisation de tabindex dans les input
        $str .= '<fieldset>
            <div class="mb-4">' . $this->title . '</div>';

        for ($i = 0; $i < count($this->form_fields); $i++) {
            if (array_key_exists('size', $this->form_fields[$i])) {
                if ($this->form_fields[$i]['size'] >= $this->field_size) {
                    $csize = $this->field_size;
                } else {
                    $csize = (int)$this->form_fields[$i]['size'] + 1;
                }
            }

            if (array_key_exists('name', $this->form_fields[$i])) {
                $num_extender = $this->interroFields($this->form_fields[$i]['name'] . 'extender');
            } else {
                $num_extender = 'no';
            }

            if (array_key_exists('type', $this->form_fields[$i])) {

                switch ($this->form_fields[$i]['type']) {

                    case 'text':
                    case 'email':
                    case 'url':
                    case 'number':
                        $str .= '<div class="mb-3 row">
                        <label class="col-form-label col-sm-4" for="' . $this->form_fields[$i]['name'] . '">' . $this->form_fields[$i]['en'];

                        $this->form_fields[$i]['value'] = $this->form_fields[$i]['value'] ? str_replace('\'', '&#039;', $this->form_fields[$i]['value']) : '';

                        $requi = '';

                        if ($this->form_fields[$i]['obligation']) {
                            $requi = 'required="required"';

                            $this->form_check .= " && (f.elements['" . $this->form_fields[$i]['name'] . "'].value!='')";

                            $str .= '<span class="text-danger ms-2">*</span>';
                        }

                        $str .= '</label>
                        <div class="col-sm-8">';

                        // Charge la valeur et analyse la clef
                        if ($this->form_fields[$i]['name'] == $this->form_key) {
                            $this->form_key_value = $this->form_fields[$i]['value'];

                            if ($this->form_key_status == 'close') {
                                $str .= '<input class="form-control" readonly="readonly" type="' . $this->form_fields[$i]['type'] . '" id="' . $this->form_fields[$i]['name'] . '" name="' . $this->form_fields[$i]['name'] . '" value="' . $this->form_fields[$i]['value'] . '" size="' . $csize . '" maxlength="' . $this->form_fields[$i]['size'] . '" ';
                            } else {
                                $str .= '<input class="form-control" type="' . $this->form_fields[$i]['type'] . '" id="' . $this->form_fields[$i]['name'] . '" name="' . $this->form_fields[$i]['name'] . '" value="' . $this->form_fields[$i]['value'] . '" size="' . $csize . '" maxlength="' . $this->form_fields[$i]['size'] . '" ' . $requi;
                            }
                        } else {
                            $str .= '<input class="form-control" type="' . $this->form_fields[$i]['type'] . '" id="' . $this->form_fields[$i]['name'] . '" name="' . $this->form_fields[$i]['name'] . '" value="' . $this->form_fields[$i]['value'] . '" size="' . $csize . '" maxlength="' . $this->form_fields[$i]['size'] . '" ' . $requi;
                        }

                        if ($num_extender != 'no') {
                            $str .= ' ' . $this->form_fields[$num_extender]['javas'] . '>';
                            $str .= $this->form_fields[$num_extender]['html'];
                        } else {
                            $str .= ' /> ';
                        }

                        $str .= '</div>
                        </div>';
                        break;

                    case 'password-access':
                        $this->form_fields[$i]['value'] = $this->form_password_access;

                    case 'password':
                        $str .= '<div class="mb-3 row">
                        <label class="col-form-label col-sm-4" for="' . $this->form_fields[$i]['name'] . '">' . $this->form_fields[$i]['en'];

                        $this->form_fields[$i]['value'] = str_replace('\'', '&#039;', $this->form_fields[$i]['value']);

                        $requi = '';

                        if ($this->form_fields[$i]['obligation']) {
                            $requi = 'required="required"';

                            $this->form_check .= " && (f.elements['" . $this->form_fields[$i]['name'] . "'].value!='')";

                            $str .= '&nbsp;<span class="text-danger">*</span></label>';
                        } else {
                            $str .= '</label>';
                        }

                        $str .= '<div class="col-sm-8">
                        <input class="form-control" type="' . $this->form_fields[$i]['type'] . '" id="' . $this->form_fields[$i]['name'] . '" name="' . $this->form_fields[$i]['name'] . '" value="' . $this->form_fields[$i]['value'] . '" size="' . $csize . '" maxlength="' . $this->form_fields[$i]['size'] . '" ' . $requi . ' />';

                        if ($num_extender != 'no') {
                            $str .= $this->form_fields[$num_extender]['html'];
                        }

                        $str .= '</div>
                        </div>';
                        break;

                    case 'checkbox':
                        $requi = '';

                        if ($this->form_fields[$i]['obligation']) {
                            $requi = 'required="required"';
                        }

                        $str .= '<div class="mb-3 row">
                        <div class="col-sm-8 ms-sm-auto">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="' . $this->form_fields[$i]['name'] . '" name="' . $this->form_fields[$i]['name'] . '" value="' . $this->form_fields[$i]['value'] . '" ' . $requi;

                        $str .= ($this->form_fields[$i]['checked']) ? ' checked="checked" />' : ' />';

                        $str .= '<label class="form-check-label" for="' . $this->form_fields[$i]['name'] . '">' . $this->form_fields[$i]['en'];

                        if ($this->form_fields[$i]['obligation']) {
                            $str .= '<span class="text-danger"> *</span>';
                        }

                        $str .= '</label>
                        </div>';

                        if ($num_extender != 'no') {
                            $str .= $this->form_fields[$num_extender]['html'];
                        }

                        $str .= '</div>
                        </div>';
                        break;

                    case 'textarea':
                        $requi = '';

                        if ($this->form_fields[$i]['obligation']) {
                            $requi = 'required="required"';
                        }

                        $str .= '<div class="mb-3 row">
                        <label class="col-form-label col-sm-4" for="' . $this->form_fields[$i]['name'] . '">' . $this->form_fields[$i]['en'];

                        $this->form_fields[$i]['value'] = str_replace('\'', '&#039;', $this->form_fields[$i]['value']);

                        if ($this->form_fields[$i]['obligation']) {
                            $this->form_check .= " && (f.elements['" . $this->form_fields[$i]['name'] . "'].value!='')";

                            $str .= '&nbsp;<span class="text-danger">*</span>';
                        }

                        $str .= '</label>';

                        $txt_row = $this->form_fields[$i]['diviseur'];

                        //$txt_col=( ($this->form_fields[$i]['size'] - ($this->form_fields[$i]['size'] % $txt_row)) / $txt_row);

                        $str .= '<div class="col-sm-8">
                        <textarea class="form-control" name="' . $this->form_fields[$i]['name'] . '" id="' . $this->form_fields[$i]['name'] . '" rows="' . $txt_row . '" ' . $requi . '>' . $this->form_fields[$i]['value'] . '</textarea>';

                        if ($num_extender != 'no') {
                            $str .= $this->form_fields[$num_extender]['html'];
                        }

                        $str .= '</div>
                        </div>';
                        break;

                    //not sure to check if ok on all case
                    case 'show-hidden':
                        $str .= '<div class="mb-3 row">
                        <label class="col-form-label col-sm-4">' . $this->form_fields[$i]['en'] . '</label>
                        <div class="col-sm-8">';

                        if ($num_extender != 'no') {
                            $str .= $this->form_fields[$num_extender]['html'];
                        }

                        $str .= '</div>
                        </div>';

                    case 'hidden':
                        $this->form_fields[$i]['value'] = str_replace('\'', '&#039;', $this->form_fields[$i]['value']);

                        $str .= '<input type="hidden" name="' . $this->form_fields[$i]['name'] . '" value="' . $this->form_fields[$i]['value'] . '" />';
                        break;

                    case 'select':
                        $str .= '<div class="mb-3 row">
                        <label class="col-form-label col-sm-4" for="' . $this->form_fields[$i]['name'] . '">' . $this->form_fields[$i]['en'] . '</label>
                        <div class="col-sm-8">
                            <select class="';

                        $str .= ($this->form_fields[$i]['multiple']) ? 'form-control' : 'form-select';

                        $str .= '" id="' . $this->form_fields[$i]['name'] . '" name="' . $this->form_fields[$i]['name'];

                        $str .= ($this->form_fields[$i]['multiple']) ? '[]" multiple="multiple"' : "\"";

                        if ($num_extender != 'no') {
                            $str .= ' ' . $this->form_fields[$num_extender]['javas'] . ' ';
                        }

                        $str .= ($this->form_fields[$i]['size'] > 1) ? " size=\"" . $this->form_fields[$i]['size'] . "\">" : '>';

                        foreach ($this->form_fields[$i]['value'] as $key => $val) {
                            $str .= '<option value="' . $key . '"';

                            if (array_key_exists('selected', $val) and $val['selected']) {
                                $str .= ' selected="selected" >';
                            } else {
                                $str .= ' >';
                            }

                            $str .= str_replace('\'', '&#039;', $val['en']) . '</option>';
                        }

                        $str .= '</select>';

                        if ($num_extender != 'no') {
                            $str .= $this->form_fields[$num_extender]['html'];
                        }

                        $str .= '</div>
                        </div>';
                        break;

                    case 'radio':
                        $first_radio = true;

                        $str .= '<div class="mb-3 row">
                        <label class="col-sm-4" for="' . $this->form_fields[$i]['name'] . '">' . $this->form_fields[$i]['en'] . '</label>
                        <div class="col-sm-8">';

                        foreach ($this->form_fields[$i]['value'] as $key => $val) {
                            $str .= '<input class="form-check-input" type="radio" ';

                            if ($first_radio) {
                                $str .= 'id="' . $this->form_fields[$i]['name'] . '" ';
                                $first_radio = false;
                            }

                            $str .= 'name="' . $this->form_fields[$i]['name'] . '" value="' . $key . '"';
                            $str .= ($val['checked']) ? ' checked="checked" />&nbsp;' : ' />&nbsp;';

                            $str .= $val['en'] . '&nbsp;&nbsp;';
                        }

                        if ($num_extender != 'no') {
                            $str .= $this->form_fields[$num_extender]['html'];
                        }

                        $str .= '</div>
                    </div>';
                        break;

                    case 'comment':
                        $str .= '<div class="col-sm-12">
                    <p>' . $this->form_fields[$i]['en'] . '</p>
                    </div>';
                        break;

                    case 'Qspam':
                        $str .= Spam::questionSpambot();
                        $str .= "\n";
                        break;

                    case 'extra':
                    case 'extra-hidden':
                        $str .= $this->form_fields[$i]['en'];
                        break;

                    case 'submit':
                        $this->form_fields[$i]['value'] = str_replace('\'', '&#039;', $this->form_fields[$i]['value']);

                        $str .= '<button class="btn btn-primary" id="' . $this->form_fields[$i]['name'] . '" type="submit" name="' . $this->form_fields[$i]['name'] . '" >' . $this->form_fields[$i]['value'] . '</button>';
                        break;

                    case 'reset':
                        $this->form_fields[$i]['value'] = str_replace('\'', '&#039;', $this->form_fields[$i]['value']);

                        $str .= $this->form_fields[$i]['en'];

                        $str .= '<input class="btn btn-secondary" id="' . $this->form_fields[$i]['name'] . '" type="reset" name="' . $this->form_fields[$i]['name'] . '" value="' . $this->form_fields[$i]['value'] . '" />';
                        break;

                    case 'stamp':
                        if ($this->form_fields[$i]['value'] == '') {
                            $this->form_fields[$i]['value'] = strtotime('now');
                        }

                        if ($this->form_fields[$i]['name'] == $this->form_key) {
                            $this->form_key_value = $this->form_fields[$i]['value'];
                        }

                        $str .= '<input type="hidden" name="' . $this->form_fields[$i]['name'] . '" value="' . $this->form_fields[$i]['value'] . '" />';
                        break;

                    case 'date':
                        if ($this->form_fields[$i]['value'] == '') {
                            $this->form_fields[$i]['value'] = date($this->form_fields[$i]['model']);
                        }

                        $str .= '<div class="mb-3 row">
                        <label class="col-form-label col-sm-4" for="' . $this->form_fields[$i]['name'] . '">' . $this->form_fields[$i]['en'];

                        if ($this->form_fields[$i]['obligation']) {
                            $this->form_check .= " && (f.elements['" . $this->form_fields[$i]['name'] . "'].value!='')";

                            $str .= '&nbsp;<span class="text-danger">*</span></label>';
                        } else {
                            $str .= '</label>';
                        }

                        if ($this->form_fields[$i]['name'] == $this->form_key) {
                            $this->form_key_value = $this->form_fields[$i]['value'];

                            if ($this->form_key_status == 'close') {
                                $str .= '<input type="hidden" id="' . $this->form_fields[$i]['name'] . '" name="' . $this->form_fields[$i]['name'] . '" value="' . $this->form_fields[$i]['value'] . '" />
                                <b>' . $this->form_fields[$i]['value'] . '</b>';
                            } else {
                                $str .= '<div class="col-sm-8">
                                <input class="form-control" id="' . $this->form_fields[$i]['name'] . '" type="text" name="' . $this->form_fields[$i]['name'] . '" value="' . $this->form_fields[$i]['value'] . '" size="' . $csize . '" maxlength="' . $this->form_fields[$i]['size'] . '" />';
                            }
                        } else {
                            $str .= '<div class="col-sm-8">
                            <input class="form-control" id="' . $this->form_fields[$i]['name'] . '" type="text" name="' . $this->form_fields[$i]['name'] . '" value="' . $this->form_fields[$i]['value'] . '" size="' . $csize . '" maxlength="' . $this->form_fields[$i]['size'] . '" />';
                        }

                        if ($num_extender != 'no') {
                            $str .= $this->form_fields[$num_extender]['html'];
                        }

                        $str .= '</div>
                        </div>';
                        break;

                    case 'upload':
                        $str .= '<div id="avava" class="mb-3 row" lang="' . Language::languageIso(1, '', '') . '">
                        <label class="col-form-label col-sm-4" for="' . $this->form_fields[$i]['name'] . '">' . $this->form_fields[$i]['en'] . '</label>
                        <div class="col-sm-8">
                            <div class="input-group mb-2 me-sm-2">
                                <button class="btn btn-secondary" type="button" onclick="reset2($(\'#' . $this->form_fields[$i]['name'] . '\'),\'\');"><i class="fas fa-sync"></i></button>
                                <label class="input-group-text n-ci" id="lab" for="' . $this->form_fields[$i]['name'] . '"></label>
                                <input type="file" class="form-control custom-file-input" id="' . $this->form_fields[$i]['name'] . '"  name="' . $this->form_fields[$i]['name'] . '" size="' . $csize . '" maxlength="' . $this->form_fields[$i]['size'] . '" />
                            </div>
                            <input type="hidden" name="MAX_FILE_SIZE" value="' . $this->form_fields[$i]['file_size'] . '" />';

                        if ($num_extender != 'no') {
                            $str .= $this->form_fields[$num_extender]['html'];
                        }

                        $str .= '</div>
                        </div>';
                        break;

                    default:
                        break;
                }
            }
        }

        $str .= '</fieldset>';

        if ($this->form_method != '') {
            $str .= '</form>';
        }

        // cette condition n'est pas fonctionnelle ???
        if ($this->form_check != 'false') {
            $str .= "<script type=\"text/javascript\">//<![CDATA[" . self::CRLF;
            $str .= "var f=document.forms['" . $this->form_title . "'];" . self::CRLF;
            $str .= "function check(){" . self::CRLF;
            $str .= " if(" . $this->form_check . "){" . self::CRLF;
            $str .= "   f.submit();" . self::CRLF;
            $str .= "   return true;" . self::CRLF;
            $str .= " } else {" . self::CRLF;
            $str .= "   alert('" . $this->mess . "');" . self::CRLF;
            $str .= "   return false;" . self::CRLF;
            $str .= "}}" . self::CRLF;
            $str .= "//]]></script>\n";
        }

        return $str;
    }

    /**
     * Retourne tous les champs du formulaire sous forme de champs cachés HTML.
     *
     * @return string
     */
    public function printFormHidden(): string
    {
        $str = '';

        for ($i = 0; $i < count($this->form_fields); $i++) {
            if (array_key_exists('name', $this->form_fields[$i])) {

                $str .= '<input type="hidden" name="' . $this->form_fields[$i]['name'] . '" value="';

                if (array_key_exists('value', $this->form_fields[$i])) {
                    $str .= stripslashes(str_replace('\'', '&#039;', $this->form_fields[$i]['value'])) . '"';
                } else {
                    $str .= '"';
                }

                $str .= ' />';
            }
        }

        return $str;
    }

    /**
     * Génère le tableau de réponses du formulaire basé sur les champs définis.
     *
     * @return void
     */
    public function makeResponse(): void
    {
        for ($i = 0; $i < count($this->form_fields); $i++) {
            $this->answer[$i] = '';

            if (array_key_exists('type', $this->form_fields[$i])) {

                switch ($this->form_fields[$i]['type']) {

                    case 'text':
                    case 'email':
                    case 'url':
                    case 'number':
                        // Charge la valeur de la clef
                        if ($this->form_fields[$i]['name'] == $this->form_key) {
                            $this->form_key_value = $GLOBALS[$this->form_fields[$i]['name']];
                        }

                    case 'password':
                        if ($this->form_fields[$i]['ctrl'] != '') {
                            $this->control($this->form_fields[$i]['name'], $this->form_fields[$i]['en'], $GLOBALS[$this->form_fields[$i]['name']], $this->form_fields[$i]['ctrl']);
                        }

                        $this->answer[$i] .= "<TEXT>\n";
                        $this->answer[$i] .= "<" . $this->form_fields[$i]['name'] . ">" . $GLOBALS[$this->form_fields[$i]['name']] . "</" . $this->form_fields[$i]['name'] . ">\n";
                        $this->answer[$i] .= "</TEXT>";
                        break;

                    case 'password-access':
                        if ($this->form_fields[$i]['ctrl'] != '') {
                            $this->control($this->form_fields[$i]['name'], $this->form_fields[$i]['en'], $GLOBALS[$this->form_fields[$i]['name']], $this->form_fields[$i]['ctrl']);
                        }

                        $this->form_password_access = $GLOBALS[$this->form_fields[$i]['name']];
                        break;

                    case 'textarea':
                    case 'textarea_no_mceEditor':
                        $this->answer[$i] .= "<TEXT>\n";
                        $this->answer[$i] .= "<" . $this->form_fields[$i]['name'] . ">" . str_replace(chr(13) . chr(10), "&lt;br /&gt;", $GLOBALS[$this->form_fields[$i]['name']]) . "</" . $this->form_fields[$i]['name'] . ">\n";
                        $this->answer[$i] .= "</TEXT>";
                        break;

                    case 'select':
                        $this->answer[$i] .= "<SELECT>\n";

                        if (is_array($GLOBALS[$this->form_fields[$i]['name']])) {
                            for ($j = 0; $j < count($GLOBALS[$this->form_fields[$i]['name']]); $j++) {
                                $this->answer[$i] .= "<" . $this->form_fields[$i]['name'] . ">" . $this->form_fields[$i]['value'][$GLOBALS[$this->form_fields[$i]['name']][$j]]['en'] . "</" . $this->form_fields[$i]['name'] . ">\n";
                            }
                        } else {
                            $this->answer[$i] .= "<" . $this->form_fields[$i]['name'] . ">" . $this->form_fields[$i]['value'][$GLOBALS[$this->form_fields[$i]['name']]]['en'] . "</" . $this->form_fields[$i]['name'] . ">";
                        }

                        $this->answer[$i] .= "</SELECT>";
                        break;

                    case 'radio':
                        $this->answer[$i] .= "<RADIO>\n";
                        $this->answer[$i] .= "<" . $this->form_fields[$i]['name'] . ">" . $this->form_fields[$i]['value'][$GLOBALS[$this->form_fields[$i]['name']]]['en'] . "</" . $this->form_fields[$i]['name'] . ">\n";
                        $this->answer[$i] .= "</RADIO>";
                        break;

                    case 'checkbox':
                        $this->answer[$i] .= "<CHECK>\n";

                        if ($GLOBALS[$this->form_fields[$i]['name']] != '') {
                            $this->answer[$i] .= "<" . $this->form_fields[$i]['name'] . ">" . $this->form_fields[$i]['value'] . "</" . $this->form_fields[$i]['name'] . ">\n";
                        } else {
                            $this->answer[$i] .= "<" . $this->form_fields[$i]['name'] . "></" . $this->form_fields[$i]['name'] . ">\n";
                        }

                        $this->answer[$i] .= "</CHECK>";
                        break;

                    case 'date':
                        if ($this->form_fields[$i]['ctrl'] != '') {
                            $this->control($this->form_fields[$i]['name'], $this->form_fields[$i]['en'], $GLOBALS[$this->form_fields[$i]['name']], $this->form_fields[$i]['ctrl']);
                        }

                        if ($this->form_fields[$i]['name'] == $this->form_key) {
                            $this->form_key_value = $GLOBALS[$this->form_fields[$i]['name']];
                        }

                        $this->answer[$i] .= "<DATUM>\n";
                        $this->answer[$i] .= "<" . $this->form_fields[$i]['name'] . ">" . $GLOBALS[$this->form_fields[$i]['name']] . "</" . $this->form_fields[$i]['name'] . ">\n";
                        $this->answer[$i] .= "</DATUM>";
                        break;

                    case 'stamp':
                        if ($this->form_fields[$i]['name'] == $this->form_key) {
                            $this->form_key_value = $GLOBALS[$this->form_fields[$i]['name']];
                        }

                        $this->answer[$i] .= "<TIMESTAMP>\n";
                        $this->answer[$i] .= "<" . $this->form_fields[$i]['name'] . ">" . $GLOBALS[$this->form_fields[$i]['name']] . "</" . $this->form_fields[$i]['name'] . ">\n";
                        $this->answer[$i] .= "</TIMESTAMP>";
                        break;

                    case 'hidden':
                    case 'submit':
                    case 'reset':
                    default:
                        $this->answer[$i] .= 'no_reg';
                        break;
                }
            }
        }
    }

    /**
     * Read Data structure and build a plain-text response.
     *
     * @param array $response
     * @return string
     */
    function writeSformData(array $response): string
    {
        $content = "<CONTENTS>\n";

        for (Reset($response), $node = 0; $node < count($response); Next($response), $node++) {
            if ($response[$node] != 'no_reg') {
                $content .= $response[$node] . "\n";
            }
        }

        $content .= "</CONTENTS>";

        return addslashes($content);
    }

    /**
     * Read Data structure and build the Internal Data Structure.
     *
     * @param string $line
     * @param string $op
     * @return string
     */
    function readLoadSformData(string $line, string $op): string
    {
        if ((!stristr($line, "<CONTENTS>")) and (!stristr($line, "</CONTENTS>"))) {

            // Premier tag
            $nom = substr($line, 1, strpos($line, '>') - 1);

            // jusqu'a </xxx
            $valeur = substr($line, strpos($line, '>') + 1, (strpos($line, '<', 1) - strlen($nom) - 2));

            if ($valeur == '') {
                $op = $nom;
            }

            switch ($op) {

                case 'TEXT':
                    $op = 'TEXT_S';
                    break;

                case 'TEXT_S':
                    $num = $this->interroFields($nom);

                    if ($num != 'no' or $num == '0') {
                        $valeur = str_replace('&lt;BR /&gt;', chr(13) . chr(10), $valeur);
                        $valeur = str_replace('&lt;br /&gt;', chr(13) . chr(10), $valeur);

                        $this->form_fields[$num]['value'] = $valeur;
                    }
                    break;

                case '/TEXT':
                    break;

                case 'SELECT':
                    $op = 'SELECT_S';
                    break;

                case 'SELECT_S':
                    $num = $this->interroFields($nom);

                    if ($num != 'no' or $num == '0') {
                        $tmp = $this->interroArray($this->form_fields[$num]['value'], $valeur);

                        $this->form_fields[$num]['value'][$tmp]['selected'] = true;
                    }
                    break;

                case '/SELECT':
                    break;

                case 'RADIO':
                    $op = 'RADIO_S';
                    break;

                case 'RADIO_S':
                    $num = $this->interroFields($nom);

                    if ($num != 'no' or $num == '0') {
                        $tmp = $this->interroArray($this->form_fields[$num]['value'], $valeur);

                        $this->form_fields[$num]['value'][$tmp]['checked'] = true;
                    }
                    break;

                case '/RADIO':
                    break;

                case 'CHECK':
                    $op = 'CHECK_S';
                    break;

                case 'CHECK_S':
                    $num = $this->interroFields($nom);

                    if ($num != 'no' or $num == '0') {
                        if ($valeur) {
                            $valeur = true;
                        } else {
                            $valeur = false;
                        }

                        $this->form_fields[$num]['checked'] = $valeur;
                    }
                    break;

                case '/CHECK':
                    break;

                case 'TIMESTAMP':
                case 'DATUM':
                    $op = 'DATUM_S';
                    break;

                case 'DATUM_S':
                    $num = $this->interroFields($nom);

                    if ($num != 'no' or $num == '0') {
                        $this->form_fields[$num]['value'] = $valeur;
                    }
                    break;

                case '/DATUM':
                    break;

                default:
                    break;
            }
        }

        return $op;
    }

    /**
     * Print HTML response.
     *
     * @param string $bg Class for TR or TD
     * @param string $retour Comment for the link at the end of the page OR "not_echo"
     * @param string $action URL to go
     * @return string|null
     */
    function affResponse(string $bg, string $retour = '', string $action = '') // : ?string
    {
        // modif Field en lieu et place des $GLOBALS ....
        settype($str, 'string');

        for ($i = 0; $i < count($this->form_fields); $i++) {
            if (array_key_exists('name', $this->form_fields[$i])) {

                $num_extender = $this->interroFields($this->form_fields[$i]['name'] . 'extender');

                if (array_key_exists($this->form_fields[$i]['name'], $GLOBALS)) {
                    $field = $GLOBALS[$this->form_fields[$i]['name']];
                } else {
                    $field = '';
                }
            } else {
                $num_extender = 'no';
            }

            if (array_key_exists('type', $this->form_fields[$i])) {

                switch ($this->form_fields[$i]['type']) {

                    case 'text':
                    case 'email':
                    case 'url':
                    case 'number':
                        $str .= '<p class="mb-1">' . $this->form_fields[$i]['en'];
                        $str .= '<br />';
                        $str .= '<strong>' . stripslashes($field) . '&nbsp;</strong>';

                        if ($num_extender != 'no') {
                            $str .= ' ' . $this->form_fields[$num_extender]['html'];
                        }

                        $str .= '</p>';
                        break;

                    case 'password':
                        $str .= '<br />' . $this->form_fields[$i]['en'];
                        $str .= '&nbsp;<strong>' . str_repeat('*', strlen($field)) . '&nbsp;</strong>';

                        if ($num_extender != 'no') {
                            $str .= ' ' . $this->form_fields[$num_extender]['html'];
                        }
                        break;

                    case 'checkbox':
                        $str .= '<br />' . $this->form_fields[$i]['en'];

                        if ($field != '') {
                            $str .= '&nbsp;<strong>' . $this->form_fields[$i]['value'] . '&nbsp;</strong>';
                        }

                        if ($num_extender != 'no') {
                            $str .= ' ' . $this->form_fields[$num_extender]['html'];
                        }
                        break;

                    case 'textarea':
                        $str .= '<br />' . $this->form_fields[$i]['en'];
                        $str .= '<br /><strong>' . stripslashes(str_replace(chr(13) . chr(10), '<br />', $field)) . '&nbsp;</strong>';

                        if ($num_extender != 'no') {
                            $str .= ' ' . $this->form_fields[$num_extender]['html'];
                        }
                        break;

                    case 'select':
                        $str .= '<br />' . $this->form_fields[$i]['en'];

                        if (is_array($field)) {
                            for ($j = 0; $j < count($field); $j++) {
                                $str .= '<strong>' . $this->form_fields[$i]['value'][$field[$j]]['en'] . '&nbsp;</strong><br />';
                            }
                        } else {
                            $str .= '&nbsp;<strong>' . $this->form_fields[$i]['value'][$field]['en'] . '&nbsp;</strong>';
                        }

                        if ($num_extender != 'no') {
                            $str .= ' ' . $this->form_fields[$num_extender]['html'];
                        }
                        break;

                    case 'radio':
                        $str .= '<br />' . $this->form_fields[$i]['en'];
                        $str .= '&nbsp;<strong>' . $this->form_fields[$i]['value'][$field]['en'] . '&nbsp;</strong>';

                        if ($num_extender != 'no') {
                            $str .= ' ' . $this->form_fields[$num_extender]['html'];
                        }
                        break;

                    case 'comment':
                        $str .= '<br />';
                        $str .= $this->form_fields[$i]['en'];
                        break;

                    case 'extra':
                        $str .= $this->form_fields[$i]['en'];
                        break;

                    case 'date':
                        $str .= '<br />' . $this->form_fields[$i]['en'];
                        $str .= '&nbsp;<strong>' . $field . '&nbsp;</strong>';

                        if ($num_extender != 'no') {
                            $str .= ' ' . $this->form_fields[$num_extender]['html'];
                        }
                        break;

                    default:
                        break;
                }
            }
        }

        if (($retour != '') and ($retour != 'not_echo')) {
            $str .= '<a href="' . $action . '" class="btn btn-secondary">[ ' . $retour . ' ]</a>';
        }

        $str .= '';

        if ($retour != 'not_echo') {
            echo $str;
        } else {
            return $str;
        }
    }

    /**
     * Control the respect of Data Type
     *
     * @param string $name
     * @param string $nom
     * @param string $valeur
     * @param string $controle
     * @return void
     */
    function control(string $name, string $nom, string $valeur, string $controle) //: void
    {
        $i = $this->interroFields($name);

        if (($this->form_fields[$i]['obligation'] != true) and ($valeur == '')) {
            $controle = '';
        }

        switch ($controle) {

            case 'a-9':
                if (preg_match_all('/([^a-zA-Z0-9 ])/i', $valeur, $trouve)) {
                    $this->error($nom, implode(' ', $trouve[0]));

                    exit();
                }
                break;

            case 'A-9':
                if (preg_match_all('([^A-Z0-9 ])', $valeur, $trouve)) {
                    return false;

                    exit();
                }
                break;

            case 'email':
                $valeur = strtolower($valeur);

                if (preg_match_all('/([^a-z0-9_@.-])/i', $valeur, $trouve)) {
                    $this->error($nom, implode(' ', $trouve[0]));

                    exit();
                }

                if (!preg_match('/^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,4}\$/i', $valeur)) {
                    $this->error($nom, 'Format email invalide');

                    exit();
                }
                break;

            case '0-9':
                if (preg_match_all('/([^0-9])/i', $valeur, $trouve)) {
                    $this->error($nom, implode(' ', $trouve[0]));

                    exit();
                }
                break;

            case '0-9extend':
                if (preg_match_all('/([^0-9_\+\-\*\/\)\]\(\[\& ])/i', $valeur, $trouve)) {
                    $this->error($nom, implode(' ', $trouve[0]));

                    exit();
                }
                break;

            case '0-9number':
                if (preg_match_all('/([^0-9+-., ])/i', $valeur, $trouve)) {
                    $this->error($nom, implode(' ', $trouve[0]));

                    exit();
                }
                break;

            case 'date':
                $date = explode('/', $valeur);

                if (count($date) == 3) {
                    settype($date[0], 'integer');
                    settype($date[1], 'integer');
                    settype($date[2], 'integer');

                    if (!checkdate($date[1], $date[0], $date[2])) {
                        $this->error($nom, 'Date non valide');

                        exit();
                    }
                } else {
                    $this->error($nom, 'Date non valide');

                    exit();
                }
                break;

            default:
                break;
        }
    }

    /**
     * Affiche un message d'erreur et un formulaire de retour.
     *
     * @param string $ibid Identifiant du message à afficher
     * @param string $car  Contenu du message
     * @return void
     */
    function error(string $ibid, string $car): void
    {
        echo '<div class="alert alert-danger">' . Language::affLangue($ibid) . ' =&#62; <span>' . stripslashes($car) . '</span></div>';

        if ($this->form_method == '') {
            $this->form_method = 'post';
        }

        echo "<form action=\"" . $this->url . "\" method=\"" . $this->form_method . "\" name=\"" . $this->form_title . "\" enctype=\"multipart/form-data\">";

        echo $this->printFormHidden();

        echo '<input class="btn btn-secondary" type="submit" name="sformret" value="Retour" />
        </form>';

        include 'footer.php';
    }

    /**
     * Affiche les données d'un formulaire depuis MySQL pour navigation/browse.
     *
     * @param int    $pas          Nombre d'éléments par ligne
     * @param string $mess_passwd  Message pour le champ mot de passe
     * @param string $mess_ok      Message pour le bouton de validation (peut être masqué avec !)
     * @param string $presentation Mode d'affichage ('liste' ou autre)
     * @return void
     */
    function sformBrowseMysql(int $pas, string $mess_passwd, string $mess_ok, string $presentation = ''): void
    {
        $result = sql_query("SELECT key_value, passwd 
                             FROM " . sql_prefix('sform') . " 
                             WHERE id_form='" . $this->form_title . "' 
                             AND id_key='" . $this->form_key . "' 
                             ORDER BY key_value ASC");

        echo "<form action=\"" . $this->url . "\" method=\"post\" name=\"browse\" enctype=\"multipart/form-data\">";
        echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" class=\"ligna\"><tr><td>";
        echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" class=\"lignb\">";

        $hidden = false;

        if (substr($mess_ok, 0, 1) == '!') {
            $mess_ok = substr($mess_ok, 1);
            $hidden = true;
        }

        $ibid = 0;

        while (list($key_value, $passwd) = sql_fetch_row($result)) {
            if ($ibid == 0) {
                echo "<tr class=\"ligna\">";
            }

            $ibid++;

            if ($passwd != '') {
                $red = "<span class=\"text-danger\">$key_value (v)</span>";
            } else {
                $red = "$key_value";
            }

            if ($presentation == 'liste') {
                echo "<td><a href=\"" . $this->url . "&amp;" . $this->submit_value . "=$mess_ok&amp;browse_key=" . urlencode($key_value) . "\" class=\"noir\">$key_value</a></td>";
            } else {
                echo "<td><input type=\"radio\" name=\"browse_key\" value=\"" . urlencode($key_value) . "\"> $red</td>";
            }

            if ($ibid >= $pas) {
                echo "</tr>";

                $ibid = 0;
            }
        }

        echo "</table><br />";

        if ($this->form_password_access != '') {
            echo "$mess_passwd : <input class=\"textbox_standard\" type=\"password\" name=\"password\" value=\"\"> - ";
        }

        if (!$hidden) {
            echo "<input class=\"bouton_standard\" type=\"submit\" name=\"" . $this->submit_value . "\" value=\"$mess_ok\">";
        }

        echo "</td></tr></table></form>";
    }

    /**
     * Lit les données d'un formulaire depuis MySQL.
     *
     * @param string $clef Clé à rechercher
     * @return bool|null   Retourne true si trouvé, false si non trouvé, null si $clef vide
     */
    function sformReadMysql(string $clef) // : ?bool
    {
        global $op;

        $op = (isset($op)) ? $op : '';

        if ($clef != '') {
            $clef = urldecode($clef);

            $result = sql_query("SELECT content 
                                 FROM " . sql_prefix('sform') . " 
                                 WHERE id_form='" . $this->form_title . "' 
                                 AND id_key='" . $this->form_key . "' 
                                 AND key_value='" . addslashes($clef) . "' 
                                 AND passwd='" . $this->form_password_access . "' 
                                 ORDER BY key_value ASC");

            $tmp = sql_fetch_assoc($result);

            if ($tmp) {
                $ibid = explode("\n", $tmp['content']);

                foreach ($ibid as $num => $line) {
                    $op = $this->readLoadSformData(stripslashes($line), $op);
                }

                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Insère une réponse de formulaire dans la base MySQL.
     *
     * @param array $response Données du formulaire
     * @return string|null    Message d'erreur en cas d'échec, sinon null
     */
    function sformInsertMysql(array $response) // : ?string
    {
        $content = $this->writeSformData($response);

        $sql = "INSERT INTO " . sql_prefix('sform') . " (id_form, id_key, key_value, passwd, content) 
                VALUES ('" . $this->form_title . "', '" . $this->form_key . "', '" . $this->form_key_value . "', '" . $this->form_password_access . "', '$content')";

        if (!$result = sql_query($sql)) {
            return 'Error Sform : Insert DB';
        }
    }

    /**
     * Supprime une entrée de formulaire dans MySQL.
     *
     * @return string|null Message d'erreur en cas d'échec, sinon null
     */
    function sformDeleteMysql() // : ?string
    {
        $sql = "DELETE FROM " . sql_prefix('sform') . " 
                WHERE id_form='" . $this->form_title . "' 
                AND id_key='" . $this->form_key . "' 
                AND key_value='" . $this->form_key_value . "'";

        if (!$result = sql_query($sql)) {
            return 'Error Sform : Delete DB';
        }
    }

    /**
     * Modifie une entrée de formulaire existante dans MySQL.
     *
     * @param array $response Données à mettre à jour
     * @return string|null    Message d'erreur en cas d'échec, sinon null
     */
    function sformModifyMysql(array $response) // : ?string
    {
        $content = $this->writeSformData($response);

        $sql = "UPDATE " . sql_prefix('sform') . " 
                SET passwd='" . $this->form_password_access . "', content='$content' 
                WHERE (id_form='" . $this->form_title . "' AND id_key='" . $this->form_key . "' AND key_value='" . $this->form_key_value . "')";

        if (!$result = sql_query($sql)) {
            return 'Error Sform : Update DB';
        }
    }

    /**
     * Lit les données d'un formulaire depuis MySQL et analyse le XML.
     *
     * @param string $clef Clé à rechercher
     * @return bool        Retourne true si trouvé et analysé, false sinon
     */
    function sformReadMysqlXml(string $clef): bool
    {
        if ($clef != '') {
            $clef = urldecode($clef);

            $result = sql_query("SELECT content FROM " . sql_prefix('sform') . " 
                                 WHERE id_form='" . $this->form_title . "' 
                                 AND id_key='" . $this->form_key . "' 
                                 AND key_value='$clef' 
                                 AND passwd='" . $this->form_password_access . "' 
                                 ORDER BY key_value ASC");

            $tmp = sql_fetch_assoc($result);

            $analyseur_xml = xml_parser_create();

            xml_parser_set_option($analyseur_xml, XML_OPTION_CASE_FOLDING, 0);
            xml_parse_into_struct($analyseur_xml, $tmp['content'], $value, $tag);

            $this->sformXmlTag($value);

            xml_parser_free($analyseur_xml);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Traite les tags XML d'un formulaire et met à jour les valeurs des champs.
     *
     * @param array $value Structure renvoyée par xml_parse_into_struct
     * @return void
     */
    function sformXmlTag(array $value): void
    {
        foreach ($value as $num => $val) {

            // open, complete, close
            if ($val['type'] == 'complete') {

                // Le nom du tag
                $nom    = $val['tag'];

                // La valeur du champs
                $valeur = $val['value'];

                $idchamp = $this->interroFields($nom);

                switch ($value[$num - 1]['tag']) {

                    case 'TEXT':
                        $valeur = str_replace('&lt;BR /&gt;', chr(13) . chr(10), $valeur);
                        $valeur = str_replace('&lt;br /&gt;', chr(13) . chr(10), $valeur);

                        $this->form_fields[$idchamp]['value'] = $valeur;
                        break;

                    case 'SELECT':
                        $tmp = $this->interroArray($this->form_fields[$idchamp]['value'], $valeur);

                        $this->form_fields[$idchamp]['value'][$tmp]['selected'] = true;
                        break;

                    case 'RADIO':
                        $tmp = $this->interroArray($this->form_fields[$idchamp]['value'], $valeur);

                        $this->form_fields[$idchamp]['value'][$tmp]['checked'] = true;
                        break;

                    case 'CHECK':
                        if ($valeur) {
                            $valeur = true;
                        } else {
                            $valeur = false;
                        }

                        $this->form_fields[$idchamp]['checked'] = $valeur;
                        break;

                    case 'DATUM':
                        $this->form_fields[$idchamp]['value'] = $valeur;
                        break;

                    case 'TIMESTAMP':
                        $this->form_fields[$idchamp]['value'] = $valeur;
                        break;
                }
            }
        }
    }
}
