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
// $Id: overview.php,v 1.1 2002/08/07 09:25:40 moleman Exp $

        $content['CONTENT'].="<h1>".$lang->translate('Stellenbörse')."</h1>";
        $query="select* from rc_jobs_category";
        $bereich=$db_con->getAll($query,DB_FETCHMODE_ASSOC);

        for ($i=0;$i<count($bereich);$i++){


                require_once 'mms_tablepager.php';

                $tabpage=new PageTable($cfg['database']['dsn'],"overview");
                $tabpage->make_url=false;
                $tabpage->str_emptyresult="";
                $tabpage->indexcol="id_job";
                $tabpage->fields=array(
                        "ueberschrift",
                        "branche",
                        "name",
                        "region"
                );
                $tabpage->map_cols=array(
                        "ueberschrift"  => "Position",
                        "branche"       => "Branche",
                        "region"        => "Region",
                        "name"          =>  $lang->translate("Firma")
                );
                $tabpage->col_view = 'ueberschrift';
                $tabpage->url_view   = url(array('module'=>'jobs','action'=>'details'));
                $tabpage->add_extra=false;


                $query="SELECT * FROM rc_jobs LEFT JOIN rc_firma ON (id=firma) WHERE aktiv = 1 AND bereich =" . $bereich[$i]['id_jobcat'];
                $tabresult=$tabpage->get($query,0,'overview');
                if($tabresult!=""){
                    $content['CONTENT'].='<h2>'.$bereich[$i]['category_'.LC_LANG].'</h2>';
                    $content['CONTENT'].=$tabresult;
                }

        }

?>