<?php

    include_once 'mms_localization.php';

    class english extends localization{

        function formatMoney($sum){
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