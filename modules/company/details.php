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
// $Id: details.php,v 1.1 2002/08/07 09:25:43 moleman Exp $

$tabelle=$cfg['table']['company'];

$query="select * from $tabelle where id = $id";
$row=$db_con->getRow($query);


include_once 'HTML/Table.php';

$tbl=new HTML_Table('class="overview"');
$tbl->addRow(array($lang->translate('company_name'),$row->name));
$tbl->addRow(array($lang->translate('street'),$row->strasse));
$tbl->addRow(array($lang->translate('street_postnr'),$row->strasse_plz));
$tbl->addRow(array($lang->translate('pobox'),$row->postfach));
$tbl->addRow(array($lang->translate('pobox_postnr'),$row->postfach_plz));
$tbl->addRow(array($lang->translate('phone'),$row->tel));
$tbl->addRow(array($lang->translate('fax'),$row->fax));
$tbl->addRow(array($lang->translate('city'),$row->ort));
$tbl->addRow(array($lang->translate('country'),$row->land));
$tbl->addRow(array($lang->translate('Email'),make_link($row->email)));
$tbl->addRow(array($lang->translate('Website'),make_link($row->website)));

$tbl->setColAttributes(0,'width="100"');
$tbl->setColAttributes(1,'width="300"');


$row1Style = array ('class'=>'overview');
$row2Style = array ('class'=>'overviewalternate');
$tbl->altRowAttributes(0,$row1Style,$row2Style);

if(strlen($row->{"profile_".LC_LANG})>0)
{
    "<h2>".$lang->translate('profile')."</h2>".$row->{"profile_".LC_LANG} ;
}else{
    $profile="";
}


$tpl->setVariable("UEBERSCHRIFT",$row->name);
if($row->url_logo){
    $tpl->setVariable("COMPANY_LOGO","<img src=\"".$cfg['path']['logo'].$row->url_logo."\">");
}
$tpl->setCurrentBlock("TABELLEBLOCK");
$tpl->SetVariable("TABELLE_UEBERSCHRIFT",$lang->translate('company'));
$tpl->setVariable("TABELLE",$tbl->toHTML().$profile);
$tpl->parseCurrentBlock();

?>