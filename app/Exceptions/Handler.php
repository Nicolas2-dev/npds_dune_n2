<?php

namespace App\Exceptions;

use Exception;
use App\Support\Facades\Theme;
use Npds\Support\Facades\View;
use Npds\Exceptions\Http\HttpException;
use Npds\Exceptions\Handler as BaseHandler;
use App\Support\Facades\Assets as AssetManager;

class Handler extends BaseHandler
{

    /**
     * Journalise une exception dans le fichier errors.log.
     *
     * @param Exception $e L'exception à journaliser
     *
     * @return void
     */
    public function report(Exception $e): void
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
     * Rendu d'une exception en réponse HTTP.
     *
     * Si l'exception est une HttpException et qu'une vue correspondante existe,
     * elle sera affichée avec la mise en page par défaut.
     *
     * @param Exception $e L'exception à afficher
     *
     * @return void
     */
    public function render(Exception $e): void
    {
        // Journalisation personnalisée
        //$this->report($e);

        // Laisse le parent gérer le rendu
        parent::render($e);
    }

}
