<?php

namespace Npds\View\Engines;

use Exception;
use Throwable;

use Whoops\Exception\ErrorException;
use Npds\View\Contracts\CompilerInterface;
use Npds\Application\Exceptions\FatalThrowableError;


class CompilerEngine extends PhpEngine
{

    /**
     * Le compilateur de templates utilisé par ce moteur.
     *
     * @var CompilerInterface
     */
    protected CompilerInterface $compiler;

    /**
     * Liste des dernières vues compilées.
     *
     * @var array<int, string>  Tableau indexé contenant les chemins ou contenus compilés.
     */
    protected array $lastCompiled = [];


    /**
     * Constructeur.
     *
     * Initialise le moteur avec une instance de TemplateCompiler.
     *
     * @param CompilerInterface $compiler L'instance du compilateur de templates à utiliser.
     */
    public function __construct(CompilerInterface $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * Récupère et compile une vue, puis retourne son contenu évalué.
     *
     * @param string $path  Chemin de la vue.
     * @param array<string, mixed> $data  Données passées à la vue.
     * @return string Résultat du rendu de la vue.
     */
    public function get(string $path, array $data = []): string
    {
        $this->lastCompiled[] = $path;

        // Si cette vue donnée a expiré, cela signifie qu'elle a simplement été modifiée depuis
        // il a été compilé pour la dernière fois, nous allons recompiler les vues afin de pouvoir évaluer un
        // nouvelle copie de la vue. Nous transmettrons au compilateur le chemin de la vue.
        if ($this->compiler->isExpired($path)) {
            $this->compiler->compile($path);
        }

        $compiled = $this->compiler->getCompiledPath($path);

        // Une fois que nous aurons le chemin d'accès au fichier compilé, nous évaluerons les chemins avec
        // PHP typique comme n'importe quel autre modèle. Nous conservons également une pile de vues
        // qui ont été rendus pour que les messages d'exception de droit soient générés.
        $results = $this->evaluatePath($compiled, $data);

        array_pop($this->lastCompiled);

        return $results;
    }

    /**
     * Gère une exception survenue lors du rendu d'une vue.
     *
     * @param \Throwable $e       Exception ou erreur rencontrée.
     * @param int $obLevel        Niveau du buffer de sortie.
     * @return void
     *
     * @throws \ErrorException
     */
    protected function handleViewException(Throwable $e, int $obLevel): void
    {
        if (! $e instanceof Exception) {
            $e = new FatalThrowableError($e);
        }

        $e = new ErrorException($this->getMessage($e), 0, 1, $e->getFile(), $e->getLine(), $e);

        parent::handleViewException($e, $obLevel);
    }

    /**
     * Génère un message d'erreur enrichi avec le chemin de la vue en cause.
     *
     * @param \Throwable $e  Exception ou erreur rencontrée.
     * @return string Message formaté.
     */
    protected function getMessage(Throwable $e): string
    {
        $path = last($this->lastCompiled);

        return $e->getMessage() .' (View: ' .realpath($path) .')';
    }

    /**
     * Retourne l'instance du compilateur de vues.
     *
     * @return \Npds\View\Contracts\CompilerInterface
     */
    public function getCompiler(): CompilerInterface
    {
        return $this->compiler;
    }

}
