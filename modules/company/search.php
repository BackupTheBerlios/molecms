<?
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
// $Id: search.php,v 1.1 2002/08/07 09:25:43 moleman Exp $

//phpinfo();
include_once 'sqlquery.inc';
 $fields=array(
        "name",
        "ort",
        "land"
    );
    $map_cols=array(
        "name"   =>  $lang->translate('company_name'),
        "ort"       =>  $lang->translate('city'),
        "land"      =>  $lang->translate('country')
    );

if(!isset($sql)) {
        $sql = new Sql_Query;
        $sql->conditions=1;
        $sql->method='get';
        $sql->translate="on";
        $sql->container="on";
        $sql->variable="on";
        $sql->module='company';
        $sql->action='search';
        $sql->lang=LC_LANG;
}

if(isset($x)) {
        $abfrage=$sql->where("x",1);
        $from=0;
}
$tpl->setVariable("UEBERSCHRIFT",$GLOBALS['str_companysearch']);
$content["ABFRAGEBOX"] =$sql->form("x",$map_cols,"searchbox",url(array('module'=>'company','action'=>'search')));

if(!isset($order)){
    $order="name";
}

if($abfrage){
    require_once 'mms_tablepager.php';

    $tabpage=new PageTable($cfg['database']['dsn'],"overview");
    $tabpage->url_view=url(array('module'=>'company','action'=>'details'));
    $tabpage->url=url(array('module'=>'company','action'=>'search','abfrage'=>urlencode($abfrage)));
    $tabpage->fields=$fields;
    $tabpage->map_cols=$map_cols;
    $table['firma']=$cfg['table']['company'];
    $query="SELECT * FROM $table[firma] WHERE $abfrage ORDER BY $order ASC";
    $tpl->setVariable("TABELLE",$tabpage->get($query,$from,'overview'));
}


$nav->setImage('../pic/search.gif');
//$nav->setmenu(toc_sublevel(2));
$tpl->setVariable("CATEGORY_COLOR","orange");


?>