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
// $Id: mod_op_overview.php,v 1.1 2002/08/07 09:25:41 moleman Exp $

    require_once 'mms_mod_operation.php';

    class newsOverview extends modOperation
    {
        var $opName='overview';
        var $moduleName='news';


        function newsOverview ()
        {
            $this->modOperation();
             $query= "SELECT
                    tbl_artikel.id_artikel,
                    tbl_artikel.ueberschrift,
                    tbl_artikel.id_lang,
                    tbl_company.name,
                    tbl_artikel.releasedate
                FROM
                    " . $this->cfg['table']['news'] ." as tbl_artikel,".$this->cfg['table']['company'] ." as tbl_company
                WHERE
                    releasedate <= CURRENT_DATE
                    AND (exdate > CURRENT_DATE OR exdate = 0)
                    AND aktiv = 1
                    AND tbl_company.id = tbl_artikel.id_autor
                ORDER BY
                    releasedate DESC, id_artikel DESC ";
            $this->setQuery($query);
        }




        function processTemplate()
        {
            $this->tpl->setCurrentBlock("RUBRIK_DATUM");

            foreach ($this->data as $row)
            {
                if ($prevdate != $row['releasedate'])
                {
                    $this->tpl->parse("RUBRIK_DATUM");
                    $prevdate=$row['releasedate'];
                }
                $this->tpl->setVariable("DATUM",$this->lang->formatDate($row['releasedate']));
                $this->tpl->setVariable("CATEGORY_COLOR",$this->color);
                
                $this->tpl->setCurrentBlock("RUBRIK_ZEILE");
                    $this->tpl->setVariable("CATEGORY_COLOR",$this->color);
                    $this->tpl->setVariable("RUBRIKNAME",$row['rubrikname']);
                    $this->tpl->setVariable("ABSTRACT",$row['abstract']);
                    $this->tpl->setVariable("ARTIKELURL",url(array('module'=>'news','action'=>'article','id'=>$row['id_artikel'])));
                    $this->tpl->setVariable("ARTIKELNAME",$row['id_lang']==ENGLISH?$row['name'].":<br>".'<img src="pic/lang_en.gif" border="0">&nbsp;'.$row['ueberschrift']:$row['name'].":<br>".$row['ueberschrift']);
                $this->tpl->parse("RUBRIK_ZEILE");

            }

            $this->tpl->parse("RUBRIK_DATUM");
        }




    }



?>