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
// $Id: mms_lang_de.php,v 1.2 2002/09/03 16:07:49 moleman Exp $


    include_once 'mms_localization.php';

    class german extends localization{

        function formatMoney($sum)
        {
            $text = '&euro; ' . number_format($sum,2,',','.');

            return($text);
        }

        function formatDate($date)
        {
            list($year,$month,$day) = explode("-",$date);
            $date=$day.'.'.$month.'.'.$year;
            return $date;
        }
    }

?>