<?php

namespace App\Support;


/**
 * Classe FileManagement
 *
 * Fournit des méthodes pour formater et obtenir la taille de fichiers
 * avec différents niveaux de précision et unités.
 */
class FileManagement
{

    /**
     * Unités de mesure utilisées pour le formatage des tailles de fichiers
     *
     * @var array
     */
    public $units = [
        'B', 
        'KB', 
        'MB', 
        'GB', 
        'TB'
    ];


    /**
     * Constructeur
     *
     * @param array|null $units Optionnel : permet de redéfinir les unités par défaut
     */
    public function __construct(?array $units = null)
    {
        if ($units !== null) {
            $this->units = $units;
        }
    }

    /**
     * Formate une taille de fichier donnée en octets en une unité lisible
     *
     * @param int $fileSize Taille en octets
     * @param int $precision Nombre de décimales
     * @return string Taille formatée (ex : 1.23 MB)
     */
    public function fileSizeFormat(int $fileSize, int $precision = 2): string
    {
        $bytes = max($fileSize, 0);

        return $this->formatSize($bytes, $precision);
    }

    /**
     * Retourne la taille d'un fichier sur le disque avec un format lisible
     *
     * @param string $fileName Chemin vers le fichier
     * @param int $precision Nombre de décimales
     * @return string Taille formatée
     */
    public function fileSizeAuto(string $fileName, int $precision = 2): string
    {
        $bytes = @filesize($fileName);
        $bytes = max($bytes, 0);

        return $this->formatSize($bytes, $precision);
    }

    /**
     * Formate une taille en octets dans une unité lisible (B, KB, MB, GB, TB).
     *
     * Cette méthode calcule l'unité la plus appropriée en fonction de la taille
     * et renvoie une chaîne formatée avec le nombre arrondi au nombre de décimales
     * spécifié.
     *
     * @param float $bytes     Taille en octets.
     * @param int   $precision Nombre de décimales à afficher.
     *
     * @return string Taille formatée avec unité.
     */
    protected function formatSize(float $bytes, int $precision): string
    {
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($this->units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $this->units[$pow];
    }

    /**
     * Retourne la taille d'un fichier sur le disque dans une unité spécifique
     *
     * @param string $fileName Chemin vers le fichier
     * @param string $unitType Unité souhaitée (B, KB, MB, GB, TB)
     * @return string Taille formatée
     */
    public function fileSizeOption(string $fileName, string $unitType): string
    {
        switch ($unitType) {
            case $this->units[0]:
                $fileSize = number_format(filesize(trim($fileName)), 1);
                break;

            case $this->units[1]:
                $fileSize = number_format(filesize(trim($fileName)) / 1024, 1);
                break;

            case $this->units[2]:
                $fileSize = number_format(filesize(trim($fileName)) / 1024 / 1024, 1);
                break;

            case $this->units[3]:
                $fileSize = number_format(filesize(trim($fileName)) / 1024 / 1024 / 1024, 1);
                break;

            case $this->units[4]:
                $fileSize = number_format(filesize(trim($fileName)) / 1024 / 1024 / 1024 / 1024, 1);
                break;

            default:
                $fileSize = number_format(filesize(trim($fileName)), 1);
                $unitType = 'B';
        }

        return $fileSize . ' ' . $unitType;
    }
}
