<?php

namespace App\Exceptions;

use Throwable;
use Npds\Exceptions\Handler as BaseHandler;

class Handler extends BaseHandler
{

    /**
     * Journalise une exception dans le fichier errors.log.
     *
     * @param Throwable $e L'exception à journaliser
     *
     * @return void
     */
    public function report(Throwable $e): void
    {
        $message = $e->getMessage();

        $code = $e->getCode();
        $file = $e->getFile();
        $line = $e->getLine();

        $trace = $e->getTraceAsString();

        $date = date('M d, Y G:iA');

        $message = "Exception information:\n
        Date: {$date}\n
        Message: {$message}\n
        Code: {$code}\n
        File: {$file}\n
        Line: {$line}\n
        Stack trace:\n
        {$trace}\n
        ---------\n\n";

        //
        $path = STORAGE_PATH .'framework' .DS .'errors.log';

        file_put_contents($path, $message, FILE_APPEND);

        // Laisse le parent envoye le mail a l'admin
        parent::report($e);
    }

    /**
     * Rendu d'une exception.
     *
     * Affiche une exception en choisissant la vue appropriée si elle existe.
     * Si l'exception est une HttpException et qu'une vue correspondante est disponible,
     * cette vue sera utilisée avec la mise en page par défaut.
     *
     * @param Throwable $e L'exception à afficher
     *
     * @return void
     */
    public function render(Throwable $e): void
    {
        // Laisse le parent gérer le rendu
        parent::render($e);
    }

}
