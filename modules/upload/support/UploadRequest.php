<?php

namespace Modules\Upload\Support;


class UploadRequest
{

    /**
     * Récupère les fichiers uploadés.
     *
     * Si $key est fourni, renvoie uniquement les fichiers pour ce champ sous forme de tableau uniforme.
     * Sinon, renvoie tous les fichiers $_FILES.
     *
     * @param string|null $key Nom du champ input (ex: 'pcfile')
     * @return array
     */
    public static function all(?string $key = null): array
    {
        if ($key === null) {
            return $_FILES ?? [];
        }

        if (!isset($_FILES[$key])) {
            return [];
        }

        $file = $_FILES[$key];

        // Upload multiple
        if (is_array($file['name'])) {
            $result = [];
            foreach ($file['name'] as $i => $name) {
                $result[] = [
                    'name'     => $name,
                    'type'     => $file['type'][$i],
                    'size'     => $file['size'][$i],
                    'tmp_name' => $file['tmp_name'][$i],
                    'error'    => $file['error'][$i],
                ];
            }
            return $result;
        }

        // Upload simple
        return [
            [
                'name'     => $file['name'],
                'type'     => $file['type'],
                'size'     => $file['size'],
                'tmp_name' => $file['tmp_name'],
                'error'    => $file['error'],
            ],
        ];
    }

    /**
     * Retourne un fichier uploadé par nom de champ.
     *
     * @param string $field
     * @return array<string, mixed>|null
     */
    public static function file(string $field): ?array
    {
        return $_FILES[$field] ?? null;
    }

    /**
     * Indique si un fichier a été posté pour un champ donné.
     *
     * @param string $field
     * @return bool
     */
    public static function has(string $field): bool
    {
        return isset($_FILES[$field]) && $_FILES[$field]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * Retourne un tableau associatif [field => tmp_name].
     *
     * Utilisation : $tmpFileNames = UploadRequest::tmpFileNames();
     * 
     * @return array<string, string>
     */
    public static function tmpFileNames(): array
    {
        $tmpNames = [];

        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                $tmpNames[$key] = $value['tmp_name'] ?? '';
            }
        }

        return $tmpNames;
    }

}
