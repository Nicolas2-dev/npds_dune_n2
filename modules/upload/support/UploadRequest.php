<?php

namespace Modules\Upload\Support;


class UploadRequest
{

    /**
     * Retourne tous les fichiers uploadés.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return $_FILES ?? [];
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
