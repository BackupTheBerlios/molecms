<?php

    require_once 'PEAR.php';
    require_once 'DB.php';
    require_once 'Config/Config.php';
    require_once  'HTML/Template/IT.php';


    class modOperation extends PEAR
    {

        var $opName = 'defaultOperation';
        var $moduleName = 'defaultModule';
        var $cfg;
        var $db_con;
        var $db_res;
        var $tpl;
        var $query;
        var $data;
        var $limit='none';

        function modOperation()
        {
            global $db_con,$nav,$cfg,$lang;
            $this->PEAR();
            $this->db_con=$db_con;
            $this->cfg=$cfg;
            $this->lang=$lang;
            $this->basedir='modules/'.$this->moduleName.'/';
            $this->tpl= new HTML_Template_IT($this->basedir);
            $this->tpl->loadTemplateFile($this->opName.'.tpl.html');
        }  // end func module

        function setLimit($limit="none")
        {
            $this->limit=$limit;
        }


        function getLimit()
        {
            return $this->limit;
        }




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


        function setDSN($dsn)
        {
            $this->dsn = $dsn;
        }


        function getDSN()
        {
            return $this->dsn;
        }


        function setQuery($query="")
        {
            $this->query=$query;
        }  // end func setQuery


        function getQuery()
        {
            return $this->query;
        } // end func getQuery



        function getData()
        {
            if(($this->limit=='none')||($this->limit==false))
            {
                $query=$this->getQuery();
            }else{
                $query=$this->getQuery().' LIMIT ' . $this->getLimit();
            }
            $this->data = $this->db_con->getAll($query,DB_FETCHMODE_ASSOC);
            return $this->data;
        } //end func getData


        function processTemplate()
        {
        $this->output="you have to overwrite this function in order to get output";
        return $this->output;
        }

        function get()
        {
            $this->getData();
            $this->processTemplate();
            return  $this->tpl->get();
        }

        function show()
        {
            echo $this->get();
        }





    }

?>
