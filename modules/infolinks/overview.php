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
// $Id: overview.php,v 1.1 2002/08/07 09:25:47 moleman Exp $

include_once 'Menu_Browser_DB.php';

class HTML_menu_browser_company extends HTML_menu_browser{

    function HTML_menu_browser_company($DSN = '', $table = '', $query=""){
        $this->HTML_Menu_browser($DSN, $table, $query);
    }

    function makeURL($eintrag){
        $url=url(array('module'=>'infolinks','action'=>'overview','id'=>$eintrag['id']));
        return $url;
    }

}



$browser = new HTML_menu_browser_company($cfg['database']['dsn']);
$browser->setQuery("SELECT *, rc_infolinkkat.id as id, count(rc_firma_produktkat.id_firma) as numchildren
FROM rc_infolinkkat LEFT JOIN rc_firma_produktkat ON (rc_firma_produktkat.id_produktkat=rc_infolinkkat.id)
GROUP BY rc_infolinkkat.id");


$m=$browser->getMenu();



$table['infolinks']=$cfg['table']['infolinks'];
$table['firma']=$cfg['table']['company'];
$table['firma_produktkat']=$cfg['table']['company_cat_product'];
 if(!$id){
    $id=0;
 }

    require_once 'mms_tablepager.php';

    $tabpage=new PageTable($cfg['database']['dsn'],"overview");
    $tabpage->fields=array('name');
    $tabpage->url_view   = url(array('module'=>'infolinks','action'=>'details'));

    $query="select*, $table[firma].name as name from $table[firma_produktkat],$table[firma]
             where $table[firma_produktkat].id_produktkat=$id
             and $table[firma_produktkat].id_firma = $table[firma].id order by land asc" ;


$cmp_menu=new mms_template_Menu($m,'tree',"REQUEST_URI");
$cmp_menu->keepEmpty=false;
$cmp_menu->shownumchildren=true;

$content['CONTENT'].='<h1>'.$cmp_menu->get('urhere').'</h1>'.'<br>'.$cmp_menu->get('tree').'<br>'.$tabpage->get($query,$from,'overview');

//$nav->setMenu($cmp_menu->get('tree'));


?>