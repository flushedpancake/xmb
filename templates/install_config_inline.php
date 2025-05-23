<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?= $lang['install_page'] ?></title>
    <meta http-equiv="content-type" content="text/html;charset=ISO-8859-1" />
    <link rel="stylesheet" href="../images/install/install.css" type="text/css" media="screen"/>
</head>
<body>
<div id="main">
    <div id="header">
        <img src="../images/install/logo.png" alt="XMB" title="XMB" />
    </div>
    <div id="configure">
        <div class="top"><span></span></div>
        <div class="center-content">
            <h1>XMB <?= $lang['config_page'] ?></h1>
            <p><?= $lang['config_inline'] ?></p>
        </div>
        <div class="bottom"><span></span></div>
    </div>
    <div id="config">
        <div class="top"><span></span></div>
        <div class="center-content">
            <textarea readonly="readonly" style="width: 90%;" rows="100"><?= $configuration ?></textarea>
            <form action="?step=5" method="post">
                <p class="button"><input type="submit" value="<?= $lang['close_window'] ?>" onclick="window.close()"></p>
            </form>
        </div>
        <div class="bottom"><span></span></div>
