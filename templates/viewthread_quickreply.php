<br />
<a name="qreply"></a>
<form method="post" name="input" action="post.php?action=reply&amp;tid=<?= $tid ?>" onsubmit="return disableButton(this);">
 <input type="hidden" name="token" value="" />
 <table width="<?= $THEME['tablewidth'] ?>" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="<?= $THEME['bordercolor'] ?>">
  <tr>
   <td><input type="hidden" name="subject" value="" />
    <table width="100%" border="0" cellpadding="<?= $THEME['tablespace'] ?>" cellspacing="<?= $THEME['borderwidth'] ?>">
     <tr bgcolor="<?= $THEME['altbg1'] ?>">
      <td colspan="3" class="category"><div align="left"><font color="<?= $THEME['cattext'] ?>"><strong>&nbsp;&raquo;&nbsp;<?= $lang['quickreply'] ?></strong><?= $quick_name_display ?></font></div></td>
     </tr>
     <?= $captchapostcheck ?>
     <tr class="quickreply">
      <td width="8%" height="101" bgcolor="<?= $THEME['altbg1'] ?>" class="tablerow">
       <div align="left">
        <span class="smalltxt"><?= $lang['texthtmlis'] ?> <?= $allowhtml ?><br />
         <?= $lang['textsmiliesare'] ?> <?= $allowsmilies ?><br />
         <?= str_replace('$url', $full_url . 'faq.php?page=messages#7', $lang['textbbcodeis']) ?> <?= $allowbbcode ?><br />
         <?= $lang['textimgcodeis'] ?> <?= $allowimgcode ?>
        </span>
       </div>
       <?= $smilies ?>
      </td>
      <td bgcolor="<?= $THEME['altbg2'] ?>" class="tablerow">
       <div class="inputWrap">
        <textarea rows="10" name="message" id="message" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"></textarea>
        <?= $quickbbcode ?>
        <div class="controls">
         <div class="postOptions">
          <label><input type="checkbox" name="smileyoff" value="yes" /> <?= $lang['textdissmileys'] ?></label>
          <label <?= $disableguest ?>><input type="checkbox" name="usesig" value="yes" <?= $usesigcheck ?> /> <?= $lang['textusesig'] ?></label>
          <label><input type="checkbox" name="bbcodeoff" value="yes" /> <?= $lang['bbcodeoff'] ?></label>
          <label <?= $disableguest ?>><input type="checkbox" name="emailnotify" value="yes" <?= $subcheck ?> /> <?= $lang['textemailnotify'] ?></label>
         </div>
         <br />&nbsp;&nbsp;<input type="submit" name="replysubmit" value="<?= $lang['textpostreply'] ?>" class="submit" />
         <br /><br />&nbsp;&nbsp;<input type="submit" name="previewpost" value="<?= $lang['textpreview'] ?>" class="submit" />
        </div>
       </div>
      </td>
     </tr>
    </table>
   </td>
  </tr>
 </table>
</form>
