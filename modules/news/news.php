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
// $Id: news.php,v 1.1 2002/08/07 09:25:42 moleman Exp $

include_once 'mod_news.php';
$news=new news();

switch($_GET['action']){
    case 'article':
        include 'mod_op_details.php';
        $details=new newsDetails($id);
        $content['CONTENT']=$details->get();
        break;
    case 'search':
        include "search.php";
        break;
    default:
        include_once 'mod_op_premium.php';
        $premium= new newsPremium();
        $content['CONTENT'].=$premium->get();
        include_once 'mod_op_overview.php';
        $overview=new newsOverview();
        $overview->setLimit(40);
        $content['CONTENT'].=$overview->get();



}
include_once 'mod_op_gakoverview.php';

        $tpl->setVariable("LC_AKTUELLES",$lang->translate("GAK News"));

        $gak= new newsGAKOverview(1);
        $gak->setLimit(2);
        $content['RIGHTNAV']="<b>".$lang->translate("Wirtschaft")."</b><br>".$gak->get();
        $gak= new newsGAKOverview(2);
         $gak->setLimit(2);
        $content['RIGHTNAV'].="<b>".$lang->translate("Technik")."</b><br>".$gak->get();
        $gak= new newsGAKOverview(3);
         $gak->setLimit(2);
        $content['RIGHTNAV'].="<b>".$lang->translate("Personelles")."</b><br>".$gak->get();
        $gak= new newsGAKOverview(4);
         $gak->setLimit(2);
        $content['RIGHTNAV'].="<b>".$lang->translate("Veranstaltungen")."</b><br>".$gak->get();
        $gak= new newsGAKOverview(5);
         $gak->setLimit(2);
        $content['RIGHTNAV'].="<b>".$lang->translate("Buchbesprechungen")."</b><br>".$gak->get();
        $gak= new newsGAKOverview(6);
         $gak->setLimit(2);
        $content['RIGHTNAV'].="<b>".$lang->translate("Firmenschriften")."</b><br>".$gak->get();



$nav->setimage('modules/news/news.gif');
$nav->setMenu($news->getNav());

?>