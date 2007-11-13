/**
 * eXtreme Message Board
 * XMB 1.9.8 Engage Final
 *
 * Developed And Maintained By The XMB Group
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 **/
var aBookOpen = false;
var aBookLink = '';
self.name = 'u2u';

function setCheckboxes(the_form, do_check) {
    if (document.forms[the_form].elements['u2u_select[]']) {
        var elts = document.forms[the_form].elements['u2u_select[]'];
        var elts_cnt = elts.length;

        if (elts_cnt) {
            for (var i = 0; i < elts_cnt; i++) {
                elts[i].checked = do_check;
            }
        } else {
            elts.checked = do_check;
        }
    }
    return true;
}

function invertSelection(the_form, element_name) {
    if (document.forms[the_form].elements[element_name]) {
        var elements = document.forms[the_form].elements[element_name];
        var count = elements.length;

        if (count) {
            for (var i = 0; i < count; i++) {
                if (elements[i].checked == true) {
                    elements[i].checked = false;
                } else {
                    elements[i].checked = true;
                }
            }
        } else {
            if (elements.checked == true) {
                elements.checked = false;
            } else {
                elements.checked = true;
            }
        }
    }
    return true;
}

function aBook() {
    if(aBookOpen == true) {
        aBookLink.close();
        aBookOpen = false;
    } else {
        if (typeof sendMode === "undefined" || sendMode != true) {
            aBookLink = window.open('buddy.php', 'aBook', "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=450,height=400");
        } else {
            aBookLink = window.open('buddy.php?action=add2u2u', 'aBook', "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=450,height=400");
        }
        aBookOpen = true;
    }
    return false;
}