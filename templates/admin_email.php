<?php

declare(strict_types=1);

namespace XMB;

?>
<tr bgcolor="<?= $THEME['altbg2'] ?>">
<td align="center">
<form method="post" action="<?= $full_url ?>admin/email.php">
<input type="hidden" name="token" value="<?= $token ?>" />
<table cellspacing="0" cellpadding="0" border="0" width="<?= $THEME['tablewidth'] ?>" align="center">
<tr>
<td bgcolor="<?= $THEME['bordercolor'] ?>">
<table border="0" cellspacing="<?= $THEME['borderwidth'] ?>" cellpadding="<?= $THEME['tablespace'] ?>" width="100%">
<tr class="category">
<td colspan="2"><strong><font color="<?= $THEME['cattext'] ?>"><a name="1" />&raquo;&nbsp;<?= $lang['admin_email_settings'] ?></font></strong></td>
</tr>
<?php
$admin->printsetting2($lang['adminemail'], 'adminemailnew', $SETTINGS['adminemail'], 50);
?>
<tr class="ctrtablerow">
<td bgcolor="<?= $THEME['altbg2'] ?>" colspan="2"><input class="submit" type="submit" name="settingsubmit" value="<?= $lang['textsubmitchanges'] ?>" /></td>
</tr>
<tr class="category">
<td colspan="2"><strong><font color="<?= $THEME['cattext'] ?>"><a name="11" />&raquo;&nbsp;<?= $lang['admin_email_server'] ?></font></strong></td>
</tr>
<?php
if ($mailerInConfig) {
    $admin->printsetting5($lang['status'], $lang['mailerInConfig']);
} else {
    $admin->printsetting5($lang['mailerType'], $lang['mailerIntro']);
?>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>"><label><input type="radio" name="mailerType" value="default" <?= $mailerDefaultSel ?> /><?= $lang['mailerTypeDefault'] ?></label></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><?= $lang['mailerTypeDefaultDetail'] ?></td>
</tr>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>"><label><input type="radio" name="mailerType" value="native" <?= $mailerNativeSel ?> /><?= $lang['mailerTypeNative'] ?></label></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><?= $lang['mailerTypeNativeDetail'] ?></td>
</tr>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>"><label><input type="radio" name="mailerType" value="sendmail" <?= $mailerSendmailSel ?> /><?= $lang['mailerTypeSendmail'] ?></label></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><?= $lang['mailerTypeSendmailDetail'] ?></td>
</tr>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>"><label><input type="radio" name="mailerType" value="symfony" <?= $mailerSymfonySel ?> /><?= $lang['mailerTypeSymfony'] ?></label></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><?= $lang['mailerTypeSymfonyDetail'] ?></td>
</tr>
<?php
    $admin->printsetting2($lang['mailerHost'], 'hostnew', $SETTINGS['mailer_host'], 50);
    $admin->printsetting2($lang['mailerPort'], 'portnew', $SETTINGS['mailer_port'], 6);
    $admin->printsetting2($lang['mailerUsername'], 'usernamenew', $SETTINGS['mailer_username'], 50);
?>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>"><?= $lang['mailerPassword'] ?></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><input type="text" name="passwordnew" size="50" value="<?= $passwordAttr ?>" /></td>
</tr>
<?php
    $labels = [$lang['textoff'], $lang['automatic'], $lang['texton']];
    $values = ['off', 'auto', 'on'];
    $admin->printsetting3($lang['mailerTLS'], 'tlsnew', $labels, $values, $tlsSel, multi: false);
}
?>
<tr class="ctrtablerow">
<td bgcolor="<?= $THEME['altbg2'] ?>" colspan="2"><input class="submit" type="submit" name="settingsubmit" value="<?= $lang['textsubmitchanges'] ?>" /></td>
</tr>
<tr class="category">
<td colspan="2"><strong><font color="<?= $THEME['cattext'] ?>"><a name="1" />&raquo;&nbsp;<?= $lang['admin_email_dkim'] ?></font></strong></td>
</tr>
<?php
    $admin->printsetting5($lang['textdesc'], $lang['mailerDkimIntro']);
    $admin->printsetting2($lang['mailerDkimKey'], 'dkimkeynew', $SETTINGS['mailer_dkim_key_path'], 50);
    $admin->printsetting2($lang['mailerDkimDomain'], 'dkimdomainnew', $SETTINGS['mailer_dkim_domain'], 50);
    $admin->printsetting2($lang['mailerDkimSelector'], 'dkimselectornew', $SETTINGS['mailer_dkim_selector'], 50);
?>
<tr class="ctrtablerow">
<td bgcolor="<?= $THEME['altbg2'] ?>" colspan="2"><input class="submit" type="submit" name="settingsubmit" value="<?= $lang['textsubmitchanges'] ?>" /></td>
</tr>
</table>
</td>
</tr>
</table>
</form>
</td>
</tr>
