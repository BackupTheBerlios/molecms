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
// $Id: mod_compound.php,v 1.1 2002/08/07 09:25:45 moleman Exp $

    require_once 'mms_module.php';

    class compound extends module{

        var $name='compound';
        var $color='maroon';

        function compound(){
            $this->module();
        }

        function getNavArray(){
            $struct[$this->name.'_1']['title']  = $this->lang->translate('browse');
            $struct[$this->name.'_1']['url']    = url(array('module'=>'compound','action'=>'browse'));
            return $struct;
        }


        function _overviewQuery(){
        if(!$_GET['frm_category']){
                $_GET['frm_category']=1;
            }
          $query= "select *,comp.id as mixid, comp.name_de as mischname_de,comp.name_en as mischname_en,cat_comp.name_de as mischkatname_de,cat_comp.name_en as mischkatname_en
        from ". $this->cfg['table']['cat_compound'] .' as cat_comp, '.$this->cfg['table']['compound']." as comp
        where comp.id_mischungkat=cat_comp.id AND comp.id_mischungkat=$_GET[frm_category]";
        return $query;
        }


        function _overviewAssignTemplates(){
            if($this->db_res->numRows()>0){
                include_once 'HTML/Table.php';
                $this->tpl->setCurrentBlock("COMPOUND");
                while($row = $this->db_res->fetchrow(DB_FETCHMODE_OBJECT)){
                    $tbl=new HTML_Table('class="overview"');
                    $tbl->addRow(array($this->lang->translate('name'),'<a class="$this->color" href="'.url(array('module'=>'compound','action'=>'details','id'=>$row->mixid)).'">'.$row->{"mischname_".LC_LANG}."</a>"));
                    $tbl->addRow(array($this->lang->translate('category'),$row->{"mischkatname_".LC_LANG}));
                    $tbl->addRow(array('M100',$row->m100));
                    $tbl->addRow(array('M300',$row->m300));
                    $tbl->addRow(array('TS',$row->ts));
                    $tbl->addRow(array('EAB',$row->eab));
                    $tbl->addRow(array('Rebound',$row->rebound));
                    $tbl->addRow(array('Shore A',$row->shore_a));
                    $tbl->addRow(array('SG',$row->sg));

                    $tbl->setColAttributes(0,'width="100"');
                    $tbl->setColAttributes(1,'width="300"');


                    $row1Style = array ('class'=>'overview');
                    $row2Style = array ('class'=>'overviewalternate');
                    $tbl->altRowAttributes(0,$row1Style,$row2Style);

                    $this->tpl->setVariable("TABLE",$tbl->toHTML());

                    $this->tpl->parseCurrentBlock();
                }
            }

            include_once 'HTML/Select.php';


            $this->tpl->setVariable("FRM_URL", url(array('module'=>'compound','action'=>'browse')));

            $query= "select* from ". $this->cfg['table']['cat_compound'];
            $this->db_res = $this->db_con->Query($query);

            $select=new HTML_Select('frm_category');
            if(!$frm_category){
                $frm_category=1;
            }
            $select->loadDbResult($this->db_res,"name_".LC_LANG,'id');
            $this->tpl->setVariable("SELECT",$select->toHTML());



            $this->tpl->setVariable("LC_SELECT_CATEGORY",$this->lang->translate('select_category'));

            $this->tpl->setvariable("HEADING",$this->lang->translate('compound'));

            $this->tpl->setVariable("CATEGORY_COLOR",$this->color);

        }


        function _detailsQuery($id){
            $query="SELECT* FROM ".$this->cfg['table']['news']." where id_artikel=$id";
            return $query;
        }

        function _detailsAssignTemplates(){
            $this->tpl->setVariable("CATEGORY_COLOR",$this->color);
            $this->tpl->setVariable("DISCLAIMER",$this->lang->translate('news_disclaimer'));
            while($row = $this->db_res->fetchrow(DB_FETCHMODE_OBJECT)){
                $this->tpl->setVariable("HEADING",$row->ueberschrift);
                $this->tpl->setVariable("AUTOR",$row->titel . " " . $row->vorname." ".$row->name);
                $this->tpl->setVariable("TEXT",$this->get_extlink($row->text));
                $this->tpl->setVariable("BACK",url(array('module'=>'news','action'=>'overview')));
                $this->tpl->setVariable("URL",strlen($row->url)>1 ? "<strong>URL: </strong><a href=\"" . $row->url ."\">".$row->url."</a>":" ");
                $this->tpl->setVariable("EMAIL",strlen($row->email)>1 ? "<strong>email: </strong><a href='$row->email)'>$row->email</a>" : "");
                $this->tpl->setVariable("ABSTRACT",strlen($row->abstract)>1? $row->abstract."<br>":"");
                //$this->tpl->setVariable("FIRMA_URL",$sess->url($phpfile["firma_details"]."?id=".$db->f(id_autor)));

                $this->tpl->setVariable("LINK_BACK",$this->lang->translate('link_back'));
                $this->tpl->setVariable("LINK_FIRMENPROFIL",$this->lang->translate('link_companyprofile'));
           }

        }

        function getDetails($id,$tplname='details.tpl.html'){
            $this->tpl= new HTML_Template_IT($basedir);
            $this->tpl->loadTemplateFile($this->basedir.$tplname);

            $query="select * from ".$this->cfg['table']['compound'] ." where id = $id";
            $row=$this->db_con->getRow($query);
            $this->tpl->setVariable("HEADING",$row->{"name_".LC_LANG});

            $this->tpl->setVariable('LC_SPECIFICATIONS',$this->lang->translate('specifications'));

            $query="select *,comp.id as mixid, comp.name_de as mischname_de,comp.name_en as mischname_en,cat_comp.name_de as mischkatname_de,cat_comp.name_en as mischkatname_en
            from ". $this->cfg['table']['cat_compound'] .' as cat_comp, '.$this->cfg['table']['compound']." as comp
            where comp.id_mischungkat=cat_comp.id AND comp.id=$id";

            $row=$this->db_con->getRow($query);

            include_once 'HTML/Table.php';
            $tbl=new HTML_Table('class="overview"');
            $tbl->addRow(array($this->lang->translate('name'),$row->{"mischname_".LC_LANG}));
            $tbl->addRow(array($this->lang->translate('category'),$row->{"mischkatname_".LC_LANG}));
            $tbl->addRow(array('M100',$row->m100));
            $tbl->addRow(array('M300',$row->m300));
            $tbl->addRow(array('TS',$row->ts));
            $tbl->addRow(array('EAB',$row->eab));
            $tbl->addRow(array('Rebound',$row->rebound));
            $tbl->addRow(array('Shore A',$row->shore_a));
            $tbl->addRow(array('SG',$row->sg));

            $tbl->setColAttributes(0,'width="100"');
            $tbl->setColAttributes(1,'width="300"');


            $row1Style = array ('class'=>'overview');
            $row2Style = array ('class'=>'overviewalternate');
            $tbl->altRowAttributes(0,$row1Style,$row2Style);

            $this->tpl->setVariable("COMPOUND_DATA",$tbl->toHTML());

            $tbl=new HTML_Table('class="overview"');
            $tbl->addRow(array('Name','phr'),'class="overview"','TH');

            $query="select *  from ".$this->cfg['table']['details_compound'] ." where id_mischung=$id";
            $this->db_res = $this->db_con->Query($query);
            while($row = $this->db_res->fetchrow(DB_FETCHMODE_OBJECT)){
                if($row->id_produkt){
                    $_url='<a class="maroon" href="'.url(array('module'=>'product','action'=>'details','id'=>$row->id_produkt)).'">'.$row->name.'</a>';
                }
                else{
                    $_url=$row->name ;
                }
                $tbl->addRow(array($_url,$row->phr));
            }



            $query="select sum(phr) as phrsum from ".$this->cfg['table']['details_compound']." where id_mischung=$id";
            $row=$this->db_con->getRow($query);
            $tbl->addRow(array('',$row->phrsum));

            $tbl->updateColAttributes(1,'align="right" "bgcolor=#eeeeee"');
            $tbl->updateRowAttributes($tbl->getrowCount()-1, "bgcolor=#CCCCCC");
            $this->tpl->setVariable('TBL_DETAILS',$tbl->toHTML());

            $this->tpl->setVariable("CATEGORY_COLOR",$this->color);

            return $this->tpl->get();

        }




    }


?>