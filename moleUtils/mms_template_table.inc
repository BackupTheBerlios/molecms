<?php
// deprecated!!!
# version 1.1-1, 2001/08/24 tf

# mms_template_table.inc

#
# Passt die Klasse table.inc so an, dass nicht direkt auf
# den Bildschirm ausgegeben wird, sondern in einen String,
# der mit der Template-Klasse ausgegeben werden kann.
#
# $tpl->set_var("TABELLE",$tbl->show_result(...))
#
# benutze echo $tbl->show_result(...) um Tabelle direkt auszugeben

#changes:
#   2001/08/24 re-added support for user-defined indexcol (sets the column from which the
#              value is appended to the links in add_extra())
#   2001/08/24 ability to generate correct urls including already & or ?
#   2001/08/17 added data rotating for show_result(). Others coming soon
#   2001/08/16 added optional uri-highlighting
#   2001/08/15 added optional alternating background algorithm
#   2001/04/23 changed require to require_once



require_once("table.inc");

/**
 * helps in generating tables out of arrays or db result sets.
 * @access  public
 * @package html helper
*/

class template_Table extends Table {
        var $classname = "template_Table";
        var $url_delete = "";      # fill in an url and it will appear as an extra column labeled "aendern";
        var $url_change = "";
        var $url_view   = "";
        var $tbl_data = "";        # contains the table conde. use echo $tbl->tbl_data to output the table
        var $rows;
        var $alternate_background=false; #if set to true, background will alter every row
        var $alternate_background_style="alternate";   #suffix to stylesheet with alternating color.
                                                        #create a stylesheet for example named my_tab,
                                                        #then create another mytab_alternate for
                                                        #the alternating backgroundcolor
        var $alternate_background_state="alternate";    #entry state for background mode.
        var $make_url =false;                            #should urls be changed to <a href"...
        var $flip =false;                                #table rotating 90°
        var $keep_empty=true;                          #replace empty cells with &nbsp; or not
        var $indexcol=0;
        var $lang="de";

        var $dict = array(
            "de" => array(
                "delete"    => "löschen",
                "change"    => "ändern",
                "view"      => "anzeigen"
            ),
            "en" => array(
                "delete"    => "delete",
                "change"    => "change",
                "view"      => "view"
            )
        );


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



  function start($ary, $class="")
  {
    return ($this->show($ary, $class));
  }

  function start_result($db, $class="")
  {
    return ($this->show_result($db, $class));
  }

  function show($ary, $class="")
  {
  print ($this->get($ary,$class));
  }

  function get($ary, $class="")
  {
    global $debug;

    if (!$this->verify_2d_array($ary))
      return "";

    $this->rows = 0;
    $this->tbl_data="";

    $this->table_open($class);
    if ($this->show_table_heading_row($ary, $class))
      $rows = $this->show_table_rows($ary, $class);
    $this->table_close($class);

    $this->rows=$rows;

    return $this->tbl_data;
  }

  function show_result($db, $class="")
  {
    print($this->get_result($db, $class));
  }

  function get_result($db, $class="")
  {
    if (!$this->verify_db($db))
      return "";

    $this->rows = 0;
    $this->tbl_data="";

    if (!$this->flip){

        $this->table_open($class);
        if ($this->show_table_heading_row_result($db, $class))
        $rows = $this->show_table_rows_result($db, $class);
        $this->table_close($class);
    }
    else{
        $this->table_open($class);
        $rows = $this->show_table_rows_result($db, $class);
        $this->table_close($class);

    }
    $this->rows=$rows;

    return $this->tbl_data;
  }

  function show_page($ary, $start, $num, $class ="")
  {
    print($this->get_page($ary, $start, $num, $class =""));
  }


  function get_page($ary, $start, $num, $class ="")
  {
    global $debug;

    if (!$this->verify_2d_array($ary))
      return "";

    $this->rows = 0;
    $this->tbl_data="";

    $this->table_open($class);
	if ($this->show_table_heading_row($ary, $class))
      $rows = $this->show_table_page_rows($ary, $start, $num, $class="");
    $this->table_close($class);

    $this->rows=$rows;

    return $this->tbl_data;
  }

  function show_result_page($db, $start, $num, $class="")
  {
    print($this->get_result_page($db, $start, $num, $class=""));
  }

  function get_result_page($db, $start, $num, $class="")
  {
    global $debug;

    if (!$this->verify_db($db))
      return "";

    $this->rows = 0;
    $this->tbl_data="";

    $this->table_open($class);
    if ($this->show_table_heading_row_result($db, $class))
		$rows = $this->show_table_page_rows_result($db, $start, $num, $class);
    $this->table_close($class);

    $this->rows=$rows;

    return $this->tbl_data;
  }

  function show_table_rows_result($db, $class="")
  {
    global $debug;

    if ($debug)
      printf("<p>show_table_rows_result()<br>\n");

    if (!$this->verify_db($db))
      return 0;

    $row = 0;

    if(!$this->flip){
        while($db->next_record())
        {
            ## Process a table row
            $this->table_row($row, $row, $db->Record, $class);
            $row++;
        }
    }
    else{
        $meta=$db->metadata();
        $db->seek(0);
        $d = $this->select_colnames($db->Record);

        for($i=0;$i < sizeof($d);$i++){
            $this->table_row_open($row,$db->Record,$class);
            $db->seek(0);

            if($this->heading){
             $this->table_heading_cell($col,$d[$row],$class);
            }

           # $this->table_cell($row,$col,"key",$meta[$row]["name"],$class);
           # for($col=0;$col<$db->num_rows();$col++){
           for($col=0;$col<$db->num_rows();$col++){
                $db->next_record();
                $this->table_cell($row,$col,"key",$db->f($d[$row]),$class);
            }

            $this->table_row_close($class);
            $row++;
        }
    }

    return $row;
  }


  function table_cell($row, $col, $key, $val, $class="")
  {
    if($this->make_url){
        $val=$this->make_link($val);
    }

    $this->table_cell_open($class);
    if($this->keep_empty){
        $this->tbl_data.=sprintf("%s", $val?$val:"&nbsp;");
    }
    else{
        $this->tbl_data.=sprintf("%s", $val);
    }
    $this->table_cell_close($class);
  }

  function table_cell_open($class="")
  {
    $class=$this->do_alternate_background($class);
     $this->tbl_data .= sprintf("  <td%s>",
      $class?" class=\"$class\"":"");
  }

  function table_cell_close($class="")
  {
    $this->tbl_data .= sprintf("</td>\n");
  }

  function table_heading_cell($col, $val, $class="")
  {
    $this->table_heading_cell_open($class);

    ## Check for column name remapping
    if ($this->verify_array($this->map_cols))
    {
      reset($this->map_cols);
      while(list($key, $name) = each($this->map_cols))
      {
        if ($key == $val)
        {
          $val = $name;
          $found = 1;
          break;
        }
      }
    }

    $this->tbl_data .= sprintf("%s", $val);
    $this->table_heading_cell_close($class);
  }

  function table_heading_cell_open($class="")
  {
    $this->tbl_data .= sprintf("  <th%s>", $class?" class=$class":"");
  }

  function table_heading_cell_close($class="")
  {
    $this->tbl_data .= sprintf("</th>\n");
  }

  function table_checkbox_cell($row, $row_key, $data, $class="")
  {
    if ($this->check)
    {
      $this->tbl_data .= sprintf("  <td%s><input type=\"checkbox\" name=\"%s[%s]\" value=\"%s\"></td>\n",
        $class?" class=$class":"",
        $this->check,
        $row,
        empty($data[$this->check])?$row_key:$data[$this->check]);
    }
  }

  function table_open($class="")
  {
    global $debug;

    if ($debug)
      printf("<p>table_open()<br>\n");

    $this->tbl_data .= sprintf("<table%s>\n", $class?" class=\"$class\"":"");
  }

  function table_close($class="")
  {
    global $debug;

    if ($debug)
      printf("<p>table_close()<br>\n");

    $this->tbl_data .= sprintf("</table>\n");
  }

  function table_row_open($row, $data, $class="")
  {
  $this->swap_background_state();
    $class=$this->do_alternate_background($class);

    $this->tbl_data .= sprintf(" <tr%s>\n", $class?" class=\"$class\"":"");
  }

  function table_row_close($row, $class="")
  {
    $this->tbl_data .= sprintf(" </tr>\n");
  }

  function table_heading_row_add_extra($data, $class="")
  {
  if(!empty($this->url_change)){
			$this->tbl_data.=sprintf("  <th%s>&nbsp;</th>\n",$class?" class=\"$class\"":"");
		}
		if(!empty($this->url_delete)){
			$this->tbl_data.=sprintf("  <th%s>&nbsp;</th>\n",$class?" class=\"$class\"":"");
		}
        if(!empty($this->url_view)){
			$this->tbl_data.=sprintf("  <th%s>&nbsp;</th>\n",$class?" class=\"$class\"":"");
		}
  }

  	function table_row_add_extra($row, $row_key, $data, $class=""){
        $class=$this->do_alternate_background($class);
		if(!empty($this->url_change)){
                        (strstr($this->url_change ,"?")=="") ? $trenner="?" : $trenner="&amp;";
			$this->tbl_data.=sprintf("  <td%s><a href=\"%s%sid=%s\">%s</a></td>\n",
                        $class?" class=\"$class\"":"",
                        $this->url_change,
                        $trenner,
                        $data[$this->indexcol],
                        $this->dict[$this->lang]["change"]);
		}
		if(!empty($this->url_delete)){
                        (strstr($this->url_delete ,"?")=="") ? $trenner="?" : $trenner="&amp;";
			$this->tbl_data.=sprintf("  <td%s><a href=\"%s%sid=%s\">%s</a></td>\n",
                        $class?" class=\"$class\"":"",
                        $this->url_delete,
                        $trenner,
                        $data[$this->indexcol],
                        $this->dict[$this->lang]["delete"]);
		}
                if(!empty($this->url_view)){
                        (strstr($this->url_view ,"?")=="") ? $trenner="?" : $trenner="&amp;";
			$this->tbl_data.=sprintf("  <td%s><a href=\"%s%sid=%s\">%s</a></td>\n",
                        $class?" class=\"$class\"":"",
                        $this->url_view,
                        $trenner,
                        $data[$this->indexcol],
                        $this->dict[$this->lang]["view"]);
		}
  	}
}

?>