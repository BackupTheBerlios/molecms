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
// $Id: jobs.php,v 1.1 2002/08/07 09:25:41 moleman Exp $

switch($_GET['action']){
    case 'details':
        $_action='details';
        break;
    default:
        $_action='overview';
}

include "$_action.php";

//$nav->setimage('/modules/jobs/jobs.gif');
//$nav->setmenu('<a href="'.url(array('module'=>'product','action'=>'search')).'">'.$lang->translate('search').'</a><br>'.'<a href="'.url(array('module'=>'product','action'=>'browse')).'">'.$lang->translate('browse').'</a>');

?>