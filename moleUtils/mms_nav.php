<?php

require_once 'HTML/Template/IT.php';

class mms_nav{

var $tpl;
var $template_file='leftnav.tpl.html';

function mms_nav($template_file='leftnav.tpl.html'){
    $this->template_file=$template_file;
    $this->tpl=new HTML_Template_IT();
    $this->tpl->loadTemplateFile($this->template_file);
}

function setTitle($title){
    $this->tpl->setVariable('NAV_TITLE',$title);
}

function setImage($image){
    $this->tpl->setVariable('NAV_IMAGE',$image);
}

function setMenu($menu){
    $this->tpl->setVariable('NAV_MENU',$menu);
}

function setExtra($extra){
    if(is_array($extra)){
    $this->tpl->setCurrentBlock('EXTRA_BLOCK');
        while (list ($key, $val) = each ($extra)) {
                $this->tpl->SetVariable('NAV_EXTRA',$val);
                $this->tpl->parseCurrentBlock();
        }
    }
    else{
        $this->tpl->setVariable('NAV_EXTRA',$extra);
    }
}

function get(){
    $this->tpl->parse();
    return $this->tpl->get();
}

function show(){
echo $this->get();
}





}
