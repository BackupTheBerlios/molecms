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
// $Id: search.php,v 1.1 2002/08/07 09:25:45 moleman Exp $

$tpl->setVariable('CATEGORY_LOGO',"<img src=\"/pic/search.gif\" width=\"48\" height=\"48\" border=\"0\">");

$tbl = new template_Table;
$tbl->heading=true;
$tbl->add_extra=true;
$tbl->indexcol=0;
$tbl->lang=LC_LANG;
$tbl->alternate_background=true;
$tbl->url_view=$sess->url("details.php");

$tbl->fields=array(
    "m100",
    "m300",
    "ts",
    "eab",
    "rebound",
    "shore_a",
    "sg"
    );
/* $tbl->map_cols=array(
        "produktname" => $GLOBALS['str_productname'],
       # "vertriebname" => $GLOBALS['str_reseller'],
        "herstellername" => $GLOBALS['str_manufacturer'],
        "name_".LC_LANG   => $GLOBALS['str_category'],
    );

$searchable_fields=array(
        $table['produkt'].".name" => $GLOBALS['str_productname'],
        "t_manufacturer.name"  => $GLOBALS['str_manufacturer'],
       # "t_reseller.name"   => $GLOBALS['str_reseller'],
        "name_".LC_LANG   => $GLOBALS['str_category'],
    );
         */

 $searchable_fields=array(
    "m100"=>"M100",
    "m300"=>"M300",
    "ts"=>"TS",
    "eab"=>"EAB",
    "rebound"=>"Rebound",
    "shore_a"=>"Shore A",
    "sg"=>"SG"
    );


if(!isset($sql)) {
	$sql = new Sql_Query;
	$sql->conditions=1;
	$sql->translate="on";
	$sql->container="on";
	$sql->variable="on";
	$sql->lang=LC_LANG;
	$sess->register("sql");
}

if(isset($x)) {
	$abfrage=$sql->where("x",1);
}

$tpl->setvariable("UEBERSCHRIFT",$GLOBALS['str_productsearch']);
$tpl->setvariable("ABFRAGEBOX",$sql->form("x",$searchable_fields,"searchbox"));

if($abfrage){
	/*$db->query("
                SELECT *,$table[produkt].name as produktname, t_reseller.name as vertriebname, t_manufacturer.name as herstellername
                FROM
                        $table[produkt], $table[produktkat],$table[firma] as t_manufacturer,$table[firma] as t_reseller,$table[reseller]
                WHERE
                        $table[produkt].id_produktkat=$table[produktkat].old_id
                        AND $table[produkt].id_hersteller=t_manufacturer.id
                        AND $table[reseller].id_produkt=$table[produkt].id
                        AND $table[reseller].id_firma=t_reseller.id
                         AND $abfrage");   */
                  $db->query("
                SELECT *,$table[produkt].name as produktname,  t_manufacturer.name as herstellername
                FROM
                        $table[produkt], $table[produktkat],$table[firma] as t_manufacturer
                WHERE
                        $table[produkt].id_produktkat=$table[produktkat].old_id
                        AND $table[produkt].id_hersteller=t_manufacturer.id

                         AND $abfrage");



	$tpl->setvariable(array(
	# "ABFRAGEBEDINGUNG"	=> sprintf("Abfragebedingung = %s<br>\n",$abfrage),
	"NUMRESULTS"	=> o_0or1($db->num_rows(),$GLOBALS['str_noresult'],"%s ".$GLOBALS['str_searchhits']),
	"TABELLE"		=> $tbl->get_result($db,"overview")));
}

$tpl->setVariable("CATEGORY_LOGO","<img src=\"/pic/product.jpg\" border=\"0\">");
$tpl->setVariable("CATEGORY_NAME",$GLOBALS['str_compoundsearch']);
$tpl->setVariable("CATEGORY_COLOR","maroon");
$tpl->setVariable("CATEGORY_SUBCATEGORIES",'');