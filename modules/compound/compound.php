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
// $Id: compound.php,v 1.1 2002/08/07 09:25:45 moleman Exp $

include_once 'mod_compound.php';
$compound=new compound();

switch($_GET['action']){
    case 'details':
        $content['CONTENT']=$compound->getDetails($id);
        break;
    case 'search':
        $_action='search';
        include "$_action.php";
        break;
    default:
        $content['CONTENT']=$compound->getOverview();
}



$tpl->setVariable("CATEGORY_COLOR",$compound->color);
$nav->setimage('/modules/compound/mischungen.gif');
$nav->setmenu($compound->getNav());

?>