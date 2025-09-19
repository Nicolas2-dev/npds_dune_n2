<nav id="uppernavbar" class="navbar navbar-expand-md bg-primary fixed-top" data-bs-theme="dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= site_url('index'); ?>"><span data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="right" title="&lt;i class='fa fa-home fa-lg' &gt;&lt;/i&gt;">NPDS^ 16</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#barnav" aria-controls="barnav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="barnav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item dropdown" data-bs-theme="light"><a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">News</a>
                    <ul class="dropdown-menu" role="menu" aria-label="list news">
                        <li><a class="dropdown-item" href="<?= site_url('index/index'); ?>">[french]Les articles[/french][english]Stories[/english][chinese]&#x6587;&#x7AE0;[/chinese][spanish]Art&#xED;culo[/spanish][german]Artikeln[/german]</a></li>
                        <li><a class="dropdown-item" href="search.php">[french]Les archives[/french][english]Archives[/english][chinese]&#x6863;&#x6848;&#x9986;[/chinese][spanish]Los archivos[/spanish][german]Die Archive[/german]</a></li>
                        <li><a class="dropdown-item" href="submit.php">[french]Soumettre un article[/french][english]Submit a New[/english][chinese]&#x63D0;&#x8BAE;&#x51FA;&#x7248;&#x4E00;&#x4EFD;&#x51FA;&#x7248;&#x7269;[/chinese][spanish]Enviar un artículo[/spanish][german]Publikation vorschlagen[/german]</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="forum.php">[french]Forums[/french][english]Forums[/english][chinese]&#x7248;&#x9762;&#x7BA1;&#x7406;[/chinese][spanish]Foros[/spanish][german]Foren[/german]</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('download'); ?>">[french]T&eacute;l&eacute;chargements[/french][english]Downloads[/english][chinese]Downloads[/chinese][spanish]Descargas[/spanish][german]Downloads[/german]</a></li>
                <li class="nav-item"><a class="nav-link" href="modules.php?ModPath=links&amp;ModStart=links">[french]Liens[/french][english]Links[/english][chinese]&#x7F51;&#x9875;&#x94FE;&#x63A5;[/chinese][spanish]Enlaces web[/spanish][german]Internetlinks[/german]</a></li>
                <li class="nav-item dropdown" data-bs-theme="light">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-user fa-lg me-1"></i><?= Theme_NpdsBoostSK::userUsername(); ?></a>
                    <ul class="dropdown-menu">
                        <li class="text-center"><?= Theme_NpdsBoostSK::userAvatar(); ?></li>
                        <?= Theme_NpdsBoostSK::menuUser() ?>
                        <li class="dropdown-divider"></li>
                        <li><?= Theme_NpdsBoostSK::boiteConnection(); ?></li>
                    </ul>
                </li>
                <?= Theme_NpdsBoostSK::boiteMessenger(); ?>
            </ul>

            <?= Theme_NpdsBoostSK::adminLink(); ?>
        </div>
    </div>
</nav>
<div class="page-header">
    <div class="row">
        <div class="col-sm-2"><img class="img-fluid" src="<?= site_url('themes/'. $theme .'/assets/images/header.png'); ?>" loading="lazy" alt="logo" /></div>
        <div id="logo_header" class="col-sm-6">
            <h1 class="my-4">NPDS<br /><small class="text-body-secondary">Responsive</small></h1>
        </div>
        <div id="ban" class="col-sm-4 text-end"><?= Component::Banner(); ?></div>
    </div>
    <div class="row">
        <div id="slogan" class="col-sm-8 text-body-secondary slogan"><strong><?= Component::siteConfig('slogan'); ?></strong></div>
        <div id="online" class="col-sm-4 text-body-secondary text-end"><?= Component::nbOnline(); ?> <br /><?= Component::date(); ?></div>
    </div>
</div>
<script type="text/javascript">
    //<![CDATA[
    $(document).ready(function() {
        var chat_pour = ['chat_tous', 'chat_membres', 'chat_anonyme', 'chat_admin'];
        chat_pour.forEach(function(ele) {
            if ($('#' + ele + '_encours').length) {
                var clon = $('#' + ele + '_encours').clone().attr('id', ele + '_ico');
                $(".navbar-nav").append(clon);
                $('#' + ele + '_ico').wrapAll('<li class="nav-item" />');
            }
        })
    })
    //]]>
</script>