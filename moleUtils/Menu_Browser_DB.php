<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Tim Franz <tfranz@mmoleman.de>                              |
// +----------------------------------------------------------------------+
//
// $Id: Menu_Browser_DB.php,v 1.2 2002/08/07 09:02:44 moleman Exp $

/**
* Simple filesystem browser that can be used to generated menu (3) hashes based on the directory structure.
*
* Together with menu (3) and the (userland) cache you can use this
* browser to generate simple fusebox like applications / content systems.
*
* Let the menubrowser scan your document root and generate a menu (3) structure
* hash which maps the directory structure, pass it to menu's setMethod() and optionally
* wrap the cache around all this to save script runs. If you do so, it looks
* like this:
*
* // document root directory
* define('DOC_ROOT', '/home/server/www.example.com/');
*
* // instantiate the menubrowser
* $browser = new menubrowser(DOC_ROOT);
*
* // instantiate menu (3)
* $menu = new menu($browser->getMenu());
*
* // output the sitemap
* $menu->show('sitemap');
*
* Now, use e.g. simple XML files to store your content and additional menu informations
* (title!). Subclass exploreFile() depending on your file format.
*
* @author   Ulf Wendel <ulf.wendel@phpdoc.de>
* @version  $Id: Menu_Browser_DB.php,v 1.2 2002/08/07 09:02:44 moleman Exp $
*/
include_once 'DB.php';

class HTML_Menu_Browser {

    /**
    * Prefix for every menu hash entry.
    *
    * Set the ID prefix if you want to merge the browser menu
    * hash with another (static) menu hash so that there're no
    * name clashes with the ids.
    *
    * @var  string
    * @see  setIDPrefix()
    */
    var $id_prefix = '';

    /**
    * Menu (3)'s setMenu() hash.
    *
    * @var  array
    */
    var $menu = array();
    var $forumarray= array();
    var $kindarray= array();

    var $lang = 'en';
    

    /**
    * Creates the object and optionally sets the directory to scan.
    *
    * @param    string
    * @see      $dir
    */
    function HTML_Menu_Browser($DSN = '', $table = '', $query="") {
        if ($DSN)
            $this->setDSN($DSN);
        if ($table)
            $this->setTable($table);
        if(!$query){
            $query="SELECT * FROM $this->table";
        }
        $this->setQuery($query);
        $this->lang=LC_LANG;
    }

    /**
    * Sets the directory to scan.
    *
    * @param    string  directory to scan
    * @access   public
    */
    function setDSN($DSN) {
        $this->DSN=$DSN;
    }


    function setTable($table) {
        $this->table=$table;
    }

    function setQuery($query){
        $this->query=$query;
    }

    /**
    * Sets the prefix for every id in the menu hash.
    *
    * @param    string
    * @access   public
    */
    function setIDPrefix($prefix) {
        $this->id_prefix = $prefix;
    }

    /**
    * Returns a hash to be used with menu(3)'s setMenu().
    *
    * @param    string  directory to scan
    * @param    string  id prefix
    * @access   public
    */
    function getMenu($DSN = '',$prefix = '') {
        if ($DSN)
            $this->setDSN($DSN);
        if ($prefix)
            $this->setIDPrefix($prefix);

        $db_con= DB::connect($this->DSN);
        $db_res= $db_con->query($this->query);

        while($tmp = $db_res->fetchRow(DB_FETCHMODE_ASSOC)) {  // Ergebnis holen
            $this->forumarray[ $tmp["id"] ] = $tmp;          // Ergebnis im Array ablegen
            $this->kindarray[ $tmp["parent_id"] ][] =  $tmp["id"]; // Vorwärtsbezüge konstruieren
        }


        $struct=array();
        if(is_array($this->kindarray)) {
            foreach($this->kindarray[0] as $thread) {
                $tmp=$this->browse($thread);
                $struct[$thread]['title']=$tmp[$thread]['title'];
                $struct[$thread]['url']=$tmp[$thread]['url'];
                $struct[$thread]['numchildren']+=$tmp[$thread]['numchildren'];
                $struct[$thread]['child_id']+=$tmp[$thread]['child_id'];
                if(is_array($tmp[$thread]['sub'])) {
                    $struct[$thread]['sub']=$tmp[$thread]['sub'];
                }
            }
        }
        $this->menu=$struct;
        return $this->menu;
    }

    function makeStringFromArray($arr){
        $arr_str="";
        if(is_array($arr)){
            foreach ($arr as $arr_el){
                $arr_str.=$arr_el.',';
            }
            $arr_str=substr($arr_str,0,-1);
        }
        return $arr_str;
    }

    function makeURL($eintrag){
        global $PHP_SELF;
        $url=$PHP_SELF.'?id='.$eintrag['url'];
        return $url;
    }

    /**
    * Recursive function that does the scan and builds the menu (3) hash.
    *
    * @param    string  directory to scan
    * @param    integer entry id - used only for recursion
    * @param    boolean ??? - used only for recursion
    * @return   array
    */
    function browse($eintrag){
   // echo $this->lang;
        $struct=array();
        $struct[$this->id_prefix.$eintrag]['title']=$this->forumarray[$eintrag]['name_'.$this->lang];
        $struct[$this->id_prefix.$eintrag]['url']=$this->makeURL($this->forumarray[$eintrag]);
        $struct[$eintrag]['numchildren']=$this->forumarray[$eintrag]['numchildren'];
        $struct[$eintrag]['child_id']=$this->makeStringFromArray($this->kindarray[$eintrag]);
        if(is_array($this->kindarray[$this->id_prefix.$eintrag])) {
            $children=0;
            foreach($this->kindarray[$eintrag] as $kind) {
                $tmp=$this->browse($kind);
                $struct[$this->id_prefix.$eintrag]['sub'][$kind]['title']=$tmp[$kind]['title'];
                $struct[$this->id_prefix.$eintrag]['sub'][$kind]['url']=$tmp[$kind]['url'];
                $struct[$this->id_prefix.$eintrag]['sub'][$kind]['numchildren']+=$tmp[$kind]['numchildren'];
             /*   if(strlen($tmp[$kind]['child_id']>0)){
                    $struct[$eintrag]['sub'][$kind]['child_id'].=$tmp[$kind]['child_id'];
                }  */
                if(is_array($tmp[$kind]['sub'])) {
                    $struct[$this->id_prefix.$eintrag]['sub'][$kind]['sub']=$tmp[$kind]['sub'];
                }
                $anderekinder+=$struct[$this->id_prefix.$eintrag]['sub'][$kind]['numchildren'];
                if(strlen($struct[$eintrag]['sub'][$kind]['kinder']) >0){
                    $struct[$eintrag]['child_id'].=','.$struct[$eintrag]['sub'][$kind]['child_id'] ;
                }
            }
        }
        $struct[$this->id_prefix.$eintrag]['numchildren']+=$children+$anderekinder;
        $this->child_id[$eintrag]=$struct[$eintrag]['child_id'];
        return $struct;

    }

    /**
    * Adds further informations to the menu hash gathered from the files in it
    *
    * @var      array   Menu hash to examine
    * @return   array   Modified menu hash with the new informations
    */
    function addFileInfo($menu) {
        // no foreach - it works on a copy - the recursive
        // structure requires already lots of memory
        reset($menu);
        while (list($id, $data) = each($menu)) {
            $menu[$id] = array_merge($data, $this->exploreFile($data['url']));
            if (isset($data['sub']))
                $menu[$id]['sub'] = $this->addFileInfo($data['sub']);
        }

        return $menu;
    }

    /**
    * Returns additional menu informations decoded in the file that appears in the menu.
    *
    * You should subclass this method to make it work with your own
    * file formats. I used a simple XML format to store the content.
    *
    * @param    string  filename
    */
    function exploreFile($file) {
        $xml = join('', @file($file));
        if (!$xml)
            return array();

        $doc = xmldoc($xml);
        $xpc = xpath_new_context($doc);

        $menu = xpath_eval($xpc, '//menu');
        $node = &$menu->nodeset[0];

        return array('title' => $node->content);
    }
}
?>