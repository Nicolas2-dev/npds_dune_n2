<?php

use App\Library\Theme\Theme;

if (! function_exists('headlines'))
{ 
    #autodoc headlines() : Bloc HeadLines <br />=> syntaxe :<br />function#headlines<br />params#ID_du_canal
    function headlines($hid = '', $block = true)
    {
        global $Version_Num, $Version_Id, $rss_host_verif, $long_chain;

        if (file_exists('config/proxy.conf.php')) {
            include 'config/proxy.conf.php';
        }

        if ($hid == '') {
            $result = sql_query("SELECT sitename, url, headlinesurl, hid 
                                FROM " . sql_prefix('headlines') . " 
                                WHERE status=1");
        } else {
            $result = sql_query("SELECT sitename, url, headlinesurl, hid 
                                FROM " . sql_prefix('headlines') . " 
                                WHERE hid='$hid' 
                                AND status=1");
        }

        while (list($sitename, $url, $headlinesurl, $hid) = sql_fetch_row($result)) {
            $boxtitle = $sitename;

            $cache_file = 'storage/cache/' . preg_replace('[^a-z0-9]', '', strtolower($sitename)) . '_' . $hid . '.cache';
            $cache_time = 1200; //3600 origine

            $items = 0;
            $max_items = 6;
            $rss_timeout = 15;

            $rss_font = '<span class="small">';

            if ((!(file_exists($cache_file))) or (filemtime($cache_file) < (time() - $cache_time)) or (!(filesize($cache_file)))) {
                $rss = parse_url($url);

                if ($rss_host_verif == true) {
                    $verif = fsockopen($rss['host'], 80, $errno, $errstr, $rss_timeout);

                    if ($verif) {
                        fclose($verif);
                        $verif = true;
                    }
                } else {
                    $verif = true;
                }

                if (!$verif) {
                    $cache_file_sec = $cache_file . '.security';

                    if (file_exists($cache_file)) {
                        $ibid = rename($cache_file, $cache_file_sec);
                    }

                    Theme::themeSidebox($boxtitle, 'Security Error');

                    return;
                } else {
                    if (!$long_chain) {
                        $long_chain = 15;
                    }

                    $fpwrite = fopen($cache_file, 'w');

                    if ($fpwrite) {
                        fputs($fpwrite, "<ul>\n");

                        $flux = simplexml_load_file($headlinesurl, 'SimpleXMLElement', LIBXML_NOCDATA);

                        //$namespaces = $flux->getNamespaces(true); // get namespaces
                        //$ic = '';

                        //ATOM//
                        if ($flux->entry) {
                            $j = 0;
                            $cont = '';

                            foreach ($flux->entry as $entry) {
                                if ($entry->content) {
                                    $cont = (string) $entry->content;
                                }

                                fputs($fpwrite, '<li><a href="' . (string)$entry->link['href'] . '" target="_blank" >' . (string) $entry->title . '</a><br />' . $cont . '</li>');

                                if ($j == $max_items) {
                                    break;
                                }

                                $j++;
                            }
                        }

                        if ($flux->{'item'}) {
                            $j = 0;
                            $cont = '';

                            foreach ($flux->item as $item) {
                                if ($item->description) {
                                    $cont = (string) $item->description;
                                }

                                fputs($fpwrite, '<li><a href="' . (string)$item->link['href'] . '"  target="_blank" >' . (string) $item->title . '</a><br /></li>');

                                if ($j == $max_items) {
                                    break;
                                }

                                $j++;
                            }
                        }

                        //RSS
                        if ($flux->{'channel'}) {
                            $j = 0;
                            $cont = '';

                            foreach ($flux->channel->item as $item) {
                                if ($item->description) {
                                    $cont = (string) $item->description;
                                }

                                fputs($fpwrite, '<li><a href="' . (string)$item->link . '"  target="_blank" >' . (string) $item->title . '</a><br />' . $cont . '</li>');

                                if ($j == $max_items) {
                                    break;
                                }

                                $j++;
                            }
                        }

                        $j = 0;

                        if ($flux->image) {
                            $ico = '<img class="img-fluid" src="' . $flux->image->url . '" />&nbsp;';
                        }

                        foreach ($flux->item as $item) {
                            fputs($fpwrite, '<li>' . $ico . '<a href="' . (string) $item->link . '" target="_blank" >' . (string) $item->title . '</a></li>');

                            if ($j == $max_items) {
                                break;
                            }

                            $j++;
                        }

                        fputs($fpwrite, "\n" . '</ul>');
                        fclose($fpwrite);
                    }
                }
            }

            if (file_exists($cache_file)) {
                ob_start();
                $ibid = readfile($cache_file); // $ibid ???
                $boxstuff = $rss_font . ob_get_contents() . '</span>';
                ob_end_clean();
            }

            $boxstuff .= '<div class="text-end"><a href="' . $url . '" target="_blank">' . translate('Lire la suite...') . '</a></div>';

            if ($block) {
                Theme::themeSidebox($boxtitle, $boxstuff);
                $boxstuff = '';
            } else {
                return $boxstuff;
            }
        }
    }
}