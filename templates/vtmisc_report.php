<form method="post" name="input" action="<?= $full_url ?>vtmisc.php?action=report">
<input type="hidden" name="token" value="" />
<table cellspacing="0" cellpadding="0" border="0" width="<?= $THEME['tablewidth'] ?>" align="center">
<tr>
<td bgcolor="<?= $THEME['bordercolor'] ?>">
<table border="0" cellspacing="<?= $THEME['borderwidth'] ?>" cellpadding="<?= $THEME['tablespace'] ?>" width="100%">
<tr>
<td class="category" colspan="2"><font color="<?= $THEME['cattext'] ?>"><strong><?= $lang['textreportpost'] ?></strong></font></td>
</tr>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>" valign="top" width="19%"><?= $lang['textreason'] ?></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><textarea rows="9" cols="45" name="reason"></textarea>
</tr>
<tr>
<td colspan="2" class="ctrtablerow" bgcolor="<?= $THEME['altbg2'] ?>"><input type="hidden" name="tid" value="<?= $tid ?>" /><input type="hidden" name="fid" value="<?= $fid ?>" /><input type="hidden" name="pid" value="<?= $pid ?>" /><input type="submit" class="submit" name="reportsubmit" value="<?= $lang['textreportpost'] ?>" /></td>
</tr>
</table>
</td>
</tr>
</table>
</form>
