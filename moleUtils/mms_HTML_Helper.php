<?php

class HTML_Helper extends PEAR{

function HTML_Helper(){
    $this->PEAR();
}

function replace_uri($str) {
  $pattern = '#(^|[^\"=]{1})(http://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm';
  return preg_replace($pattern,"\\1<a href=\"\\2\\3\">\\3</a>\\4",$str);
}

function make_link($str){
    if(!preg_match("/mailto:|http:/", $str)){
        if(preg_match("/@/", $str)){
            $str="mailto:".$str;
        }
        if(preg_match("/www/", $str)){
        $str="http://".$str;
        }

    }
return $this->replace_uri($str);
}

function swap_background_state(){
    if($this->alternate_background_state!="normal"){
        $this->alternate_background_state="normal";
    }
    else{
        $this->alternate_background_state="alternate";
    }
}

function do_alternate_background($class="") {
if ($this->alternate_background){
      if($this->alternate_background_state!="normal"){}
      else{
        $class.=$this->alternate_background_style;
      }
      $class?$class:"";
  }
return $class;
}

}

?>