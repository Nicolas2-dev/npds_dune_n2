<?php

namespace Modules\Upload\Support;


class UploadAttachment
{

    /**
     * Affiche les fichiers uploadés pour un post dans un forum.
     *
     * @param string $apli     Nom de l'application.
     * @param int $post_id     Identifiant du post.
     * @param bool|int $Mmod   Marqueur pour afficher les fichiers non visibles (0 = non, 1 = oui).
     *
     * @return string|null     HTML des pièces jointes ou null si aucune pièce jointe.
     */
    public static function display_upload(
        string $apli,
        int $post_id,
        bool|int $Mmod
    ): ?string {
        $att = self::getAttachments($apli, $post_id, 0, $Mmod);

        if (!is_array($att)) {
            return null;
        }

        $att_count = count($att);

        $attachments = '<div class="list-group">
            <div class="list-group-item d-flex justify-content-start align-items-center mt-2">
                <img class="n-smil" src="assets/images/forum/subject/07.png" alt="icon_pieces jointes" />
                <span class="text-body-secondary p-2">' . upload_translate('Pièces jointes') . '</span><a data-bs-toggle="collapse" href="#lst_pj' . $post_id . '"><i data-bs-toggle="tooltip" data-bs-placement="top" title="" class="toggle-icon fa fa-lg me-2 fa-caret-up"></i></a>
                <span class="badge bg-secondary ms-auto">' . $att_count . '</span>
            </div>
            <div id="lst_pj' . $post_id . '" class="collapse show">';

        for ($i = 0; $i < $att_count; $i++) {
            $att_id        = $att[$i]["att_id"];
            $att_name      = $att[$i]["att_name"];
            $att_path      = $att[$i]["att_path"];
            $att_type      = $att[$i]["att_type"];
            $att_size      = (int) $att[$i]["att_size"];
            $compteur      = $att[$i]["compteur"];
            $visible       = $att[$i]["visible"];
            $att_inline    = $att[$i]["inline"];
            $marqueurV     = (!$visible) ? '@' : '';

            $path = "$att_path/$att_id.$apli." . $marqueurV . "$att_name";

            $attachments .= self::getAttachmentUrl(
                $apli,
                $att_id,
                $path,
                $att_type,
                $att_size,
                $att_inline,
                $compteur,
                $visible,
                $Mmod
            );

            $att_list[$att_id] = $att_name;
        }

        $attachments .= '</div>
            </div>';

        return $attachments;
    }

    /**
     * Récupère la liste des attachements ou un attachement spécifique pour un post donné.
     *
     * @param string $apli    Nom de l'application.
     * @param int $post_id    Identifiant du post auquel les fichiers sont attachés.
     * @param int $att_id     Identifiant de l'attachement à récupérer (0 pour tous).
     * @param int|bool $Mmod  Marqueur pour inclure les fichiers non visibles (0 = non, 1 = oui).
     *
     * @return array<int, array<string, mixed>>|string
     *         Retourne un tableau d'attachements si trouvés, sinon une chaîne vide.
     */
    public static function getAttachments(
        string $apli,
        int $post_id,
        int $att_id = 0,
        int|bool $Mmod = 0
    ): array|string {
        global $upload_table;

        $query = "SELECT att_id, att_name, att_type, att_size, att_path, inline, compteur, visible 
                    FROM $upload_table 
                    WHERE apli='$apli' AND post_id='$post_id'";

        if ($att_id > 0) {
            $query .= " AND att_id=$att_id";
        }

        if (!$Mmod) {
            $query .= " AND visible=1";
        }

        $query .= " ORDER BY att_type,att_name";
        $result = sql_query($query);

        $i = 0;

        while ($attach = sql_fetch_assoc($result)) {
            $att[$i] = $attach;
            $i++;
        }

        return ($i == 0) ? '' : $att;
    }

    /**
     * Retourne l'attachement formaté pour affichage ou téléchargement.
     *
     * Résout le chemin du fichier, génère l'URL, et choisit le rendu
     * adapté selon le type MIME du fichier.
     *
     * @param string $apli        Nom de l'application appelante.
     * @param int $att_id         Identifiant unique de l'attachement.
     * @param string $att_path    Chemin relatif du fichier.
     * @param string $att_type    Type MIME du fichier.
     * @param int $att_size       Taille du fichier en octets.
     * @param int $att_inline     Indique si l'affichage doit être inline (1) ou non (0).
     * @param int $compteur       Compteur d'accès ou d'affichage.
     * @param int $visible        Indique si le fichier est visible (1) ou non (0).
     * @param bool $Mmod          Marqueur de modification spécifique.
     * @param array|null $userdata Données de l'utilisateur (optionnel).
     *
     * @return string HTML ou URL du fichier prêt à être affiché.
     */
    public static function getAttachmentUrl(
        string $apli,
        int $att_id,
        string $att_path,
        string $att_type,
        int $att_size,
        int $att_inline = 0,
        int $compteur = 0,
        int $visible = 0,
        bool $Mmod = false,
        ?array $userdata = null
    ): string
    {
        // Résolution du chemin et du fichier
        $result = self::resolveAttachment($att_path);

        // Si resolveAttachment retourne une erreur HTML
        if (is_string($result)) {
            return $result;
        }

        // Nom du fichier réel sur le serveur
        $att_file = $result['file'];

        // Nom affiché à l’utilisateur
        $att_name = $result['name'];
        
        // Chemin complet sur le serveur
        $att_path = $result['path'];

        // Gestion du marqueur Mmod
        $marqueurM = '';
        if ($Mmod && $userdata) {
            $marqueurM = '&amp;Mmod=' . substr($userdata[2] ?? '', 8, 6);
        }

        // URL de téléchargement
        $att_url = 'getfile.php?att_id=' . $att_id
                . '&amp;apli=' . $apli
                . $marqueurM
                . '&amp;att_name=' . rawurlencode($att_file);

        // Texte visible si le fichier est non visible
        $visible_wrn = ($visible != 1)
            ? '&nbsp;<span class="text-danger" style="font-size: .65rem;">' 
                . upload_translate('Fichier non visible') . '</span>'
            : '';

        // Retourne le rendu selon le type
        return match (self::displayMode($att_type)) {
            UploadMode::IMG => UploadMineRender::renderImg([
                'name'      => $att_name,
                'url'       => $att_url,
                'visible'   => $visible_wrn
            ]),

            UploadMode::PLAINTEXT => UploadMineRender::renderText([
                'name'      => $att_name,
                'path'      => $att_path,
                'visible'   => $visible_wrn
            ]),

            UploadMode::HTML => UploadMineRender::renderHtml([
                'name'      => $att_name,
                'path'      => $att_path,
                'visible'   => $visible_wrn
            ]),

            UploadMode::SWF => UploadMineRender::renderShockwaveFlash([
                'url'       => $att_url,
                'path'      => $att_path,
                'visible'   => $visible_wrn
            ]),

            UploadMode::VIDEO => UploadMineRender::renderVideo([
                'name'      => $att_name,
                'url'       => $att_url
            ]),

            UploadMode::AUDIO => UploadMineRender::renderAudio([
                'name'      => $att_name,
                'url'       => $att_url
            ]),

            UploadMode::LINK => UploadMineRender::renderLink([
                'name'     => $att_name,
                'url'      => $att_url,
                'type'     => $att_type,
                'size'     => $att_size,
                'visible'  => $visible_wrn,
                'compteur' => $compteur,
            ]),

            default => UploadMineRender::renderLink([
                'name'     => $att_name,
                'url'      => $att_url,
                'type'     => $att_type,
                'size'     => $att_size,
                'visible'  => $visible_wrn,
                'compteur' => $compteur,
            ]),
        };
    }

    /**
     * Détermine le mode d'upload en fonction du type MIME du fichier.
     *
     * @param string $att_type Type MIME du fichier.
     * 
     * @return UploadMode Le mode d'upload correspondant.
     */
    public static function displayMode(string $att_type): UploadMode
    {
        return match ($att_type) {
            'image/gif', 'image/png', 'image/jpeg', 'image/pjpeg', 'image/svg+xml' => UploadMode::IMG,
            'text/plain' => UploadMode::PLAINTEXT,
            'text/html' => UploadMode::HTML,
            'application/x-shockwave-flash' => UploadMode::SWF,
            'video/mpeg' => UploadMode::VIDEO,
            'audio/mpeg' => UploadMode::AUDIO,
            default => UploadMode::LINK,
        };
    }

    /**
     * Résout les informations d'une pièce jointe à partir de son chemin relatif.
     *
     * @param string $att_path Chemin relatif du fichier depuis le document root.
     * 
     * @return array{path: string, name: string, file: string}|string
     *         Retourne un tableau contenant le chemin absolu, le nom sans extension
     *         et le nom complet du fichier, ou une chaîne HTML d'erreur si le fichier
     *         n'existe pas.
     */
    public static function resolveAttachment(string $att_path): array|string
    {
        global $DOCUMENTROOT;

        // ex: report.pdf
        $base_name  = basename($att_path);                     
        
        // ex: report
        $att_name   = pathinfo($base_name, PATHINFO_FILENAME); 
        
        $full_path  = $DOCUMENTROOT . $att_path;

        if (!is_file($full_path)) {
            return '&nbsp;<span class="text-danger" style="font-size: .65rem;">'
                . upload_translate('Fichier non trouvé') . ' : ' . $base_name . '</span>';
        }

        return [
            // chemin absolu pour ouvrir le fichier
            'path' => $full_path, 
            
            // nom sans extension (utile pour affichage)
            'name' => $att_name, 
            
            // nom complet avec extension
            'file' => $base_name   
        ];
    }

    /**
     * Insère une pièce jointe dans la base de données.
     *
     * @param string $apli       Nom de l'application.
     * @param int $IdPost        Identifiant du post.
     * @param int $IdTopic       Identifiant du topic.
     * @param int $IdForum       Identifiant du forum.
     * @param string $name       Nom du fichier.
     * @param string $path       Chemin complet du fichier.
     * @param string $inline     Indique si le fichier est en ligne ('A' = attaché par défaut).
     * @param int $size          Taille du fichier en octets (0 pour calculer automatiquement).
     * @param string $type       Type MIME du fichier.
     *
     * @return int               ID de la pièce jointe insérée, ou -1 en cas d'échec.
     */
    public static function insertAttachment(
        string $apli,
        int $IdPost,
        int $IdTopic,
        int $IdForum,
        string $name,
        string $path,
        int $size = 0,
        string $type = ''
    ): int {
        global $upload_table, $visible_forum;

        $size = empty($size) ? filesize($path) : $size;
        $type = empty($type) ? UploadMimeType::getDefaultMime() : $type;

        $stamp = time();

        $sql = "INSERT INTO $upload_table 
                VALUES (0, '$IdPost', '$IdTopic','$IdForum', '$stamp', '$name', '$type', '$size', '$path', '1', '$apli', '0', '$visible_forum')";
        $ret = sql_query($sql);

        if (!$ret) {
            return -1;
        }

        return sql_last_id();
    }

    /**
     * Supprime une pièce jointe de la base de données et du disque.
     *
     * @param string $apli        Nom de l'application.
     * @param string $upload_dir  Chemin du dossier de téléchargement.
     * @param int $id             ID de la pièce jointe.
     * @param string $att_name    Nom du fichier à supprimer.
     *
     * @return void
     */
    public static function deleteAttachment(
        string $apli,
        string $upload_dir,
        int $id,
        string $att_name
    ): void {
        global $upload_table;

        @unlink("$upload_dir/$id.$apli.$att_name");

        $sql = "DELETE FROM $upload_table 
                WHERE att_id= '$id'";

        sql_query($sql);
    }

    /**
     * Supprime un ou plusieurs fichiers joints et leurs entrées en base de données.
     *
     * @param int|int[] $del_att  ID unique ou tableau d'ID des pièces jointes à supprimer.
     *
     * @return void
     */
    public static function delete(int|array $del_att): void
    {
        global $upload_table, $apli, $DOCUMENTROOT;

        $rep = $DOCUMENTROOT;

        $del_att = is_array($del_att) ? implode(',', $del_att) : $del_att;

        $sql = "SELECT att_id, att_name, att_path 
                FROM $upload_table 
                WHERE att_id IN ($del_att)";

        $result = sql_query($sql);

        while (list($att_id, $att_name, $att_path) = sql_fetch_row($result)) {
            @unlink($rep . "$att_path/$att_id.$apli.$att_name");
        }

        $sql = "DELETE FROM $upload_table 
                WHERE att_id IN ($del_att)";

        sql_query($sql);
    }

    /**
     * Met à jour le mode d'affichage (inline) pour un ou plusieurs fichiers joints.
     *
     * @param array<int, string> $inline_att Tableau associatif [att_id => mode] des pièces jointes.
     *
     * @return void
     */
    public static function update_inline(array $inline_att): void
    {
        global $upload_table;

        if (is_array($inline_att)) {
            foreach ($inline_att as $id => $mode) {
                $sql = "UPDATE $upload_table 
                        SET inline='$mode' 
                        WHERE att_id=$id";

                sql_query($sql);
            }
        }
    }

    /**
     * Renomme les fichiers joints pour refléter leur état de visibilité.
     *
     * Les fichiers visibles ont leur nom sans "@", tandis que les fichiers non visibles
     * sont préfixés par "@" dans le nom de fichier.
     *
     * @param string|int[] $listeV Liste des att_id visibles (séparés par virgule ou tableau d'IDs)
     * @param string|int[] $listeU Liste des att_id non visibles (séparés par virgule ou tableau d'IDs)
     *
     * @return void
     */
    public static function renomme_fichier(array|string $listeV, array|string $listeU): void
    {
        global $upload_table, $apli, $DOCUMENTROOT;

        $query = "SELECT att_id, att_name, att_path 
                FROM $upload_table 
                WHERE att_id in ($listeV) 
                AND visible=1";

        $result = sql_query($query);

        while ($attach = sql_fetch_assoc($result)) {
            if (!file_exists($DOCUMENTROOT . $attach['att_path'] . $attach['att_id'] . '.' . $apli . '.' . $attach['att_name'])) {
                rename($DOCUMENTROOT . $attach['att_path'] . $attach['att_id'] . '.' . $apli . '.@' . $attach['att_name'], $DOCUMENTROOT . $attach['att_path'] . $attach['att_id'] . '.' . $apli . '.' . $attach['att_name']);
            }
        }

        $query = "SELECT att_id, att_name, att_path 
                FROM $upload_table 
                WHERE att_id IN ($listeU) 
                AND visible=0";

        $result = sql_query($query);

        while ($attach = sql_fetch_assoc($result)) {
            if (!file_exists($DOCUMENTROOT . $attach['att_path'] . $attach['att_id'] . '.' . $apli . '.@' . $attach['att_name'])) {
                rename($DOCUMENTROOT . $attach['att_path'] . $attach['att_id'] . '.' . $apli . '.' . $attach['att_name'], $DOCUMENTROOT . $attach['att_path'] . $attach['att_id'] . '.' . $apli . '.@' . $attach['att_name']);
            }
        }
    }

    /**
     * Met à jour la visibilité des pièces jointes.
     *
     * @param array<int>|null $visible_att Liste des att_id à rendre visibles
     * @param array<int>|string $visible_list Liste complète des att_id concernés (tableau ou CSV)
     *
     * @return void
     */
    public static function update_visibilite(array|null $visible_att, array|string $visible_list): void
    {
        global $upload_table;

        if (is_array($visible_att)) {
            $visible = implode(',', $visible_att);

            $sql = "UPDATE $upload_table 
                    SET visible='1' 
                    WHERE att_id IN ($visible)";

            sql_query($sql);

            $visible_lst = explode(',', substr($visible_list, 0, strlen($visible_list) - 1));

            $result = array_diff($visible_lst, $visible_att);

            $unvisible = implode(",", $result);

            $sql = "UPDATE $upload_table 
                    SET visible='0' 
                    WHERE att_id IN ($unvisible)";

            sql_query($sql);
        } else {
            $visible_lst = explode(',', substr($visible_list, 0, strlen($visible_list) - 1));

            $unvisible = implode(',', $visible_lst);

            $sql = "UPDATE $upload_table 
                    SET visible='0' 
                    WHERE att_id IN ($unvisible)";

            sql_query($sql);
        }

        self::renomme_fichier($visible, $unvisible);
    }

}
