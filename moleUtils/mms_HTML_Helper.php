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
// $Id: mms_HTML_Helper.php,v 1.2 2002/09/03 16:07:49 moleman Exp $

require_once 'PEAR.php';

/** Provides some often used functions
* @author   tfranz  <tfranz@moleman.de>
*/
class HTML_Helper extends PEAR{


    /** Sets up the helper
    */
    function HTML_Helper(){
        $this->PEAR();
    } // end func HTML_Helper


    
    /**Parses a string for uris and replaces the found ones with a link
    * @param    string  $str    the string to parse
    * @return   string  url-ized string
    * @author   tfranz  <tfranz@moleman.de>
    */
    function replaceUri($str)
    {
      $pattern = '#(^|[^\"=]{1})(http://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm';
      return preg_replace($pattern,"\\1<a href=\"\\2\\3\">\\3</a>\\4",$str);
    } // end func replaceUri



    /** Convenience function: Behaves like replaceUri, but tries to be smart
    * adds a mailto: in front of any word containig an @ and a http:// before
    * any word starting with www so also these "loose" uris can be recgnised
    * @see      HTML_Helper::replaceUri
    * @param    string  $str    the string to parse
    * @return   string  url-ized string
    * @author   tfranz  <tfranz@moleman.de>
    */
    function makeLink($str)
    {
        // has no valid protocol?
        if(!preg_match("/mailto:|http:/", $str)){
            // check for possible email-address (@)
            if(preg_match("/@/", $str)){
                $str="mailto:".$str;    // add protocol
            }
            // check for possible website
            if(preg_match("/www/", $str)){
                $str="http://".$str; // add protocol
            }
    
        }
        //replace protocols with link
        return $this->replaceUri($str);
    } // end func makeLink


    /**
    * Generates different types of URLs. Either /index.php?module=foo?id=bar
    * or /foo/bar.html which is useful for sites who qant to appear static (needs apache's mod_rewrite)
    +
    * @param    array   params  The information about the url
    *                           (module affected, operation in the module, id)
    * @param    string  mode    Whether to generate url using index.php or dirctory-like urls
    *                           (allowed vaues: plain, rewrite)
    * @author   tfranz  <tfranz@moleman.de>
    */    
    function url($params,$mode='plain')
    {

        //generate url with parameters

        if ($mode=='plain'){
            $url='/index.php?';
            $urlparams='';
            if (is_array($params)){
                foreach($params as $key => $value){
                    $urlparams.=$key.'='.$value.'&';
                }
                $urlparams=substr($urlparams,0,-1);
            }
            else{
                $urlparams=$params;
            }
            $url.=$urlparams;
        }

        // generate url suitable for mod_rewrite
    
        elseif($mode=='rewrite'){
            if($params['module']){
                $module=$params['module'].'/';
            }
            if(array_key_exists('action',$params) && array_key_exists('id',$params)){
                $action=$params['action'].'/'.$params['id'].'.html';
            }
            else{
                $action=$params['action'].'.html';
            }
            $url='/'.$module.$action;
        }
        return $url;
    } // end func url

    
    
    function swap_background_state()
    {
        if($this->alternate_background_state!="normal"){
            $this->alternate_background_state="normal";
        }else{
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
    
} // end class HTML_Helper

?>