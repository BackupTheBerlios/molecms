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
// $Id: mms_template_menu.php,v 1.2 2002/09/03 16:07:49 moleman Exp $

include_once("HTML/Menu.php");

/**
 * Class mms_template_menu provides six different menu structures. it is based on menu3 by ulf wendel.
 * @access  public
 * @package navigation
 * @author tfranz <tfranz@moleman.de>
*/

class mms_template_menu extends HTML_menu
{
    var $std_icon="icon_text.gif";
    var $std_folder="icon_folder.gif";
    var $std_empty="empty.gif";
    var $icon_dir="/pic/";
    var $menu_width="150";
    var $class="";
    var $keepEmpty=true;
    var $session;
    var $perm;
    var $aLevel=-1;
    
    
    /** sets the default icons for each different icontype
     * @author tfranz
     * @param   array   $iconset the array containing the iconsnames for each icon
     */
    function setIcons($iconset)
    {
        $this->setIcon('empty',$iconset['empty']);
        $this->setIcon('folder',$iconset['folder']);
        $this->setIcon('node',$iconset['node']);
    
    } // end func setIcons
    
    
    /** sets the icon for the specified node to the given icon
    * @author tfranz <tfranz@moleman.de>
    * @param    string  $icontype    the icon type to change
    * @param    string  $icon    the url of the icon to set
    */
    
    function setIcon($icontype,$icon)
    {
        $this->std_{$icontype}=$icon;
    } // end func setIcon


    /** sets the menuwidth to the given amount.
    * Has only effect on vertical menus (Sitemap, tree)
    * @param    string $width   the width the menu shall have
    * @author   tfranz <tfranz@moleman.de>
    */
    function setMenuWidth($width)
    {
        $this->menu_width=$width;
    } // end func setMenuWidth


    /** sets a stylesheet for output.
    * @param    string  $css    the stylesheet class to use
    * @author   tfranz <tfranz@moleman.de>
    */
    function setStyleClass($css)
    {
        $this->class=$class;
    } // end func setStyleClass


    /** Defines, whether a a node which has a number of 0 children shall be shown
    * @param    boolean $bool   show nodes with no children?
    * @author   tfranz <tfranz@moleman.de>
    */
    function setEmptyNodes($bool)
    {
        $this->keepEmpty=$bool;
    } // end func setemptyNodes


    function getStart() {

        $html = "\n<table width=\"$this->menu_width\" class=\"$this->class\">\n";
        switch ($this->menu_type) {
            case "rows":
                $html = "";
                break;
            case "yahoo":
                $html = "";
                break;
            case "urhere":
                $html="";
                break;
        }

        return $html;
    } // end func getStart


    function getEnd() {

        $html = "</table>";
        switch ($this->menu_type) {
            case "rows":
                $html ="<br />";
                break;
            case "yahoo":
                $html ="";
                break;
            case "urhere":
                $html ="";
                break;
        }

        return $html;
    } // end func getEnd




    /**
    * getEntry for the rows output
    *
    * @brother  getEntry()
    */
    function getEntryRows(&$node, $level, $item_type) {
        $html = "";

       // $node["title"] = str_replace(" ", "&nbsp;", $node["title"]);
       // $space = "&nbsp;|&nbsp;";
       $space='<img src="'.$this->icon_dir.'nav_spacer.png">';

        // draw the <td></td> cell depending on the type of the menu item
        switch ($item_type) {
            case 0:
                // plain menu item
                $html .= sprintf('%s<a href="%s" onmouseover="change_img(\'%s\',\'%s\',\'%s\')" onmouseout="change_img(\'%s\',\'%s\',\'%s\')"%s><img border="0" src="%s" alt="" name="%s"></a>%s',
                                    $indent,
                                    $node["url"],
                                    $node["title"],
                                    $node["title"].'_active',
                                    LC_LANG,
                                    $node["title"],
                                    $node["title"]."_passive",
                                    LC_LANG,
                                    ( isset($node["target"]) ) ? ' target="' . $node["target"] . '"' : "",
                                    $this->icon_dir.$node["title"].'_passive_'.LC_LANG.'.png',
                                    $node["title"],
                                    $space
                                 );
                break;

            case 1:
                // selected (active) menu item
                $html .= sprintf('%s<img src="%s" alt="">%s',
                                   $indent,
                                   $this->icon_dir.$node["title"].'_active_'.LC_LANG.'.png',
                                   $space
                                );
                break;

            case 2:
                // part of the path to the selected (active) menu item
                $html .= sprintf('<a href="%s"%s><img src="%s" alt=""></a>&nbsp;%s',
                                    $node["url"],
                                    ( isset($node["target"]) ) ? ' target="' . $node["target"] . '"' : "",
                                    $node["title"],
                                    $space
                                );
                break;

            case 3:
                // << prev url
                $html .= sprintf('<a href="#top">Top</a>&nbsp;&nbsp;<a href="%s"%s>&lt;</a>&nbsp;&nbsp;',
                                    $node["url"],
                                    ( isset($node["target"]) ) ? ' target="' . $node["target"] . '"' : ""
                                 );
                break;

            case 4:
                // next url >>
                $html .= sprintf('&nbsp;&nbsp;<a href="%s"%s>&gt;</a>',
                                    $node["url"],
                                    ( isset($node["target"]) ) ? ' target="' . $node["target"] . '"' : ""
                                 );
                break;

            case 5:
                // up url ^^
                $html .= sprintf('<a href="%s"%s>^</a>',
                                    $node["url"],
                                    ( isset($node["target"]) ) ? ' target="' . $node["target"] . '"' : ""
                                 );
                break;
        }

        return $html;
    } // end func getEntryRows


    function get_icon($node){
        if($this->menu_type!='urhere'){
            $icon=$this->std_empty;
            if($node["sub"]){
                    $icon=$this->std_folder;
            }
            else{
                    $icon=$this->std_icon;
            }
            ( isset($node["icon"])&&$node["icon"]!="" ) ? $icon=$node["icon"] : $icon;
            return '<img src="'.$this->icon_dir.$icon.'" border="0" alt="">';
        }
        else{return " ";}
    }

    function get_url($node){
        $url="";
        if(isset($node["url"])&&$node["url"]!=""){
                $url=sprintf("<a%s href=\"%s\"%s>",
                        ($this->class)?" class=\"".$this->class."\"" : "",
                        ($this->session)?$this->session->url($node["url"]):$node["url"],
                        ( isset($node["target"]) ) ? ' target="' . $node["target"] . '"' : "");
             //   $node["count"]?$zahl="(".$node["count"].")" : $zahl="";
                ($this->lang=="default") ? $title=$node["title"] : $title=$node["title_".$this->lang];
               // $url.=$title.$zahl."</a>";
        }

        return $url;
    }
    
    function getEntryUrhere(&$node,$level,$item_type){
    $html="";
    $indent="";
    $menuitemtitle=$node["title"];
    switch ($item_type) {
            case 0:
                // plain menu item
                $html .= sprintf('%s%s%s&nbsp;%s</a>&nbsp;%s',
                                    $indent,
                                    $this->get_url($node),
                                    $this->get_icon($node),
                                    $menuitemtitle,
                                    $space
                                 );
                break;

            case 1:
                // selected (active) menu item
                $html .= sprintf('%s%s&nbsp;%s&nbsp;%s',
                                   $indent,
                                   $this->get_icon($node),
                                   $menuitemtitle,
                                   $space
                                );
                break;

            case 2:
                // part of the path to the selected (active) menu item
                $html .= sprintf('%s%s%s&nbsp;%s</a>&nbsp;%s%s',

                                    ("urhere" == $this->menu_type) ? "" : $indent,
                                    $this->get_url($node),
                                    $this->get_icon($node),
                                    $menuitemtitle,
                                    ("urhere" == $this->menu_type) ? "&nbsp;&gt;&nbsp;" : "",
                                    $space
                                );
                break;
            }
    return $html;

    }


    
    /** generates a yahoo-like menu, i.e. shows just the direct children of the active node
    */
    function getEntryYahoo(&$node,$level,$item_type)
    {
        $html="";
        $indent="";
        if($this->shownumchildren)
                $menuitemtitle=$node["title"] . ' ' .$node['numchildren'];
            else
                $menuitemtitle=$node["title"];
        switch ($item_type) {
            case 0:
                // plain menu item

                if($level==$this->aLevel+1){
                    $html .= sprintf('%s%s%s&nbsp;%s</a>&nbsp;%s',
                                        $indent,
                                        $this->get_url($node),
                                        $this->get_icon($node),
                                        $menuitemtitle,
                                        $space
    
                                 );
                    $html.="<br>";
                }
                break;

        }
        return $html;

    } // end func getEntryYahoo



    function getEntry(&$node, $level, $item_type) {

    //checkt permission, wenn node keine Permission hat, wird er fuer jeden lesbar eingestuft,
    // wenn user nicht genuegend Rechte für diesen Node hat, wird ein Leerstring zurückgegeben
    // und die procedure beendet

    if($this->perm){
        !$node["perm"]?$node["perm"]="nobody":$node["perm"];
        if(!$this->perm->have_perm($node["perm"])){
           return "";
        }
    }

    if(($node['numchildren']>0) or ($this->keepEmpty==true)){
        if ("rows" == $this->menu_type)
          return $this->getEntryRows($node, $level, $item_type);
        if ("urhere" == $this->menu_type)
         return $this->getEntryUrhere($node, $level, $item_type);
         if ("yahoo" == $this->menu_type)
         return $this->getEntryYahoo($node, $level, $item_type);



        $html = "";

        $node["title"] = str_replace(" ", "&nbsp;", $node["title"]);

        if ("tree" == $this->menu_type) {
            // tree menu
            $indent = "<img src=\"".$this->icon_dir.$this->std_empty."\" width=\"14\" alt=\"&nbsp;\">";
            if ($level)
                for ($i = 0; $i < $level; $i++)
                    $indent .= "<img src=\"".$this->icon_dir.$this->std_empty."\" width=\"14\"  alt=\"&nbsp;\">";
        }

        // add space between elements?
        if ("urhere" == $this->menu_type || "rows" == $this->menu_type) {
            $space = "";
        } else {
            //$space = '<img src="/grafiken/dummie1x1.gif" width="1" height="14" border="0"><br />';
            $space = "";
        }

        // draw the <td></td> cell depending on the type of the menu item
        if($this->shownumchildren)
            $menuitemtitle=$node["title"] . ' ' .$node['numchildren'];
        else
            $menuitemtitle=$node["title"];

        switch ($item_type) {
            case 0:
                // plain menu item
                $html .= sprintf('<tr%s><td%s nowrap>%s%s%s&nbsp;%s</a>&nbsp;%s</td></tr>',
                                    ' class="'.$this->class.'"',
                                    ' class="'.$this->class.'"',
                                    $indent,
                                    $this->get_url($node),
                                    $this->get_icon($node),
                                    $menuitemtitle,
                                    $space
                                 );
                break;

            case 1:
                // selected (active) menu item
                $html .= sprintf('<tr%s><td%s nowrap>%s%s&nbsp;<span%s>%s</span>&nbsp;%s</td></tr>',
                                    ' class="'.$this->class.'"',
                                    ' class="'.$this->class.'"',
                                   $indent,
                                   $this->get_icon($node),
                                     ' class="'.$this->class.'Active"',
                                   $menuitemtitle,
                                   $space
                                );
                break;

            case 2:
                // part of the path to the selected (active) menu item

                    $html .= sprintf('<tr%s><td%s nowrap>%s%s%s&nbsp;%s</a>&nbsp;%s%s</td></tr>',
                                        ' class="'.$this->class.'"',
                                        ' class="'.$this->class.'"',
                                        ("urhere" == $this->menu_type) ? "" : $indent,
                                        $this->get_url($node),
                                        $this->get_icon($node),
                                        $menuitemtitle,
                                        ("urhere" == $this->menu_type) ? "&nbsp;&lt;&nbsp;" : "",
                                        $space
                                    );
                break;

            case 3:
                // << prev url
                $html .= sprintf('<a href="#top">Top</a>&nbsp;&nbsp;<a href="%s"%s>&lt;</a>&nbsp;&nbsp;',
                                    $node["url"],
                                    ( isset($node["target"]) ) ? ' target="' . $node["target"] . '"' : ""
                                 );
                break;

            case 4:
                // next url >>
                $html .= sprintf('&nbsp;&nbsp;<a href="%s"%s>&gt;</a>',
                                    $node["url"],
                                    ( isset($node["target"]) ) ? ' target="' . $node["target"] . '"' : ""
                                 );
                break;


            case 5:
                // up url ^^
                $html .= sprintf('<a href="%s"%s>^</a>',
                                    $node["url"],
                                    ( isset($node["target"]) ) ? ' target="' . $node["target"] . '"' : ""
                                 );
                break;
        }


    }
        return $html;
    } // end func getEnty


    function activeLevel($menu, $level = 0, $flag_stop_level = -1){
        foreach ($menu as $node_id => $node) {
                if ($this->current_url == $node['url']) {
                    // menu item that fits to this url - 'active' menu item
                    $type = 1;
                    $this->aLevel=$level;
                } else if (isset($this->path[$level]) && $this->path[$level] == $node_id) {
                    // processed menu item is part of the path to the active menu item
                    $type = 2;
                } else {
                    // not selected, not a part of the path to the active menu item
                    $type = 0;
                }


                // follow the subtree if the active menu item is in it
                if ($type && isset($node['sub']))
                    $this->activeLevel($node['sub'], $level + 1);
            }

    }



    function buildMenu($menu, $level = 0, $flag_stop_level = -1) {
        static $last_node = array(), $up_node = array();

        if($this->menu_type!='yahoo'){
            parent::buildMenu($menu,$level,$flag_stop_level);

        }
        else{
            $this->activeLevel($menu,$level,$flag_stop_level);

            foreach ($menu as $node_id => $node) {
                if ($this->current_url == $node['url']) {
                    // menu item that fits to this url - 'active' menu item
                    $type = 1;
                } else if (isset($this->path[$level]) && $this->path[$level] == $node_id) {
                    // processed menu item is part of the path to the active menu item
                    $type = 2;
                } else {
                    // not selected, not a part of the path to the active menu item
                    $type = 0;
                }

                $this->html .= $this->getEntry($node, $level, $type);

                // follow the subtree if the active menu item is in it
                if ($type && isset($node['sub']))
                    $this->buildMenu($node['sub'], $level + 1);
            }
        }
    }

   

} // end class redsys_menu
?>