<?php
/*********************************************************************
    index.php

    Helpdesk landing page. Please customize it to fit your needs.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2010 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id: $
**********************************************************************/
require('client.inc.php');
//We are only showing landing page to users who are not logged in.
if($thisclient && is_object($thisclient) && $thisclient->isValid()) {
    require('tickets.php');
    exit;
}


require(CLIENTINC_DIR.'header.inc.php');
?>

<?require(CLIENTINC_DIR.'main.inc.php');
require(CLIENTINC_DIR.'footer.inc.php'); ?>
