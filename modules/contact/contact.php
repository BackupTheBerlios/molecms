<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// +----------------------------------------------------------------------+
// | MoleCMS                                                              |
// +----------------------------------------------------------------------+
// | Copyright (c) 2000-2002 MoleManMedia Tim Franz                       |
// +----------------------------------------------------------------------+
// | Authors: Tim Franz <tfranz@moleman.de>                               |
// +----------------------------------------------------------------------+
//
// $Id: contact.php,v 1.1 2002/08/07 09:25:44 moleman Exp $


switch($_GET['action']){
    case 'contact':
        include "contactform.php";
        break;
    default:
        include "impressum.php";
}

$nav->setimage('/modules/contact/contakt.gif');
$nav->setMenu('<a class="leftnav" href="'.url(array('module'=>'contact','action'=>'impressum')).'"><img src="/pic/pfeilchen_orange.gif" border="0" alt="">&nbsp;Impressum</a><br>');//.'<a href="'.url(array('module'=>'contact','action'=>'contact')).'">Feedback</a>');
$tpl->setVariable("CATEGORY_COLOR","green");

?>