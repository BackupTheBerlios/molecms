<?php

    include_once 'mms_localization.php';

    class german extends localization{

        function formatMoney($sum){
            $text = '&euro; ' . number_format($sum,2,',','.');

            return($text);
        }

        function formatDate($date){
            list($year,$month,$day) = explode("-",$date);
            $date=$day.'.'.$month.'.'.$year;
            return $date;
        }
    }

?>