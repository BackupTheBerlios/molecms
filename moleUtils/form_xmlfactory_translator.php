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
// $Id: form_xmlfactory_translator.php,v 1.2 2002/09/03 16:07:49 moleman Exp $

require_once 'HTML_OOH_Form/form_xmlfactory.php';


/**
* Creates a translateable form object from a xml file.
* adds translation capability to form_xmlfactory.
* translates labels and button values
* @access   public
* @package  HTML_OOH_Form*
* @author   tfranz  <tfranz@moleman.de>
*/
class form_xmlfactory_translator extends form_xmlfactory{

    
    /** @var    object lang    $lang    $the appllication's translator
    */
    var $lang;


    /**
    * Returns a form object build from the given xml file.
    *
    * @param    string  xml filename (full path)
    * @param    object lang    $lang    $the appllication's translator
    * @throws   form_error
    * @return   mixed object form on success, otherwise false
    */
    function form_xmlfactory_translator($file,$lang="")
    {
        if($lang){
            $this->lang=$lang;
        }
        $this->form_xmlfactory($file);
     } // end func form_xmlfactory_translator

    
    /*
    * XML Parser opening tag handler
    *
    * @param  resource  xmlparser
    * @param  string    tag name
    * @param  array     tag attributes
    */
    function startElement($parser, $name, $attrs)
    {
       // $name = strtolower($name);
        parent::startElement($parser,$name,$attrs);
        $name = (string)strtolower($name);

        if((isset($this->attributes['label']))&&($this->lang)){
            $this->attributes['label']=$this->lang->tr($this->attributes['label']);
        }
        if((isset($this->attributes['value']))&&($this->lang)){
            $this->attributes['value']=$this->lang->tr((string)$this->attributes['value']);
        }
    } // end func startElement



    function cdata($parser, $cdata)
    {
        parent::cdata($parser, $cdata);
        if((isset($this->attributes['value']))&&($this->lang)){
            $this->attributes['value']=$this->lang->tr((string)$this->attributes['value']);
        }

    } // end func cdata


} // end class form_xmlfactory_translator


?>