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
// $Id: mod_op_premium.php,v 1.1 2002/08/07 09:25:42 moleman Exp $

  require_once 'mod_op_overview.php';

  class newsPremium extends newsOverview
  {
    var $opName='premium';

    function newsPremium()
    {
        $this->newsOverview();
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
                    AND premium=1
                ORDER BY
                    releasedate DESC, id_artikel DESC
                LIMIT 10";
            $this->setQuery($query);
    }

  }


?>