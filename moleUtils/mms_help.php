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
// $Id: mms_help.php,v 1.2 2002/09/03 16:07:49 moleman Exp $

require_once 'HTML/Template/IT.php';



/** Provides a simple Help panel for use with HTML_OOH_Forms.
*
* Example:
* Create an HTML_OOH_Form in XML
* In the HTML-Section of each element add
* <html >onFocus="showHelp('topic', true)" onBlur="showHelp('topic', false)"</html >
*
* //Create the helper application
* $hlp=new mms_help('This is the helper');
* //add the topics
* $hlp->addTopic('topic','help sentences');
* //displays help form
* $hlp->show();
* If Javascript is enabled, the Help function acts dynamic and
* highlights the currently selected topic
* @author   tfranz  <tfranz@moleman.de>
*/
class mms_help {

    
    /**@var array   $hlp_topics Contains the help topics
    */
    var $hlp_topics = array();

    
    /**@var object HTML_Template_IT $tpl    the template system
    */
    var $tpl;

    
    /**
    */
    var $template_file='help.tpl.html';

    function mms_help($title='Hilfe'){
        $this->tpl=new HTML_Template_IT();
        $this->tpl->loadTemplateFile($this->template_file);
        $this->setTitle($title);
    } // end func mms_help

    
    /** Sets the title of the helpwindow
    * @param    string  $title  title
    */
    function setTitle($title){
        $this->tpl->setVariable('HLP_TITLE',$title);
    } // end func setTitle

    
    /** Adds a help topic to the panel
    * @param    string  $name   the keyword of the help.
    */
    function addTopic($name,$content){
        $this->hlp_topics["$name"]=$content;
    }
    
    function alterTopic($name,$content){
        $this->hlp_topics["$name"]=$content;
    }
    
    function deleteTopic($name){
    }
    
    function get(){
        if(count($this->hlp_topics)==0){
            return '';
        }else{
            $this->tpl->setCurrentBlock('HLP_BLOCK');
            while (list ($key, $val) = each ($this->hlp_topics)) {
                    $this->tpl->SetVariable('HLP_TOPIC',$key);
                    $this->tpl->SetVariable('HLP_HEADING',$val['title']);
                    $this->tpl->SetVariable('HLP_DESCRIPTION',$val['description']);
                    $this->tpl->parseCurrentBlock();
            }
            $this->tpl->parse();
            return $this->tpl->get();
        }
    }
    
    
    function show(){
        echo $this->get();
    }
    
    
    

}

?>