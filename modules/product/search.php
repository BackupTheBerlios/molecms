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
// $Id: search.php,v 1.1 2002/08/07 09:25:47 moleman Exp $

//phpinfo();
include_once 'sqlquery.inc';


$fields=array(
    "produktname",
    "herstellername",
    "name_".LC_LANG
    );
$map_cols=array(
        "produktname" => $lang->translate('productname'),
        "herstellername" => $lang->translate('manufacturer'),
        "name_".LC_LANG   => $lang->translate('category')
    );

$searchable_fields=array(
        $cfg['table']['product'].".name" => $lang->translate('productname'),
        "t_manufacturer.name"  =>  $lang->translate('manufacturer'),
        "name_".LC_LANG   =>  $lang->translate('category')
    );

if(!isset($sql)) {
        $sql = new Sql_Query;
        $sql->conditions=1;
        $sql->method='get';
        $sql->translate="on";
        $sql->container="on";
        $sql->variable="on";
        $sql->module='product';
        $sql->action='search';
        $sql->lang=LC_LANG;
}

if(isset($x)) {
        $abfrage=$sql->where("x",1);
        $from=0;
}
$tpl->setVariable("UEBERSCHRIFT",$lang->translate('productsearch'));
$content["ABFRAGEBOX"] =$sql->form("x",$searchable_fields,"searchbox",url(array('module'=>'product','action'=>'search')));

if(!isset($order)){
    $order="name";
}

if($abfrage){
    require_once 'mms_tablepager.php';

    $table['firma']=$cfg['table']['company'];
    $table['produkt']=$cfg['table']['product'];
    $table['produktkat']=$cfg['table']['cat_product'];

    $tabpage=new PageTable($cfg['database']['dsn'],"overview");
    $tabpage->url_view=url(array('module'=>'product','action'=>'details'));
   // echo $abfrage;
   // $abfrage=urldecode($abfrage);
    $tabpage->url=url(array('module'=>'product','action'=>'search','abfrage'=>urlencode($abfrage)));
    $tabpage->fields=$fields;
    $tabpage->map_cols=$map_cols;
    $tabpage->indexcol='produktid';

    $query="SELECT *,$table[produkt].id as produktid, $table[produkt].name as produktname,  t_manufacturer.name as herstellername
                FROM
                        $table[produkt], $table[produktkat],$table[firma] as t_manufacturer
                WHERE
                        $table[produkt].id_produktkat=$table[produktkat].id
                        AND $table[produkt].id_hersteller=t_manufacturer.id

                         AND ".stripslashes($abfrage);

    $tpl->setVariable("TABELLE",$tabpage->get($query,$from,'overview'));
}



?>