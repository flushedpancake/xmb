<form method="post" action="<?= $full_url ?>topicadmin.php?action=bump">
<input type="hidden" name="token" value="<?= $token ?>" />
<table cellspacing="0" cellpadding="0" border="0" width="<?= $THEME['tablewidth'] ?>" align="center">
<tr>
<td bgcolor="<?= $THEME['bordercolor'] ?>"><table border="0" cellspacing="<?= $THEME['borderwidth'] ?>" cellpadding="<?= $THEME['tablespace'] ?>" width="100%">
<tr>
<td class="category" colspan="2"><font color="<?= $THEME['cattext'] ?>"><strong><?= $lang['textbumpthread'] ?></strong></font></td>
</tr>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>" width="22%"><?= $lang['loggedinuser'] ?></td>
<td bgcolor="<?= $THEME['altbg2'] ?>" ><?= $hUsername ?> <?= $lang['textminilogout'] ?></td>
</tr>
<tr>
<td colspan="2" class="ctrtablerow" bgcolor="<?= $THEME['altbg2'] ?>"><input type="hidden" name="fid" value="<?= $fid ?>" /><input type="hidden" name="tid" value="<?= $tid ?>" /><input type="submit" class="submit" name="bumpsubmit" value="<?= $lang['textbumpthread'] ?>" /></td>
</tr>
</table>
</td>
</tr>
</table>
</form>
