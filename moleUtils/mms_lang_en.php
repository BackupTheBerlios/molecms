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
// $Id: mms_lang_en.php,v 1.2 2002/09/03 16:07:49 moleman Exp $

    include_once 'mms_localization.php';

    class english extends localization{

        function formatMoney($sum)
        {
            $text = "";

            //negative signs precede dollar signs
            if($sum < 0){
                $text .= "-";
                $sum = abs($sum);
            }

            $text .= '$' . number_format($sum,2,'.',',');

            return($text);
        }
    }
?>