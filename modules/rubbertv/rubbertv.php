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
// $Id: rubbertv.php,v 1.1 2002/08/07 09:25:45 moleman Exp $

define('TPL_RUBBERTV','modules/'.$module.'/rubbertv.tpl.html');
$tpl2=new IntegratedTemplateExtension();
$tpl2->loadTemplateFile(TPL_RUBBERTV);
$tpl2->setVariable("CATEGORY_COLOR","turquoise");
$tpl2->setvariable("HEADING",$lang->translate('rubbertv'));

$table['video']=$cfg['table']['video'];
$table['video_details']=$cfg['table']['video_details'];

$query="select * from $table[video],$table[video_details]
where $table[video].id=$table[video_details].id_video order by id_video";
$db_res = $db_con->Query($query);


$oldid=0;
while($row = $db_res->fetchrow(DB_FETCHMODE_OBJECT)){
    if($row->id_video!=$oldid){
        $tpl2->parse("VIDEO");
        $oldid=$row->id_video;
    }
    $tpl2->setvariable("NAME",$row->name);
    $tpl2->setvariable("DESCRIPTION",$row->beschreibung);
    $tpl2->setcurrentblock("DETAILS");
    $tpl2->setvariable("FORMAT",$row->format);
    $tpl2->setvariable("LC_PLAYERS",$lang->translate('mediaplayer'));
    $tpl2->setvariable("URL","<a class=\"pink\" href=\"".$row->url."\">".$row->bandbreite."</a>");
    $tpl2->parseCurrentBlock();
}
$tpl2->parse("VIDEO");
$content["CONTENT"]=$tpl2->get();


$tpl->setVariable("CATEGORY_COLOR","turquoise");
$nav->setimage('/modules/rubbertv/tv.gif');
$nav->setExtra("<a class=\"leftnav\" href=\"http://www.real.com\"><img src=\"/pic/pfeilchen_orange.gif\" border=\"0\" alt=\"\">&nbsp;Real Player</a><br><a class=\"leftnav\" href=\"http://microsoft.com\"><img src=\"/pic/pfeilchen_orange.gif\" border=\"0\" alt=\"\">&nbsp;Windows Media Player</a>");


?>