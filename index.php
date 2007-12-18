<?php
/**
 * eXtreme Message Board
 * XMB 1.9.9 Engage Beta 1
 *
 * Developed And Maintained By The XMB Group
 * Copyright (c) 2001-2008, The XMB Group
 * http://www.xmbforum.com
 *
 * Sponsored By iEntry, Inc.
 * Copyright (c) 2007, iEntry, Inc.
 * http://www.ientry.com
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 **/

require 'header.php';

loadtemplates(
'index',
'index_category',
'index_forum',
'index_forum_lastpost',
'index_forum_nolastpost',
'index_noforum',
'index_ticker',
'index_stats',
'index_welcome_guest',
'index_welcome_member',
'index_whosonline'
);

eval('$css = "'.template('css').'";');

$ticker = '';
if ($SETTINGS['tickerstatus'] == 'on') {
    $contents = '';
    $news = explode("\n", str_replace(array("\r\n", "\r"), array("\n"), $tickercontents));
    for($i=0;$i<count($news);$i++) {
        if (strlen(trim($news[$i])) == 0) {
            continue;
        }
        $news[$i] = postify($news[$i], 'no', 'no', 'yes', 'no', 'yes', 'yes', false, 'yes', 'no');
        $news[$i] = str_replace('\"', '"', addslashes($news[$i]));
        $contents .= "\tcontents[$i]='$news[$i]';\n";
    }
    eval('$ticker = "'.template('index_ticker').'";');
}

$gid = getInt('gid');
if ($gid) {
    $gid = (int) $gid;
    $SETTINGS['tickerstatus'] = 'off';
    $SETTINGS['whosonlinestatus'] = 'off';
    $SETTINGS['index_stats'] = 'off';
    $query = $db->query("SELECT name FROM ".X_PREFIX."forums WHERE fid='$gid' AND type='group' LIMIT 1");
    $cat = $db->fetch_array($query);
    $db->free_result($query);
    nav(html_entity_decode(stripslashes($cat['name'])));
} else {
    $gid = 0;
}

eval('echo "'.template('header').'";');

$statsbar = '';
if ($SETTINGS['index_stats'] == 'on') {
    $query = $db->query("SELECT username FROM ".X_PREFIX."members WHERE lastvisit!=0 ORDER BY regdate DESC LIMIT 1");
    $lastmember = $db->fetch_array($query);
    $db->free_result($query);

    $query = $db->query("SELECT COUNT(uid) FROM ".X_PREFIX."members UNION ALL SELECT COUNT(tid) FROM ".X_PREFIX."threads UNION ALL SELECT COUNT(pid) FROM ".X_PREFIX."posts");
    $members = $db->result($query, 0);
    if ($members == false) {
        $members = 0;
    }

    $threads = $db->result($query, 1);
    if ($threads == false) {
        $threads = 0;
    }

    $posts = $db->result($query, 2);
    if ($posts == false) {
        $posts = 0;
    }
    $db->free_result($query);

    $memhtml = '<a href="member.php?action=viewpro&amp;member='.rawurlencode($lastmember['username']).'"><strong>'.$lastmember['username'].'</strong></a>.';
    eval($lang['evalindexstats']);
    eval('$statsbar = "'.template('index_stats').'";');
}

if ($gid == 0) {
    if (X_MEMBER) {
        eval('$welcome = "'.template('index_welcome_member').'";');
    } else {
        eval('$welcome = "'.template('index_welcome_guest').'";');
    }

    $whosonline = '';
    if ($SETTINGS['whosonlinestatus'] == 'on') {
        $guestcount = $membercount = $hiddencount = 0;
        $member = array();
        $query  = $db->query("SELECT m.status, m.username, m.invisible, w.* FROM ".X_PREFIX."whosonline w LEFT JOIN ".X_PREFIX."members m ON m.username=w.username ORDER BY w.username");
        while($online = $db->fetch_array($query)) {
            switch($online['username']) {
                case 'xguest123':
                    $guestcount++;
                    break;
                default:
                    if ($online['invisible'] != 0 && X_ADMIN) {
                        $member[] = $online;
                        $hiddencount++;
                    } else if ($online['invisible'] != 0) {
                        $hiddencount++;
                    } else {
                        $member[] = $online;
                        $membercount++;
                    }
                    break;
            }
        }
        $db->free_result($query);

        $onlinetotal = $guestcount + $membercount;

        if ($membercount != 1) {
            $membern = '<strong>'.$membercount.'</strong> '.$lang['textmembers'];
        } else {
            $membern = '<strong>1</strong> '.$lang['textmem'];
        }

        if ($guestcount != 1) {
            $guestn = '<strong>'.$guestcount.'</strong> '.$lang['textguests'];
        } else {
            $guestn = '<strong>1</strong> '.$lang['textguest1'];
        }

        if ($hiddencount != 1) {
            $hiddenn = '<strong>'.$hiddencount.'</strong> '.$lang['texthmems'];
        } else {
            $hiddenn = '<strong>1</strong> '.$lang['texthmem'];
        }

        eval($lang['whosoneval']);
        $memonmsg = '<span class="smalltxt">'.$lang['whosonmsg'].'</span>';

        $memtally = array();
        $num = 1;
        $show_total = (X_ADMIN) ? ($membercount+$hiddencount) : ($membercount);

        $show_inv_key = false;
        for($mnum=0; $mnum<$show_total; $mnum++) {
            $pre = $suff = '';

            $online = $member[$mnum];

            $pre = '<span class="status_'.str_replace(' ', '_', $online['status']).'">';
            $suff = '</span>';

            if ($online['invisible'] != 0) {
                $pre .= '<strike>';
                $suff = '</strike>'.$suff;
                if (!X_ADMIN && $online['username'] != $xmbuser) {
                    $num++;
                    continue;
                }
            }

            if ($online['username'] == $xmbuser && $online['invisible'] != 0) {
                $show_inv_key = true;
            }

            $memtally[] = '<a href="member.php?action=viewpro&amp;member='.rawurlencode($online['username']).'">'.$pre.''.$online['username'].''.$suff.'</a>';
            $num++;
        }

        if (X_ADMIN || $show_inv_key === true) {
            $hidden = ' - <strike>'.$lang['texthmem'].'</strike>';
        } else {
            $hidden = '';
        }

        $memtally = implode(', ', $memtally);
        if ($memtally == '') {
            $memtally = '&nbsp;';
        }

        $datecut = $onlinetime - (3600 * 24);
        if (X_ADMIN) {
            $query = $db->query("SELECT username, status FROM ".X_PREFIX."members WHERE lastvisit >= '$datecut' ORDER BY username ASC");
        } else {
            $query = $db->query("SELECT username, status FROM ".X_PREFIX."members WHERE lastvisit >= '$datecut' AND invisible!=1 ORDER BY username ASC");
        }

        $todaymembersnum = 0;
        $todaymembers = array();
        $pre = $suff = '';
        while($memberstoday = $db->fetch_array($query)) {
            $pre = '<span class="status_'.str_replace(' ', '_', $memberstoday['status']).'">';
            $suff = '</span>';
            $todaymembers[] = '<a href="member.php?action=viewpro&amp;member='.rawurlencode($memberstoday['username']).'">'.$pre.''.$memberstoday['username'].''.$suff.'</a>';
            ++$todaymembersnum;
        }
        $todaymembers = implode(', ', $todaymembers);
        $db->free_result($query);

        if ($todaymembersnum == 1) {
            $memontoday = $todaymembersnum.$lang['textmembertoday'];
        } else {
            $memontoday = $todaymembersnum.$lang['textmemberstoday'];
        }
        eval('$whosonline = "'.template('index_whosonline').'";');
    }

    if ($gid = 0) {
        $fquery = $db->query("SELECT name as cat_name, fid as cat_fid FROM ".X_PREFIX."forums WHERE type='group' ORDER BY displayorder ASC");
    } else {
        $fquery = $db->query("SELECT f.*, c.name as cat_name, c.fid as cat_fid FROM ".X_PREFIX."forums f LEFT JOIN ".X_PREFIX."forums c ON (f.fup=c.fid) WHERE (c.type='group' AND f.type='forum' AND c.status='on' AND f.status='on') OR (f.type='forum' AND f.fup='' AND f.status='on') ORDER BY c.displayorder ASC, f.displayorder ASC");
    }
} else {
    $ticker = $welcome = $whosonline = $statsbar = '';
    $fquery = $db->query("SELECT f.*, c.name as cat_name, c.fid as cat_fid FROM ".X_PREFIX."forums f LEFT JOIN ".X_PREFIX."forums c ON (f.fup=c.fid) WHERE (c.type='group' AND f.type='forum' AND c.status='on' AND f.status='on' AND f.fup='$gid') ORDER BY c.displayorder ASC, f.displayorder ASC");
}

if ($SETTINGS['showsubforums'] == 'on') {
    $index_subforums = array();
    if ($gid == 0) {
        $query = $db->query("SELECT fid, fup, name, private, userlist FROM ".X_PREFIX."forums WHERE status='on' AND type='sub' ORDER BY fup, displayorder");
        while($queryrow = $db->fetch_array($query)) {
            $index_subforums[] = $queryrow;
        }
        $db->free_result($query);
    }
}

$lastcat = 0;
$forumlist = $cforum = '';
while($thing = $db->fetch_array($fquery)) {
    $cforum = forum($thing, 'index_forum');
    if ($lastcat != (int) $thing['cat_fid'] && !empty($cforum)) {
        $lastcat = (int) $thing['cat_fid'];
        $thing['cat_name'] = html_entity_decode($thing['cat_name']);
        eval('$forumlist .= "'.template('index_category').'";');
    }
    $forumlist .= $cforum;
}

if (empty($forumlist)) {
    eval('$forumlist = "'.template('index_noforum').'";');
}
$db->free_result($fquery);

eval('$index = "'.template('index').'";');
end_time();
eval('$footer = "'.template('footer').'";');
echo stripslashes($index.$footer);
?>