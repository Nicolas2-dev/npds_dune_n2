<?php

if (! function_exists('pollMain')) {
    #autodoc pollMain($pollID,$pollClose) : Construit le bloc sondage
    function pollMain($pollID, $pollClose)
    {
        global $boxTitle, $boxContent, $pollcomm;

        if (!isset($pollID)) {
            $pollID = 1;
        }

        if (!isset($url)) {
            $url = sprintf('pollBooth.php?op=results&amp;pollID=%d', $pollID);
        }

        $boxContent = '<form action="pollBooth.php" method="post">
            <input type="hidden" name="pollID" value="' . $pollID . '">
            <input type="hidden" name="forwarder" value="' . $url . '">';

        $result = sql_query("SELECT pollTitle, voters 
                            FROM " . sql_prefix('poll_desc') . " 
                            WHERE pollID='$pollID'");

        list($pollTitle, $voters) = sql_fetch_row($result);

        global $block_title;
        $boxTitle = $block_title == '' ? translate('Sondage') :  $block_title;

        $boxContent .= '<legend>' . affLangue($pollTitle) . '</legend>';

        $result = sql_query("SELECT pollID, optionText, optionCount, voteID 
                            FROM " . sql_prefix('poll_data') . " 
                            WHERE (pollID='$pollID' 
                            AND optionText<>'') 
                            ORDER BY voteID");

        $sum = 0;
        $j = 0;

        if (!$pollClose) {
            $boxContent .= '<div class="mb-3">';

            while ($object = sql_fetch_assoc($result)) {
                $boxContent .= '<div class="form-check">
                    <input class="form-check-input" type="radio" id="voteID' . $j . '" name="voteID" value="' . $object['voteID'] . '" />
                    <label class="form-check-label d-block" for="voteID' . $j . '" >' . affLangue($object['optionText']) . '</label>
                </div>';

                $sum = $sum + $object['optionCount'];
                $j++;
            }

            $boxContent .= '</div>';
        } else {
            while ($object = sql_fetch_assoc($result)) {
                $boxContent .= '&nbsp;' . affLangue($object['optionText']) . '<br />';
                $sum = $sum + $object['optionCount'];
            }
        }

        settype($inputvote, 'string');

        if (!$pollClose) {
            $inputvote = '<button class="btn btn-outline-primary btn-sm btn-block" type="submit" value="' . translate('Voter') . '" title="' . translate('Voter') . '" ><i class="fa fa-check fa-lg"></i> ' . translate('Voter') . '</button>';
        }

        $boxContent .= '<div class="mb-3">' . $inputvote . '</div>
            </form>
            <a href="pollBooth.php?op=results&amp;pollID=' . $pollID . '" title="' . translate('Résultats') . '">
                ' . translate('Résultats') . '
            </a>&nbsp;&nbsp;
            <a href="pollBooth.php">
                ' . translate('Anciens sondages') . '
            </a>
            <ul class="list-group mt-3">
                <li class="list-group-item">' . translate('Votes : ') . ' <span class="badge rounded-pill bg-secondary float-end">' . $sum . '</span></li>';

        if ($pollcomm) {
            if (file_exists($path = 'modules/comments/config/pollBoth.php')) {
                include $path;
            }

            list($numcom) = sql_fetch_row(sql_query("SELECT COUNT(*) 
                                                    FROM " . sql_prefix('posts') . " 
                                                    WHERE forum_id='$forum' 
                                                    AND topic_id='$pollID' 
                                                    AND post_aff='1'"));

            $boxContent .= '<li class="list-group-item">' . translate('Commentaire(s) : ') . ' <span class="badge rounded-pill bg-secondary float-end">' . $numcom . '</span></li>';
        }

        $boxContent .= '</ul>';

        themesidebox($boxTitle, $boxContent);
    }
}

if (! function_exists('PollNewest')) {
    #autodoc PollNewest() : Bloc Sondage <br />=> syntaxe : <br />function#pollnewest<br />params#ID_du_sondage OU vide (dernier sondage créé)
    function PollNewest(?int $id = null): void
    {
        // snipe : multi-poll evolution
        if ($id != 0) {
            settype($id, 'integer');

            list($ibid, $pollClose) = pollSecur($id);

            if ($ibid) {
                pollMain($ibid, $pollClose);
            }
        } elseif ($result = sql_query("SELECT pollID 
                                    FROM " . sql_prefix('poll_data') . " 
                                    ORDER BY pollID DESC 
                                    LIMIT 1")) {

            list($pollID) = sql_fetch_row($result);

            list($ibid, $pollClose) = pollSecur($pollID);

            if ($ibid) {
                pollMain($ibid, $pollClose);
            }
        }
    }
}
