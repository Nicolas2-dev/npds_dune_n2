<?php

namespace Npds\Support;


class HelperGenerator
{

    /**
     * Génère un fichier d'autoload pour tous les fichiers `helpers.php` trouvés dans
     * les dossiers NPDS, Library et Support.
     *
     * Cette méthode parcourt récursivement les répertoires suivants :
     * - npds
     * - app/Library
     * - app/Support
     *
     * Pour chaque fichier `helpers.php` trouvé, elle ajoute un `require_once` dans
     * le fichier `storage/generated_helpers_autoload.php`.  
     * Le fichier est réécrit uniquement si son contenu a changé.
     *
     * @return void
     */
    public static function generate(): void
    {
        $folders = [
            __DIR__ . '/../../npds',
            __DIR__ . '/../../app/Library',
            __DIR__ . '/../../app/Support',
        ];

        $helpers = [];

        foreach ($folders as $root) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($root)
            );

            foreach ($iterator as $file) {
                if ($file->getFilename() === 'helpers.php') {
                    $helpers[] = str_replace(__DIR__ . '/../../', '', $file->getPathname());
                }
            }
        }

        $outputFile = __DIR__ . '/../../storage/generated_helpers_autoload.php';

        $content = "<?php\n\n";
        foreach ($helpers as $path) {
            $content .= "require_once __DIR__ . '/../$path';\n";
        }

        if (!file_exists($outputFile) || file_get_contents($outputFile) !== $content) {
            file_put_contents($outputFile, $content);
        }
    }
}
