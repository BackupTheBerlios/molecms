<?php

require_once 'HTML_OOH_Form/form_xmlfactory.php';


class form_xmlfactory_translator extends form_xmlfactory{

    var $lang;

     function form_xmlfactory_translator($file,$lang="") {
        if($lang){
            $this->lang=$lang;
        }
        $this->form_xmlfactory($file);
     }

     function startElement($parser, $name, $attrs) {
       // $name = strtolower($name);

        parent::startElement($parser,$name,$attrs);
        $name = (string)strtolower($name);

        if((isset($this->attributes['label']))&&($this->lang)){
            $this->attributes['label']=$this->lang->tr($this->attributes['label']);
        }
        if((isset($this->attributes['value']))&&($this->lang)){
            $this->attributes['value']=$this->lang->tr((string)$this->attributes['value']);
       }
     }

    function cdata($parser, $cdata) {
        parent::cdata($parser, $cdata);
         if((isset($this->attributes['value']))&&($this->lang)){
            $this->attributes['value']=$this->lang->tr((string)$this->attributes['value']);
       }

    }

}


?>
