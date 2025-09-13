<?php

namespace Npds\View\Engines;

use Exception;
use Throwable;
use Npds\View\Contracts\EngineInterface;


class PhpEngine implements EngineInterface
{

    /**
     * Récupère le rendu d'une vue PHP.
     *
     * @param string $path Chemin complet du fichier de vue.
     * @param array  $data Tableau des variables à injecter dans la vue.
     *
     * @return string Contenu HTML rendu de la vue.
     */
    public function get(string $path, array $data = array()): string
    {
        return $this->evaluatePath($path, $data);
    }

    /**
     * Évalue le fichier de vue en PHP et retourne son contenu.
     *
     * Les variables passées dans $data sont extraites dans la portée de la vue.
     * Le rendu est capturé via un tampon de sortie pour éviter toute fuite.
     *
     * @param string $path Chemin complet du fichier de vue.
     * @param array  $data Variables à injecter dans la vue.
     *
     * @return string Contenu rendu de la vue.
     *
     * @throws \Exception|\Throwable Si une erreur survient pendant le rendu.
     */
    protected function evaluatePath(string $__path, array $__data): string
    {
        $obLevel = ob_get_level();

        ob_start();

        // Injection des variables dans l’espace de la vue
        foreach ($__data as $__variable => $__value) {
            if (in_array($__variable, array('__path', '__data'))) {
                continue;
            }

            ${$__variable} = $__value;
        }

        // Nettoyage des variables temporaires avant le rendu de la vue
        unset($__data, $__variable, $__value);

        // Évaluation du contenu de la vue dans un bloc try/catch afin de capturer
        // toute sortie générée avant qu'une erreur ou exception ne survienne.
        // Cela évite que des parties incomplètes de la vue ne s'affichent.
        try {
            include $__path;
        }
        catch (Exception $e) {
            $this->handleViewException($e, $obLevel);
        }
        catch (Throwable $e) {
            $this->handleViewException($e, $obLevel);
        }

        return ltrim(ob_get_clean());
    }

    /**
     * Gère les exceptions survenues lors du rendu d'une vue.
     *
     * Nettoie tous les tampons de sortie ouverts jusqu'au niveau initial.
     *
     * @param \Exception|\Throwable $e        L'exception levée pendant le rendu.
     * @param int                   $obLevel Niveau initial du tampon de sortie.
     *
     * @return void
     *
     * @throws \Exception|\Throwable Relance l'exception après nettoyage.
     */
    protected function handleViewException(Exception|Throwable $e, int $obLevel): void
    {
        while (ob_get_level() > $obLevel) {
            ob_end_clean();
        }

        throw $e;
    }

}
