<h1><?= $lang['version_check_head'] ?></h1>
<p><?= $lang['version_check_text'] ?></p>
<ul>
    <li><a href="https://www.xmbforum2.com/"><?= $lang['version_check_latest'] ?></a>: <img src="https://www.xmbforum2.com/phpbin/xmbvc/vc.php?bg=f0f0f0&amp;fg=000000" alt="" style="position: relative; top: 8px;" /></li>
    <li><?= $lang['version_check_current'] ?>: <?= $versiongeneral ?></li>
</ul>
<form action="?step=3" method="post">
    <p class="button"><input type="submit" value="<?= $lang['install_step'] ?> XMB <?= $versionshort ?> &gt;" /></p>
</form>
