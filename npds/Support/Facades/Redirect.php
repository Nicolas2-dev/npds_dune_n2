<?php

namespace Npds\Support\Facades;

use Npds\Http\Response as HttpResponse;
use Npds\Support\Facades\Request;

class Redirect
{

    /**
     * Redirige vers une URL donnée.
     *
     * @param string $path    Le chemin ou URL de destination.
     * @param int    $status  Le code HTTP de redirection (par défaut 302).
     * @param array  $headers Les en-têtes supplémentaires à envoyer.
     *
     * @return HttpResponse  Une instance de HttpResponse avec redirection.
     */
    public static function to(string $path, int $status = 302, array $headers = []): HttpResponse
    {
        $url = site_url($path);

        return static::createRedirectResponse($url, $status, $headers);
    }

    /**
     * Redirige vers la page précédente ou vers la page d'accueil si aucune précédente.
     *
     * @param int   $status  Le code HTTP de redirection (par défaut 302).
     * @param array $headers Les en-têtes supplémentaires à envoyer.
     *
     * @return HttpResponse  Une instance de HttpResponse avec redirection.
     */
    public static function back(int $status = 302, array $headers = []): HttpResponse
    {
        $url = Request::previous() ?: site_url();

        return static::createRedirectResponse($url, $status, $headers);
    }

    /**
     * Crée une réponse HTTP de redirection.
     *
     * @param string $url     L'URL de destination.
     * @param int    $status  Le code HTTP de redirection.
     * @param array  $headers Les en-têtes HTTP supplémentaires.
     *
     * @return HttpResponse  Une instance de HttpResponse configurée pour la redirection.
     */
    protected static function createRedirectResponse(string $url, int $status, array $headers): HttpResponse
    {
        $content = '
<html>
<body onload="redirect_to(\'' .$url .'\');"></body>
<script type="text/javascript">function redirect_to(url) { window.location.href = url; }</script>
</body>
</html>';

        $headers['Location'] = $url;

        return new HttpResponse($content, $status, $headers);
    }

}
