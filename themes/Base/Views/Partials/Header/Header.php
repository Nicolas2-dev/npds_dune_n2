<table width="90%" align="center">
    <div id="container">
        <tr>
            <td colspan="3">
                <div id="header_infos">
                   <?= Component::nbOnline(); ?>
                    <?= Component::whoIm(); ?>
                </div>
                <div id="header_logo">
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <?= Component::date(); ?>
                </div>
                <div id="header_banner">
                    <?= Component::Banner(); ?>
                </div>
                <div id="header_search">
                    <?= Component::searchTopics(); ?>
                    <?= Component::search(); ?>
                </div>
                <div id="header_navbar">
                    <div id="header_menu">
                        <li><a href="index.php">[french]Accueil[/french][english]Home[/english]</a></li>
                        <li><?= Component::member(); ?></li>
                        <li><a href="submit.php">[french]Soumettre un article[/french][english]Submit a New[/english]</a></li>
                    </div>
                </div>
            </td>
        </tr>                
    </div>               
</table>