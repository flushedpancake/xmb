<table cellspacing="0" cellpadding="0" border="0" width="<?= $THEME['tablewidth'] ?>" align="center">
<tr>
<td bgcolor="<?= $THEME['bordercolor'] ?>">
<table border="0" cellspacing="<?= $THEME['borderwidth'] ?>" cellpadding="<?= $THEME['tablespace'] ?>" width="100%">
<tr>
<td colspan="2" class="category"><font color="<?= $THEME['cattext'] ?>"><strong><?= $lang['textprofor'] ?> <?= $username ?></strong></font></td>
</tr>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>" width="22%"><?= $lang['textusername'] ?></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><?= $username ?><?= $memberlinks ?></td>
</tr>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>"><?= $lang['textregistered'] ?></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><?= $regdate ?> (<?= $ppd ?> <?= $lang['textmesperday'] ?>)</td>
</tr>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>"><?= $lang['textposts'] ?></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><?= $postnum ?> (<?= $percent ?>% <?= $lang['textoftotposts'] ?>)</td>
</tr>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>" valign="top"><?= $lang['textstatus'] ?><br /><?= $newsitelink ?></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><?= $showtitle ?><?= $customstatus ?><br /><?= $stars ?><br /><br /><?= $avatarrank ?></td>
</tr>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>" valign="top"><?= $lang['lastactive'] ?></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><?= $lastmembervisittext ?></td>
</tr>
</table>
</td>
</tr>
</table>
<br />
<table cellspacing="0" cellpadding="0" border="0" width="<?= $THEME['tablewidth'] ?>" align="center">
<tr>
<td bgcolor="<?= $THEME['bordercolor'] ?>"><table border="0" cellspacing="<?= $THEME['borderwidth'] ?>" cellpadding="<?= $THEME['tablespace'] ?>" width="100%">
<tr>
<td colspan="2" class="category"><font color="<?= $THEME['cattext'] ?>"><strong><?= $lang['memcp_otherinfo'] ?></strong></font></td>
</tr>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>" width="22%"><?= $lang['textsite'] ?></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><a href="<?= $site ?>" onclick="window.open(this.href); return false;"><?= $site ?></a></td>
</tr>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>"><?= $lang['textlocation'] ?></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><?= $location ?></td>
</tr>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>"><?= $lang['textbday'] ?></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><?= $bday ?></td>
</tr>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>" valign="top"><?= $lang['textbio'] ?></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><?= $bio ?></td>
</tr>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>" valign="top"><?= $lang['userprofilemood'] ?></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><?= $mood ?></td>
</tr>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>" valign="top"><?= $lang['textprofforumma'] ?></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><?= $topforum ?></td>
</tr>
<tr class="tablerow">
<td bgcolor="<?= $THEME['altbg1'] ?>" valign="top"><?= $lang['textproflastpost'] ?></td>
<td bgcolor="<?= $THEME['altbg2'] ?>"><?= $lastpost ?></td>
</tr>
</table>
</td>
</tr>
</table>
<br />
<table cellspacing="0" cellpadding="0" border="0" width="<?= $THEME['tablewidth'] ?>" align="center">
<tr>
<td bgcolor="<?= $THEME['bordercolor'] ?>">
<table border="0" cellspacing="<?= $THEME['borderwidth'] ?>" cellpadding="<?= $THEME['tablespace'] ?>" width="100%">
<tr>
<td colspan="2" class="category"><font color="<?= $THEME['cattext'] ?>"><strong><?= $lang['memcp_otheroptions'] ?></strong></font></td>
</tr>
<tr>
<td bgcolor="<?= $THEME['altbg1'] ?>" colspan="2" class="tablerow"><strong><?= $postSearchLink ?></strong> <?= $admin_edit ?></td>
</tr>
</table>
</td>
</tr>
</table>
