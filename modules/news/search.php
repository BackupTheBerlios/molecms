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
// $Id: search.php,v 1.1 2002/08/07 09:25:42 moleman Exp $

require_once 'HTML_OOH_Form/form.php';
define('TPL_SEARCH','modules/'.$module."/search.tpl.html");
$tpl2=new IntegratedTemplateExtension();
$tpl2->loadTemplateFile(TPL_SEARCH);

$tbl_artikel=$table["artikel"];

$frm=new form("","GET",url(array('module'=>'news','action'=>'search')),"");

$frm->addElement(array(
    'type'  => "text",
	'name'  => "wort1"));
 $frm->addElement(array(
    'type'  => "hidden",
	'name'  => "module",
    'value' => 'news'));
 $frm->addElement(array(
    'type'  => "hidden",
	'name'  => 'action',
    'value' => 'search'));
$frm->addElement(array(
	"type"	=>	"radio",
	"name"	=>	"verknuepfung",
    "elname"=>  "verknuepfung_and",
    'checked'=>true,
	"value"	=>	"AND"));
 $frm->addElement(array(
	"type"	=>	"radio",
	"name"	=>	"verknuepfung",
    "elname"=>  "verknuepfung_or",
	"value"	=>	"OR"));
$frm->addElement(array(
	"type"	=>	"submit",
	"name"	=>	"go",
	"value"	=>	"suchen"));


$tpl2->setVariable(array(
	"FORMULARSTART"	=>	$frm->getStart().$frm->getElement('module').$frm->getElement('action'),
	"SUCHFELD"		=>	$frm->getElement("wort1"),
	"SUCHE_UND"		=>	$frm->getElement("verknuepfung","verknuepfung_and")."and",
	"SUCHE_ODER"	=>	$frm->getElement("verknuepfung","verknuepfung_or")."or",
	"GO"			=>	$frm->getElement("go"),
	"FORMULARENDE"	=>	$frm->getFinish()));

if($go){

function baueSuchabfrage($suchworte="",$verknuepfung="AND"){
    global $cfg;
    $tbl_artikel=$cfg['table']['news'];

	//falls nichts eingetragen wurde
	if(empty($suchworte)){
		return false;
	}

	//HTML-Dekodierung,
	$suchworte=trim(urldecode($suchworte));
	//mehrere Leerzeichen durch eines ersetzen
	$suchworte=ereg_replace("([  ]+)"," ",$suchworte);

	//Suchworte in Array extrahieren
	//Fall 1: Nur ein Suchwort, d.h. keine Leerzeichen
	if(!ereg(" ",$suchworte)){
		$suchArray[0]="$suchworte";
	}
	else{
	//Fall 2: mehrere Suchworte
		$suchArray=explode(" ",$suchworte);
	}
	$anzahlSuchworte=count($suchArray);

	//SQL-Anweisung vorbereiten
	$abfrage= "
                SELECT
        			*,date_format($tbl_artikel.releasedate, '%d.%m.%Y') as releasedate
                FROM
        			$tbl_artikel
				WHERE ";

	if($anzahlSuchworte==1){
		$aktuellesWort=strtolower($suchArray[0]);
		$abfrage .= "((LOWER(ueberschrift) LIKE '%$aktuellesWort%') OR
					 (LOWER(abstract) LIKE '%$aktuellesWort%') OR
					 (LOWER(schlagworte) LIKE '%$aktuellesWort%') OR
					 (LOWER(text) LIKE '%$aktuellesWort%'))";
	}
	else{
		$zaehler=0;
		while(list($key,$aktuellesWort)=each($suchArray)){
			$zaehler++;
			if(!empty($aktuellesWort)){
				if($zaehler != $anzahlSuchworte){
					$abfrage .= "((LOWER(ueberschrift) LIKE '%$aktuellesWort%') OR
					 (LOWER(abstract) LIKE '%$aktuellesWort%') OR
					 (LOWER(schlagworte) LIKE '%$aktuellesWort%') OR
					 (LOWER(text) LIKE '%$aktuellesWort%'))". $verknuepfung;
				}
				else{
					$abfrage .= "((LOWER(ueberschrift) LIKE '%$aktuellesWort%') OR
					 (LOWER(abstract) LIKE '%$aktuellesWort%') OR
					 (LOWER(schlagworte) LIKE '%$aktuellesWort%') OR
					 (LOWER(text) LIKE '%$aktuellesWort%'))";
				}
			}
		}
	}

	return $abfrage;
}

global $abfrage;
global $verknuepfungsmode;
global $suchwort;
global $anzahl_datensaetze;
global $weiter_link;

$abfrage=baueSuchabfrage($wort1,$verknuepfung);

$aktuelle_abfrage=$abfrage."ORDER BY releasedate DESC";


$db_res=$db_con->query($aktuelle_abfrage);
$tpl2->setCurrentBlock("ZEILE");
$tpl2->setvariable("NUMRESULTS","Anzahl Datensätze: ".$db_res->numRows());
$tpl2->setVariable("CATEGORY_COLOR","red");
while($row = $db_res->fetchrow(DB_FETCHMODE_OBJECT)){
	$tpl2->setvariable(array(
		"UEBERSCHRIFT"=>      '<a class="red" href="'.url(array('module'=>'news','action'=>'article','id'=>$row->id_artikel)).'">'. $row->ueberschrift.'</a>',
		"ABSTRACT"              =>        ($row->abstract=="0")?"": $row->abstract."<br>",
		"TEXT"				  =>		substr($row->text,0,150)." ...<a class=\"red\" href=\"".url(array('module'=>'news','action'=>'article','id'=>$row->id_artikel))."\">". $lang->translate('more')."</a>",
		"RELEASEDATE"                  =>        $row->releasedate));
	$tpl2->parseCurrentBlock();
}


}


$content["CONTENT"]=$tpl2->get();



?>