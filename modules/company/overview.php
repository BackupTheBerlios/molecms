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
// $Id: overview.php,v 1.1 2002/08/07 09:25:43 moleman Exp $

include_once 'Menu_Browser_DB.php';

class HTML_menu_browser_company extends HTML_menu_browser{

    function HTML_menu_browser_company($DSN = '', $table = '', $query=""){
        $this->HTML_Menu_browser($DSN, $table, $query);
    }

    function makeURL($eintrag){
        $url=url(array('module'=>'company','action'=>'overview','id'=>$eintrag['id']));
        return $url;
    }

}



$browser = new HTML_menu_browser_company($cfg['database']['dsn']);
$browser->setQuery("SELECT *, rc_produktkat.id as id, count(rc_firma_produktkat.id_firma) as numchildren
FROM rc_produktkat LEFT JOIN rc_firma_produktkat ON (rc_firma_produktkat.id_produktkat=rc_produktkat.id)
GROUP BY rc_produktkat.id");

$m=$browser->getMenu();





$table['firma']=$cfg['table']['company'];
$table['firma_produktkat']=$cfg['table']['company_cat_product'];
 $table['cat_product']=$cfg['table']['cat_product'];
 if(!$id){
    $id=0;
 }

 $query="select name_".LC_LANG." from $table[cat_product] where id = $id";
 $str_category=$db_con->getOne($query);


    require_once 'mms_tablepager.php';

    $tabpage=new PageTable($cfg['database']['dsn'],"overview");
    $tabpage->url_view=$phpfile["firma_details"];
    $tabpage->fields=array('name');
    $tabpage->url_view   = url(array('module'=>'company','action'=>'details'));
    $tabpage->add_extra=false;
    $tabpage->col_view='name';
    $tabpage->make_url=false;
   // $tabpage->map_cols=$map_cols;
   $query="select*, $table[firma].name as name, $table[firma].land from $table[firma_produktkat],$table[firma]
             where $table[firma_produktkat].id_produktkat=$id
             and $table[firma_produktkat].id_firma = $table[firma].id order by land asc";





//print_r ($m);
$cmp_menu=new mms_template_Menu($m,'yahoo',"REQUEST_URI");
$cmp_menu->keepEmpty=false;
$cmp_menu->shownumchildren=true;

if(!$id){
    $cmp_menu->overrideURL='/index.php?module=company&action=overview&id=225';
}

$content['CONTENT'].="<h1>".$str_category."</h1>".$cmp_menu->get('urhere').'<br>'.$cmp_menu->get('yahoo').'<br><hr>'.$tabpage->get($query,$from,'overview');




?>