<?php

namespace Npds\Support;

class HelperGenerator
{
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
