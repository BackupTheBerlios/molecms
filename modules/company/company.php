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
// $Id: company.php,v 1.1 2002/08/07 09:25:43 moleman Exp $

switch($_GET['action']){
    case 'details':
        $_action='details';
        break;
    case 'search':
        $_action='search';
        break;
    default:
        $_action='overview';
}

include "$_action.php";

$nav->setimage('/modules/company/firmen.gif');
$nav->setmenu('<a class="leftnav" href="'.url(array('module'=>'company','action'=>'search')).'"><img src="/pic/pfeilchen_orange.gif" border="0" alt="">&nbsp;'.$lang->translate('search').'</a><br>'.'<a class="leftnav" href="'.url(array('module'=>'company','action'=>'browse')).'"><img src="/pic/pfeilchen_orange.gif" border="0" alt="">&nbsp;'.$lang->translate('browse').'</a>');

?>