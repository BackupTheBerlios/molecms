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
// $Id: details.php,v 1.1 2002/08/07 09:25:41 moleman Exp $

		$query="SELECT rc_jobs.*,
		            rc_jobs_category.category_".LC_LANG." as bereichsname,
		            rc_firma.name as firmaname, rc_firma.url_logo
		        FROM rc_firma LEFT JOIN rc_jobs ON(id=firma) LEFT JOIN rc_jobs_category ON (bereich=id_jobcat)

		        WHERE rc_jobs.id_job = $id";

		// Wertzuweisungen Informationsfelder
		
		$row=$db_con->getRow($query,DB_FETCHMODE_OBJECT);
		$tpl2=new HTML_Template_IT();
		$tpl2->loadTemplateFile('modules/jobs/details.tpl.html');

        $tpl2->setVariable(array(
            "LC_FIRMA"          => $lang->translate("Firma"),
            "LC_BEREICH"        => $lang->translate("Bereich"),
            "LC_BRANCHE"        => $lang->translate("Branche"),
            "LC_REGION"         => $lang->translate("Region"),
            "LC_AUFTRAGGEBER"   => $lang->translate("Der Auftraggeber"),
            "LC_AUFGABEN_ZIELE" => $lang->translate("Ihre neuen Aufgaben"),
            "LC_WIRBIETEN"      => $lang->translate("Wir bieten"),
            "LC_KONTAKT"        => $lang->translate("Kontakt")));

		$tpl2->setVariable(array(
		    "FIRMA"             => '<a href="'.url(array('module'=>'company','action'=>'details','id'=>$row->firma)).'">'.$row->firmaname.'</a>',
			"BEREICH"			=> $row->bereichsname,
			"BRANCHE"			=> $row->branche,
			"REGION"			=> $row->region,
			"STELLENNAME"		=> $row->ueberschrift,
			"AUFTRAGGEBER"		=> $row->auftraggeber,
			"AUFGABEN"			=> $row->aufgaben,
			"PROFIL"			=> $row->profil,
			"WIRBIETEN"         => $row->wirbieten,
			"KONTAKT"           => $row->kontakt));

			if($row->url_logo){
    $tpl2->setVariable("COMPANY_LOGO","<img src=\"".$cfg['path']['logo'].$row->url_logo."\">");
}
		$content['CONTENT'].=$tpl2->get();

?>