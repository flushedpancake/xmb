<?php
/**
 * XMB 1.9.8 Engage Final
 *
 * Developed By The XMB Group
 * Copyright (c) 2001-2007, The XMB Group
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 **/

if (!defined('IN_CODE')) {
    exit("Not allowed to run this file directly.");
}

class mod {
    function mod() {
        global $self, $xmbuser, $xmbpw, $lang, $action, $oToken;

        if (!X_STAFF && $action != 'votepoll' && $action != 'report') {
            extract($GLOBALS);
            error($lang['notpermitted'], false);
        }
    }

    function statuscheck($fid) {
        global $self, $xmbuser, $lang, $table_forums, $db, $oToken;

        $query = $db->query("SELECT moderator FROM $table_forums WHERE fid='$fid'");
        $mods = $db->result($query, 0);
        $status1 = modcheck($self['status'], $xmbuser, $mods);

        if (X_SMOD || X_ADMIN) {
            $status1 = 'Moderator';
        }

        if ($status1 != 'Moderator') {
            extract($GLOBALS);
            error($lang['textnoaction'], false);
        }
    }

    function log($user='', $action, $fid, $tid, $reason='') {
        global $xmbuser, $db, $table_logs, $oToken;

        if ($user == '') {
            $user = $xmbuser;
        }

        $db->query("REPLACE $table_logs (tid, username, action, fid, date) VALUES ('$tid', '$user', '$action', '$fid', ".$db->time().")");
        return true;
    }

    function create_tid_string($tids=0) {
        if (!is_array($tids)) {
            $tidstr = (int)$tids;
        } else {
            $tidstr = '';
            foreach ($tids as $value) {
                $value = (int) $value;
                if ($value > 0) {
                    $tidstr .= (empty($tidstr)) ? $value : ','.$value;
                }
            }
        }
        return $tidstr;
    }

    function create_tid_array($tids) {
        $tidArr = array();
        $tidP = explode(',', $tids);
        foreach ($tidP AS $flip) {
            $flip = (int) $flip;
            if ($flip > 0) {
                $tidArr[] = $flip;
            }
        }
        return $tidArr;
    }
}
?>