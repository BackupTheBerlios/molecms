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
// $Id: mms_localization.php,v 1.2 2002/09/03 16:07:49 moleman Exp $

    //Strategy super class
    class localization{

        var $dict;

        function formatMoney($sum)
        {
            number_format($sum);
        }

        function formatDate($date)
        {
            return $date;
        }

        function translate($phrase)
        {
            if(array_key_exists($phrase,$this->dict)){
                $phrase=$this->dict["$phrase"];
            }
            return($phrase);
        }


        function tr($phrase)
        {
            return $this->translate($phrase);
        }
    }

?>