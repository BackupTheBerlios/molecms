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
// $Id: details.php,v 1.1 2002/08/07 09:25:47 moleman Exp $

    $table['product']=$cfg['table']['product'];
    $table['reseller']=$cfg['table']['reseller'];
    $table['company']=$cfg['table']['company'];
    $table['compound']=$cfg['table']['compound'];
    $table['compound_details']=$cfg['table']['details_compound'];

    $query="select *, c.name as company_name, p.name as product_name from $table[product] as p LEFT JOIN $table[company] as c ON (p.id_hersteller=c.id) where p.id = $id AND aktiv=".TRUE;
    $row=$db_con->getRow($query);

    $category=$row->id_produktkat;

    $link_back= '<a href="';
    $link_back.=url(array('module'=>'product','action'=>'overview','id'=>$category));
    $link_back.='">';
    $link_back.=$lang->translate("back");
    $link_back.='</a>';


    include_once 'HTML/Table.php';

    $tbl=new HTML_Table('class="overview"');
    $tbl->addRow(array($lang->translate('name'),$row->{product_name}));
    $tbl->addRow(array($lang->translate('chemical nomenclature'),$row->{info.'_'.LC_LANG}));
     $tbl->addRow(array($lang->translate('further information'),$row->{bem_.LC_LANG}));
    $tbl->addRow(array($lang->translate('manufacturer'),'<a href="'.url(array('module'=>'company','action'=>'details','id'=>$row->id)).'"><img src="'.$cfg['path']['logo'].$row->url_logo.'"><br>'.$row->{company_name}.'</a>'));
    $tbl->addRow(array($lang->translate('website'),$row->product_url));

    $tbl->setColAttributes(0,'width="100"');
    $tbl->setColAttributes(1,'width="300"');


    $row1Style = array ('class'=>'overview');
    $row2Style = array ('class'=>'overviewalternate');
    $tbl->altRowAttributes(0,$row1Style,$row2Style);


    $tpl->setVariable("UEBERSCHRIFT",$row->product_name);
    $tpl->SetVariable("TABELLE_UEBERSCHRIFT",$row->product_name . " (".$link_back.")");

/** BEGIN Vertrieb **/


    $query="select *,t_reseller.name as reseller
        from $table[product],$table[company] as t_reseller, $table[reseller]
        where $table[product].id=$id
        AND $table[product].id=$table[reseller].id_produkt
        AND $table[reseller].id_firma=t_reseller.id" ;


    require_once 'mms_tablepager.php';

    $tabpage = new PageTable($cfg['database']['dsn'],"overview");
    $tabpage->fields = array('name','land');
    $tabpage->make_url=false;
    $tabpage->add_extra=false;
    $tabpage->col_view = 'name';
    $tabpage->url_view = url(array('module'=>'company','action'=>'details'));
    $res_vertrieb='<h2>'.$lang->translate('reseller').'</h2>'.$tabpage->get($query,$from,'overview').'<br>';

/** END Vertrieb **/

/** BEGIN Beispielmischungen **/
    $query="select *,$table[compound].id as mixid, $table[compound].name_".LC_LANG." as mischname from $table[product],$table[compound_details],$table[compound] where $table[compound_details].id_produkt=$table[product].id AND $table[compound].id = $table[compound_details].id_mischung AND $table[product].id=$id";
    $tabpage = new PageTable($cfg['database']['dsn'],"overview");
    $tabpage->fields = array('mischname');
    $tabpage->map_cols=array('mischname'=>$lang->translate('compound'));
    $tabpage->url_view = url(array('module'=>'compound','action'=>'details'));
    $tabpage->setEmptyString($lang->translate('no compounds available for this product'));
    $res_compound = '<h2>'.$lang->translate('compound').'</h2>'.$tabpage->get($query,$from,'overview');
/** END Beispielmischungen  **/

    $tpl->setCurrentBlock("TABELLEBLOCK");
    $tpl->setVariable("TABELLE",$tbl->toHTML().$res_vertrieb.$res_compound);
    $tpl->parseCurrentBlock();

?>