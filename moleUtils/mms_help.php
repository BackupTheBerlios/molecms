<?php

require_once 'HTML/Template/IT.php';

class mms_help {
    var $hlp_topics = array();
    var $tpl;
    var $template_file='help.tpl.html';

function mms_help($title='Hilfe'){
    $this->tpl=new HTML_Template_IT();
    $this->tpl->loadTemplateFile($this->template_file);
    $this->setTitle($title);
}

function setTitle($title){
    $this->tpl->setVariable('HLP_TITLE',$title);
}

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
