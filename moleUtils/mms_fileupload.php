<?php

/**
 * Class fileupload provides a simple interface for uploading files onto the webserver.
 * @access  public
 * @package form helper
*/
require_once "db_mysql.inc";
require_once "mms_template_oohforms.inc";
require_once "mms_template_table.inc";

class fileupload{

    // {{{ properties

    /**
     * @var  resource  name of the form class to be used
     */
    var $form_class     = "template_form";
    var $frm;

    var $db_class       = "db_rubbercity";
    var $db_name        = "rubbercity";
    var $db_tablename   = "rc_bild";
    var $db_idname      = "id";
    var $db_artikelidname="id_artikel";
    var $db;

    var $table_class    = "template_table";
    var $tbl;

    var $url_upload = "/home/tfranz/public_html/rubbercity.nil/pic/userpic/";
    var $url_thumb  = "/home/tfranz/public_html/rubbercity.nil/pic/userpic/";
    var $prefix_thumb = "t_";
    var $thumb_size_x   = 80;
    var $id;

    var $lang="de";

    /**
     * @var  array  Hash of language dependent strings for localisation of the user interface
     */
    var $dict = array(
        "de" => array(
            "ctl_choosefile"    => "Durchsuchen",
            "ctl_addfile"       => "Anfügen",
            "ctl_deletefile"    => "Entfernen",
            "lbl_choosefile"    => "Wählen Sie eine <i>JPG</i>-Datei als Anlage aus.",
            "lbl_addfile"       => "Fügen Sie die Datei zur Liste hinzu",
            "lbl_actualfiles"   => "Aktuelle Dateianlagen" ,
            "lbl_comment"       => "Kommentar"
        ),

        "en" => array(
            "ctl_choosefile"    => "browse",
            "ctl_addfile"       => "add",
            "ctl_deletefile"    => "delete",
            "lbl_choosefile"    => "Choose a <i>JPG</i>-file to upload.",
            "lbl_addfile"       => "Add file to list",
            "lbl_actualfiles"   => "current files",
            "lbl_comment"       => "comment"
        )
    );
    // }}}


    // {{{ fileupload
    /**
    * Constructor
    *
    * @access public
    * @return void
    */
    function fileupload($id){
        $this->frm  = new $this->form_class;
        $this->db   = new $this->db_class;
        $this->tbl  = new $this->table_class;
            $this->tbl->check = "id";
            $this->tbl->heading  = true;
        $this->id   = $id;
    }
    // }}}

    function get(){
        $this->get_form();
    }

    function show(){
        print $this->get();
    }

    function set_formdata(){
        $this->frm->add_element(
            array(
                "name"  => "fld_fileupload",
                "type"  => "file",
                "size"  => 500000
            )
        );
        $this->frm->add_element(
            array(
                "name"  => "fld_filecomment",
                "type"  => "text",
                "label" => $this->dict[$this->lang]["lbl_comment"]
            )
        );
        $this->frm->add_element(
            array(
                "type"      => "submit",
                "name"      => "btn_newentry",
                "value"     => $this->dict[$this->lang]["ctl_addfile"]
            )
        );
        $this->frm->add_element(
            array(
                "type"      => "submit",
                "name"      => "btn_deleteentry",
                "value"     => $this->dict[$this->lang]["ctl_deletefile"]
            )
        );
    }

    function get_table(){
        $abfrage="select* from ".$this->db_tablename." where ".$this->db_artikelidname."=".$this->id;
        #echo $abfrage;
        $this->db->query($abfrage);
        $s=$this->tbl->get_result($this->db);
        return $s;
    }

    function show_table(){
        print $this->get_table();
    }

    function set_layout(){
        $s  = "";
        $s  .= "<strong>".$this->dict[$this->lang]['lbl_choosefile']."</strong>";
        $s .= $this->frm->get_element(fld_fileupload);
        $s .= $this->frm->get_element(fld_filecomment);
        $s  .= "<br><strong>".$this->dict[$this->lang]['lbl_addfile']."</strong>";
        $s .= $this->frm->get_element(btn_newentry);
        $s  .= "<br><strong>".$this->dict[$this->lang]['lbl_actualfiles']."</strong>";
        $s .= $this->get_table();
        $s .= $this->frm->get_element(btn_deleteentry);
        return $s;
    }

    // {{{ get_form()
    /**
    * Returns the form code ready to be parsed thru a template
    *
    * @access public
    * @return string
    */
    function get_form(){
        $s   = "";
        $s  .= $this->set_formdata();
        $s  .= $this->frm->get_start("","","","","frm_upload");
        $s  .= $this->set_layout();
        $s  .= $this->frm->get_finish();
        return $s;

    }
    // }}}

    // {{{ show_form()
    /**
    * Prints the form
    *
    * @access public
    * @return void
    */
    function show_form(){
        print $this->get_form();
    }
    // }}}

    function do_thumb($dest=""){
        $src_img = imagecreatefromjpeg($this->url_upload.$dest);
        #$new_w = imagesx($src_img)/2;
       # $new_h = imagesy($src_img)/2;
       $new_w=$this->thumb_size_x;
       $ratio=imagesx($src_img)/imagesy($src_img);
       $new_h=$this->thumb_size_x/$ratio;
        $dst_img = imagecreate($new_w,$new_h);
        imagecopyresized($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img));
        imagejpeg($dst_img,$this->url_thumb.$this->prefix_thumb.$dest );
        return $this->prefix_thumb.$dest;
    }

     function get_query_delete($data) {
     reset($data);
            $query = "delete from $this->db_tablename where ";
            $z=count($data);
            $i=1;
      while(list($k, $v) = each($data)) {
      $i < $z ? $trenner=" OR " : $trenner="";
       $query .= sprintf("$this->db_idname=%s %s",
                $v,
                $trenner
               );
       $i++;

      }
      #echo $query;
      return $query;
    }

    function do_delete($data){
        $abfrage=$this->get_query_delete($data);
        $this->db->query($abfrage);
    }


    // {{{ do_upload
    /**
    * Uploads a file
    *
    * @access public
    * @return boolean errorcode
    */
    function do_upload($inputfile,$destinationName,$thumb=false){
    global $fld_filecomment;
        if ($inputfile <> "none"){
            $dest=$this->url_upload.$destinationName;
            if(copy($inputfile,$dest)){
                $err_code=true;
            }else{
                $err_code=false;
             }
        }else{
            $err_code=false;
        }
        @unlink($inputfile);
         if ($thumb==true){
         $name_thumb=$this->do_thumb($destinationName);
         }
         else{$name_thumb="";}
        $this->db->query("INSERT INTO $this->db_tablename VALUES ('','$this->id','$destinationName','$name_thumb','$fld_filecomment')");

    return $err_code;
    }
    // }}}


}


?>