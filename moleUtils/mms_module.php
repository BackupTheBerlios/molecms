<?php

    require_once 'PEAR.php';
    require_once 'HTML/Template/IT.php';
    require_once 'DB.php';
    require_once 'Config/Config.php';

    class module extends PEAR
    {

        var $name='module_prototype';
        var $color;
        var $basedir;
        var $cfg;
        var $db_con;
        var $db_res;
        var $tpl;
        var $today;

        function module()
        {
            global $db_con,$nav,$cfg,$lang;
            $this->PEAR();
            $this->db_con=$db_con;
            $this->cfg=$cfg;
            $this->lang=$lang;
           // $this->today=date("Ymd",time());
            $this->basedir='modules/'.$this->name.'/';
            $this->tpl= new HTML_Template_IT($basedir);
        }  // end func module

        function getNavArray()
        {

        }     //end func getNavArray

        function getNav()
        {
            include_once 'mms_template_menu.php';
            $menu=new mms_template_menu($this->getNavArray(),'tree',"REQUEST_URI");
            $menu->std_icon="pfeilchen_orange.gif";
            $menu->class="leftnav";
            return $menu->get();

        }  //end func getNav

        function get_extlink($str)
        {
            $pattern = '#(^|[^\"=]{1})(http://)([^\s<>]+)([\s\n<>]|$)#sm';
            return preg_replace($pattern,"\\1<img src=\"pic/ext_link.gif\" alt=\"(externer Link)\"><a href=\"\\2\\3\">\\3</a>\\4",$str);
        }   //end func get_extlink

        /**
          Replaces an URI with the HTML-Clickable equivalent
        
          @param    string  str The string to be parsed an replaced
          @return   string  str The tag-added string
          @see      make_link
        
        */
        
        function replace_uri($str)
        {
            $pattern = '#(^|[^\"=]{1})(http://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm';
            return preg_replace($pattern,"\\1<a href=\"\\2\\3\">\\3</a>\\4",$str);
        } // end func replace_uri
        
        
        
        
        /**
          Checks if the URI to translate is prefixed with an internet protocol
          if not, the protocol is added. Calls then replace_uri.
        
          @see      replace_uri
          @param    string  str The string to be parsed an replaced
          @return   string  str The tag-added string
        
        */
        
        function make_link($str)
        {
            if(!preg_match("/mailto:|http:/", $str))
            {
                if(preg_match("/@/", $str))
                {
                    $str="mailto:".$str;
                }
                if(preg_match("/www/", $str))
                {
                    $str="http://".$str;
                }
        
            }
            return $this->replace_uri($str);
        }   // end func make_link


        function _overviewQuery($mode='default')
        {
            return "";
        }

        function _overviewAssignTemplates($mode='default')
        {
        }

        function getOverview($mode='default',$tplname="overview.tpl.html")
        {
            $this->tpl= new HTML_Template_IT($basedir);
            $this->tpl->loadTemplateFile($this->basedir.$tplname);
            if($mode=='premium')
            {
                $limit=10;
            }else{
                $limit=$this->cfg['database']['limit_rows'];
            }
            $this->db_res = $this->db_con->limitQuery($this->_overviewQuery($mode),0,$limit);
            $this->_overviewAssignTemplates($mode);

            return $this->tpl->get();

        }


        function _detailsQuery($mode='default')
        {
            return "";
        }


        function _detailsAssignTemplates($mode='default')
        {
        }



        function getDetails($id,$tplname="details.tpl.html")
        {
            $this->tpl= new HTML_Template_IT($basedir);
            $this->tpl->loadTemplateFile($this->basedir.$tplname);
            $this->db_res=$this->db_con->Query($this->_detailsQuery($id));
            $this->_detailsAssignTemplates();

            return $this->tpl->get();
        }


        function getName()
        {
            return $this->name;
        }

        function setName($name)
        {
            $this->name=$name;
        }

        function getColor()
        {
            return $this->color;
        }

        function setColor($color)
        {
            $this->color=$color;
        }


    }

?>
