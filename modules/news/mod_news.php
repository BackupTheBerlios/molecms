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
// $Id: mod_news.php,v 1.1 2002/08/07 09:25:42 moleman Exp $

    require_once 'mms_module.php';

    class news extends module{

        var $name='news';
        var $color='red';

        function news(){
            $this->module();
        }

        function getNavArray(){
            $struct[$this->name.'_1']['title']  = $this->lang->translate('toc');
            $struct[$this->name.'_1']['url']    = url(array('module'=>'news','action'=>'overview'));
            $struct[$this->name.'_2']['title']  = $this->lang->translate('search');
            $struct[$this->name.'_2']['url']    = url(array('module'=>'news','action'=>'search'));
            return $struct;
        }


        function _overviewQuery($mode='default'){
            $query= "SELECT
                    tbl_artikel.id_artikel,
                    tbl_artikel.ueberschrift,
                    tbl_artikel.id_lang,
                    tbl_company.name,
                    tbl_artikel.releasedate
                FROM
                    " . $this->cfg['table']['news'] ." as tbl_artikel,".$this->cfg['table']['company'] ." as tbl_company
                WHERE
                    tbl_artikel.releasedate <= CURRENT_DATE
                    AND (tbl_artikel.exdate > CURRENT_DATE OR tbl_artikel.exdate = 0)
                    AND tbl_artikel.aktiv = 1
                    AND tbl_company.id = tbl_artikel.id_autor";
                if ($mode=='premium'){
                    $query.= " AND premium=1 ";
                }
                $query.=" ORDER BY
                    tbl_artikel.releasedate DESC, id_artikel DESC ";
                  //  echo $query;
                return $query;
        }


        function _overviewAssignTemplates($mode='default'){
            //$this->tpl->setvariable("HEADING",$this->lang->translate('heading_news'));
            if($mode=='premium'){
                while($row = $this->db_res->fetchrow(DB_FETCHMODE_OBJECT)){
                    $this->tpl->setCurrentBlock("PREMIUM_ZEILE");
                    $this->tpl->setVariable("CATEGORY_COLOR",$this->color);
                    $this->tpl->setVariable("DATUM",$this->lang->formatDate($row->releasedate));
                    $this->tpl->setVariable("RUBRIKNAME",$row->rubrikname);
                    $this->tpl->setVariable("ABSTRACT",$row->abstract);
                    $this->tpl->setVariable("ARTIKELURL",url(array('module'=>'news','action'=>'article','id'=>$row->id_artikel)));
                    $this->tpl->setVariable("ARTIKELNAME",$row->id_lang==2?"<img src=\"/pic/lang_en.gif\">&nbsp;".$row->ueberschrift:$row->ueberschrift);
                    $this->tpl->parse("PREMIUM_ZEILE");
                }
                $this->tpl->setVariable("PREMIUM_UEBERSCHRIFT",$this->lang->translate('heading_premium'));
                $this->tpl->parse("RUBRIK_PREMIUM");
            }
            elseif($mode=='default'){
                $prevdate=0;
                $this->tpl->setCurrentBlock("RUBRIK_DATUM");
                while($row = $this->db_res->fetchrow(DB_FETCHMODE_OBJECT)){
                    if ($prevdate != $row->releasedate) {
                        $this->tpl->parse("RUBRIK_DATUM");
                        $prevdate=$row->releasedate;
                    }
                    $this->tpl->setVariable("DATUM",$this->lang->formatDate($row->releasedate));
                    $this->tpl->setVariable("CATEGORY_COLOR",$this->color);
                    $this->tpl->setCurrentBlock("RUBRIK_ZEILE");
                    $this->tpl->setVariable("CATEGORY_COLOR",$this->color);
                    $this->tpl->setVariable("RUBRIKNAME",$row->rubrikname);
                    $this->tpl->setVariable("ABSTRACT",$row->abstract);
                    $this->tpl->setVariable("ARTIKELURL",url(array('module'=>'news','action'=>'article','id'=>$row->id_artikel)));
                    $this->tpl->setVariable("ARTIKELNAME",$row->id_lang==ENGLISH?$row->name.":<br>".'<img src="/pic/lang_en.gif">&nbsp;'.$row->ueberschrift:$row->name.":<br>".$row->ueberschrift);
                    $this->tpl->parse("RUBRIK_ZEILE");

                }
                $this->tpl->parse("RUBRIK_DATUM");
            }
            else {echo 'dieser Modus it nicht implementiert';}
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
                $this->tpl->setVariable("FIRMA_URL",url(array('module'=>'company','action'=>'details','id'=>$row->id_autor)));

                $this->tpl->setVariable("LINK_BACK",$this->lang->translate('link_back'));
                $this->tpl->setVariable("LINK_FIRMENPROFIL",$this->lang->translate('link_companyprofile'));
           }
          /*  $query="select* from $tbl_media where id_artikel=$id";
            $db_res = $this->db_con->Query($query);
            $tpl->setCurrentBlock("THUMB");
            while($row = $this->db_res->fetchrow(DB_FETCHMODE_OBJECT)){
                $this->tpl->setVariable("PIC",$row->thumb_uri);
                $this->tpl->setVariable("PIC_URI",$row->uri);
                $this->tpl->setVariable("PIC_COMMENT",$row->kommentar);
                $this->tpl->parseCurrentBlock();
            }  */

        }




    }


?>