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
// $Id: infolinks.php,v 1.1 2002/08/07 09:25:47 moleman Exp $


switch($_GET['action']){
    case 'details':
        $_action='details';
        break;
    default:
        $_action='overview';
}

include "$_action.php";
$nav->setimage('/modules/infolinks/infolinks.gif');


?>