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
// $Id: mod_op_gakoverview.php,v 1.1 2002/08/07 09:25:41 moleman Exp $

    require_once 'mms_mod_operation.php';

    class newsGAKOverview extends modOperation
    {
        var $opName='gakoverview';
        var $moduleName='news';
        var $category='all';


        function newsGAKOverview ($category='all')
        {
            $this->modOperation();
             $this->setDSN('DSN');
            $this->db_con=DB::connect($this->getDSN());
            $this->category=$category;
             $query= "SELECT
                    tbl_artikel.id_artikel,
                    tbl_artikel.ueberschrift,
                    tbl_artikel.releasedate
                FROM
                     gak_news_artikel  as tbl_artikel
                WHERE
                    releasedate <= CURRENT_DATE
                    AND (exdate > CURRENT_DATE OR exdate = 0)
                    AND aktiv = 1 ";
                if($this->category!="all")
                {
                    $query.=" AND id_rubrik=$this->category ";
                }

                $query.="ORDER BY
                    releasedate DESC, id_artikel DESC";
            $this->setQuery($query);
        }




        function processTemplate()
        {
           // $this->tpl->setCurrentBlock("RUBRIK_DATUM");

            foreach ($this->data as $row)
            {
                $this->tpl->setCurrentBlock("RUBRIK_ZEILE");
                $this->tpl->setVariable("CATEGORY_COLOR",$this->color);
                $this->tpl->setVariable("RUBRIKNAME",$row['rubrikname']);
                $this->tpl->setVariable("ARTIKELURL",'http://www.gak.de/artikel.php3?id='.$row['id_artikel']);
                $this->tpl->setVariable("ARTIKELNAME",$row['ueberschrift']);
                $this->tpl->parse("RUBRIK_ZEILE");

            }

        }




    }



?>