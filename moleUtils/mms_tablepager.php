<?php

require_once 'HTML/Table.php';
require_once 'DB_Pager/Pager.php';

class PageTable extends PEAR {

    var $db;
    var $limit=50;
    var $table;
    var $fields;
    var $map_cols;
    var $check;
    var $heading=true;
    var $make_url =true;
    var $alternate_background=true;
    var $alternate_background_style="alternate";
    var $indexcol="id";
    var $add_extra=true;
    var $url_delete = "";      // fill in an url and it will appear as an extra column labeled "aendern";
    var $url_change = "";
    var $url_view   = "";
    var $col_view = "";
    var $col_change="";
    var $col_delete="";

    var $str_emptyresult="no data matching your query";

    var $lang=LC_LANG;

    var $dict = array(
            "de" => array(
                "delete"    => "löschen",
                "change"    => "ändern",
                "view"      => "anzeigen",
                "rows"      => "Datens&auml;tze"
            ),
            "en" => array(
                "delete"    => "delete",
                "change"    => "change",
                "view"      => "view",
                "rows"       => "rows"
            )
        );


function PageTable($dsn="",$class="") {
    $this->PEAR();
    $this->url=$_SERVER['PHP_SELF'];
    $this->db= DB::connect($dsn);
    $this->table=new HTML_Table($class?"class=".$class:"");
}

function replace_uri($str) {
    $pattern = '#(^|[^\"=]{1})(http://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm';
    return preg_replace($pattern,"\\1<a href=\"\\2\\3\">\\3</a>\\4",$str);
}

function setEmptyString($emptystring){
    $this->str_emptyresult=$emptystring;
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

function table_Header($class=""){
    $header_cols=array();
    if($this->check){
        $header_cols[]="&nbsp;";
    }
    foreach ($this->fields as $field) {
        $map_field = $this->map_cols[$field];
        if (strlen($map_field)>0){
            $field=$map_field;
        }
        $header_cols[]=$field;
    }
    if($this->add_extra){
        if($this->url_change){
            $header_cols[]="";
        }
        if($this->url_delete){
            $header_cols[]="";
        }
        if($this->url_view){
            $header_cols[]="";
        }
    }
    $this->table->addRow($header_cols,$class?array("class"=>$class):"",'TH');
}

function table_Body($pager,$from=0,$class=""){
    while ($row = $pager->fetchrow(DB_FETCHMODE_ASSOC)){
        $this->table_row($row[$this->check],$row,$class);
    }
}

function table_row($row,$data,$class=""){
    $show_arr=array();
        if($this->check){
            $show_arr[]=$this->table_checkbox_cell($row,$row,$data);
        }
        foreach ($this->fields as $field) {
            if($this->make_url){
                $show_arr[]=$this->make_link($data[$field]);
            }
            else{
                if($this->col_view==$field){
                 (strstr($this->url_view ,"?")=="") ? $trenner="?" : $trenner="&amp;";
                    $show_arr[]=sprintf("<a href=\"%s%sid=%s\">%s</a>",
                        $this->url_view,
                        $trenner,
                        $data[$this->indexcol],
                        $data[$field]);
                }
                else{
                    $show_arr[]=$data[$field];
                }
            }

        }
        if($this->add_extra){
            if(!empty($this->url_change)){
                (strstr($this->url_change ,"?")=="") ? $trenner="?" : $trenner="&amp;";
                $show_arr[]=sprintf("<a href=\"%s%sid=%s\">%s</a>",
                $this->url_change,
                $trenner,
                $data[$this->indexcol],
                $this->dict[$this->lang]["change"]);
            }
            if(!empty($this->url_delete)){
                (strstr($this->url_delete ,"?")=="") ? $trenner="?" : $trenner="&amp;";
                $show_arr[]=sprintf("<a href=\"%s%sid=%s\">%s</a>",
                $this->url_delete,
                $trenner,
                $data[$this->indexcol],
                $this->dict[$this->lang]["delete"]);
            }
            if(!empty($this->url_view)){
                (strstr($this->url_view ,"?")=="") ? $trenner="?" : $trenner="&amp;";
                $show_arr[]=sprintf("<a href=\"%s%sid=%s\">%s</a>",
                $this->url_view,
                $trenner,
                $data[$this->indexcol],
                $this->dict[$this->lang]["view"]);
            }
        }
        $this->table->addRow($show_arr);
}

function table_Footer($class=""){
}


function get($query,$from=0,$class=""){

    $res=$this->db->query($query);
        $pager = new DB_Pager ($res, $from, $this->limit);
        $data = $pager->build();

        if (DB::isError($data)){
            return $this->raiseError(DB::errorMessage($data));
        }

        if (!$data) {
            //return $this->raiseError("There were no results");
            return $this->str_emptyresult;
        }
    if($this->heading){
        $this->table_Header($class);
    }
    $this->table_Body($pager,$from,$class);
    $this->table_Footer($class);

    if ($class){
        $row1Style = array ("class"=>$class);
        if ($this->alternate_background){
            $row2Style = array ("class" =>$class.$this->alternate_background_style);
            $this->table->altRowAttributes(1,$row1Style,$row2Style);
        }
    }

    return $this->meta_head($data)."<br>".$this->get_nav($data).$this->table->toHTML().$this->get_nav($data);
}

function show($query,$from=0,$class=""){
    echo $this->get($query,$from,$class);
}

function get_numRows($data){
    return $data['numrows'].' '. $this->dict[$this->lang]['rows'];
}

function get_numPages($data){
  // Num pages
  return $data['current'].' of '.$data['numpages']. "pages<br>";
}

function meta_head($data){
    return $this->get_numRows($data);
}

function getURLTrenner($url){
    $trenner = ( strpos($url, "?") == false ?  "?" : "&" );
    return $trenner;
}

function nav_prev($data){
    $nav="";
    if ($data['current']!=1){
        $nav='<a href="'. $this->url.$this->getURLTrenner($this->url)."from=" . $data['prev'] . '"> ' .'&lt;-&nbsp;prev&nbsp;'. $data['limit'] . '</a>&nbsp;&nbsp;&nbsp; ';
    }
    else $nav="";
    return $nav;
}

function nav_next($data){
    $nav="";
    if ($data['current']<$data['numpages']){
        $trenner = ( strpos($this->url, "?") == false ?  "?" : "&" );
        $nav.=' &nbsp;&nbsp;&nbsp;<a href="'. $this->url.$this->getURLTrenner($this->url)."from=" . $data['next'] . '"> ' . $data['remain'] . '&nbsp;next-&gt;</a>';
    }
    else $nav="";
    return $nav;

}

function nav_pages($data){
    if($data['numpages']>1){
        $pageselect="&nbsp;";
        foreach ($data['pages'] as $page => $start_row) {
            if($page==$data["current"]){
                $pageselect.="<strong>&gt;$page&lt;</strong>&nbsp;";
            }
            else{
                $pageselect.="<a href=\"".$this->getURLTrenner($this->url)."from=$start_row\">$page</a>&nbsp;";
            }
        $nav=$pageselect;
        }
    }
    else{
        $nav="";
    }
    return $nav;
}

function get_nav($data){
   $nav=$this->nav_prev($data).$this->nav_pages($data).$this->nav_next($data);
   return $nav;
}




function setLimit($limit=20){
    $this->limit=$limit;
}

 function table_checkbox_cell($row, $row_key, $data)
  {
    if ($this->check)
    {
     $checkbox=sprintf("<input type=\"checkbox\" name=\"%s[%s]\" value=\"%s\">",
        $this->check,
        $row,
        empty($data[$this->check])?$row_key:$data[$this->check]);
    }
    return $checkbox;
  }

 /**
  *Selects the column names that should be displayed in an HTML
  *            table. This is based on the $fields variable, if set. If it
  *            is not set, then all fields names are used. This is how you
  *            filter displayed data.
  * array    $data     An array containing information about the column
  *                    names. If $fields is not used, then this variable is
  *                    used instead.
  * @access    private
  * @returns  : An array containing the column names.
  */

  function select_colnames($data)
  {
    global $debug;

    if ($debug)
      printf("<p>select_colnames()<br>\n");

    if (!is_array($this->fields) && is_array($data))
    {
      reset($data);
      while(list($key, $val) = each($data))
      {
        if (ereg($this->filter, $key))
          $this->fields[] = $key;
      }
    }
    $d = $this->fields;

    if ($debug)
    {
      print_array($d);
      printf("select_colnames() return<br>");
    }

    return $d;
  }


}

?>
