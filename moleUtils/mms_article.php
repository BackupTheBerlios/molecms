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
// $Id: mms_article.php,v 1.2 2002/09/03 16:07:49 moleman Exp $

require_once 'PEAR.php';
require_once 'DB.php';

class article extends PEAR{

    var $db;
    var $tbl;

    function article(){
        $this->PEAR();
    }

    function insert($content){
        $id=$this->db->nextId('article');
        $querystring="INSERT INTO $this->tbl (id_artikel) VALUES ($id)";

        $result=$this->db->query($querystring);

        if (DB::isError($result)) {
            die ($result->getMessage());
        }

        $this->update($id,$content);

    }

    function update($id,$content){

        $querystring="
                    UPDATE
                        $this->tbl
                    SET
                        id_autor='$content[autor]',
                        id_lang='$content[lang]',
                        ueberschrift='$content[ueberschrift]',
                        abstract='$content[abstract]',
                        text='$content[artikel]',
                        releasedate='$content[releasedate]',
                        url='$content[url]',
                        email='$content[email]',
                        aktiv='$content[aktiv]',
                        premium='$content[premium]'
                    WHERE id_artikel=$id";

            $result=$this->db->query($querystring);

            if (DB::isError($result)) {
                die ($result->getMessage());
            }
     }

     function fetch($id){
        if (!isset($id)){
            return $this->raiseError("Ohne ausgewählten Artikel kann man nichts ändern!");
        }

        $data=$this->db->getRow("SELECT * FROM $this->tbl WHERE id_artikel = $id");

        if(!isset($data)){
           return $this->raiseError("Dieser Artikel existiert nicht.");
        }
        if($data->id_autor!=$GLOBALS['_autor'] AND !$GLOBALS['perm']->have_perm('admin'))  {
            return $this->raiseError("Sie können Artikel Nr. $data->id_artikel nicht ändern, da er nicht von Ihnen erstellt wurde und sie keine Administratorrechte besitzen.");
        }
        return $data;

     }


}


?>