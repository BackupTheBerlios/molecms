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
// $Id: mod_op_details.php,v 1.1 2002/08/07 09:25:42 moleman Exp $

    require_once 'mms_mod_operation.php';

    class newsDetails extends modOperation
    {
        var $opName='details';
        var $moduleName='news';
        var $id;


        function newsDetails ($id=0)
        {
            $this->modOperation();
            $query="SELECT* FROM ".$this->cfg['table']['news']." where id_artikel=$id";
            $this->setQuery($query);
        }




        function processTemplate()
        {

            $this->tpl->setVariable("CATEGORY_COLOR",$this->color);
            $this->tpl->setVariable("DISCLAIMER",$this->lang->translate('news_disclaimer'));
            foreach ($this->data as $row)
            {
                $this->tpl->setVariable("HEADING",$row['ueberschrift']);
                $this->tpl->setVariable("AUTOR",$row['titel'] . " " . $row['vorname']." ".$row['name']);
                $this->tpl->setVariable("TEXT",$this->get_extlink($row['text']));
                $this->tpl->setVariable("BACK",url(array('module'=>'news','action'=>'overview')));
                $this->tpl->setVariable("URL",strlen($row['url'])>1 ? "<strong>URL: </strong><a href=\"" . $row['url'] ."\">".$row['url']."</a>":" ");
                $this->tpl->setVariable("EMAIL",strlen($row['email'])>1 ? '<strong>email: </strong><a href="$row[email]">$row[email]</a>' : "");
                $this->tpl->setVariable("ABSTRACT",strlen($row['abstract'])>1? $row['abstract']."<br>":"");
                $this->tpl->setVariable("FIRMA_URL",url(array('module'=>'company','action'=>'details','id'=>$row['id_autor'])));

                $this->tpl->setVariable("LINK_BACK",$this->lang->translate('link_back'));
                $this->tpl->setVariable("LINK_FIRMENPROFIL",$this->lang->translate('link_companyprofile'));

            }

        }




    }



?>