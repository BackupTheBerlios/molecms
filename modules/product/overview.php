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
// $Id: overview.php,v 1.1 2002/08/07 09:25:47 moleman Exp $

include_once 'Menu_Browser_DB.php';

class HTML_menu_browser_company extends HTML_menu_browser{

    function HTML_menu_browser_company($DSN = '', $table = '', $query=""){
        $this->HTML_Menu_browser($DSN, $table, $query);
    }

    function makeURL($eintrag){
        $url=url(array('module'=>'product','action'=>'overview','id'=>$eintrag['id']));
        return $url;
    }

}



    $browser = new HTML_menu_browser_company($cfg['database']['dsn']);
    $browser->setQuery("SELECT *, rc_produktkat.id as id, count(rc_produkt.id) as numchildren
    FROM rc_produktkat LEFT JOIN rc_produkt ON (rc_produkt.id_produktkat=rc_produktkat.id)
    GROUP BY rc_produktkat.id ORDER BY rc_produktkat.name_".LC_LANG);
    $browser->lang=LC_LANG;
    $m=$browser->getMenu();


    $table['product']=$cfg['table']['product'];
    $table['cat_product']=$cfg['table']['cat_product'];

    if(!$id){
        $id=0;
    }

    require_once 'mms_tablepager.php';

    $tabpage=new PageTable($cfg['database']['dsn'],"overview");
    $tabpage->make_url=false;
    $tabpage->fields=array('name');
    $tabpage->col_view = 'name';
    $tabpage->url_view   = url(array('module'=>'product','action'=>'details'));
    $tabpage->add_extra=false;

    $query="select name_".LC_LANG." from $table[cat_product] where id = $id ORDER BY name_".LC_LANG;
    $str_category=$db_con->getOne($query);

    $query="select* from $table[product]
             where id_produktkat=$id order by name asc";


    $prod_menu=new mms_template_Menu($m,'yahoo',"REQUEST_URI");
    $prod_menu->keepEmpty=false;
    $prod_menu->shownumchildren=true;

    $content['CONTENT'].= "<h1>".$str_category."</h1>".$prod_menu->get('urhere').'<br>'.$prod_menu->get('yahoo').'<br><hr>'.$tabpage->get($query,$from,'overview');




?>