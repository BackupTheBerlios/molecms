<?php

require_once 'PEAR.php';
require_once 'DB.php';

class product extends PEAR{

    var $db;
    var $tbl;

    function product(){
        $this->PEAR();
    }

    function insert($content){
        $id=$this->db->nextId('product');
        $querystring="INSERT INTO $this->tbl (id) VALUES ($id)";

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
                        id_produktkat='$content[id_produktkat]',
                        id_hersteller='$content[id_hersteller]',
                        name='$content[name]',
                        info_de='$content[info_de]',
                        info_en='$content[info_en]',
                        bem_de='$content[bem_de]',
                        bem_en='$content[bem_en]',
                        product_url='$content[product_url]',
                        aktiv='$content[aktiv]'
                    WHERE id=$id";

            $result=$this->db->query($querystring);

            if (DB::isError($result)) {
                die ($result->getMessage());
            }
     }

     function fetch($id){
        if (!isset($id)){
            return $this->raiseError("Ohne ausgewählten Artikel kann man nichts ändern!");
        }

        $data=$this->db->getRow("SELECT * FROM $this->tbl WHERE id = $id");

        if(!isset($data)){
           return $this->raiseError("Dieses Produkt existiert nicht.");
        }
        if($data->id_hersteller!=$GLOBALS['_autor'] AND !in_array($data->id,getReselledProducts($GLOBALS['_autor'])) AND !$GLOBALS['perm']->have_perm('admin')){
            return $this->raiseError("Sie können Produkt Nr. $data->id_artikel nicht ändern, da es nicht von Ihnen erstellt wurde und sie keine Administratorrechte besitzen.");
        }
        return $data;

     }


}


?>
