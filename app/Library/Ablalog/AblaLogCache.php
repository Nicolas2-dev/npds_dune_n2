<?php

namespace App\Library\Ablalog;

use RuntimeException;


class AblaLogCache
{

    /**
     * Chemin complet vers le fichier de cache.
     *
     * @var string
     */
    private string $filePath;

    /**
     * Contenu à écrire dans le fichier de cache (commence par l'ouverture PHP).
     *
     * @var string
     */
    private string $content = "<?php\n";

    /**
     * Constructeur de la classe.
     *
     * @param string $filePath Chemin complet du fichier de cache.
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Charge un fichier de cache existant (si présent).
     */
    public function load(): void
    {
        if (!file_exists($this->filePath)) {
            throw new RuntimeException("Le fichier de cache {$this->filePath} est introuvable.");
        }

        include $this->filePath;
    }

    /**
     * Ajoute une variable PHP au fichier.
     */
    public function addVar(string $name, mixed $value): void
    {
        if (is_string($value)) {
            $value = addslashes($value);

            $this->content .= "\$$name = \"$value\";\n";
        } else {
            $this->content .= "\$$name = $value;\n";
        }
    }

    /**
     * Ajoute une variable de tableau PHP au fichier.
     */
    public function addArray(string $arrayName, int $index, int $subIndex, mixed $value): void
    {
        if (is_string($value)) {
            $value = addslashes($value);
            $this->content .= "\${$arrayName}[$index][$subIndex] = \"$value\";\n";
        } else {
            $this->content .= "\${$arrayName}[$index][$subIndex] = $value;\n";
        }
    }

    /**
     * Sauvegarde dans le fichier.
     */
    public function save(): void
    {
        $this->content .= "?>\n";

        file_put_contents($this->filePath, $this->content);
    }

}
