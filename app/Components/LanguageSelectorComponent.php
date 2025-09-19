<?php

namespace App\Components;

use App\Support\Facades\Language;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::LanguageSelector("index.php"); ?>
*/

class LanguageSelectorComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $url = is_array($params) ? ($params[0] ?? "index.php") : $params;
        
        return Language::affLocalLangue($url, "choice_user_language", "");
    }
}
