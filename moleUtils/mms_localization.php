<?php

    //Strategy super class
    class localization{

        var $dict;

        function formatMoney($sum){
            number_format($sum);
        }

        function formatDate($date){
            return $date;
        }

        function translate($phrase){
            if(array_key_exists($phrase,$this->dict)){
                $phrase=$this->dict["$phrase"];
            }
            return($phrase);
        }


        function tr($phrase){
            return $this->translate($phrase);
        }
    }

?>
