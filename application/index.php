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
// $Id: index.php,v 1.2 2002/09/03 16:06:58 moleman Exp $

//error_reporting(E_ALL & ~E_NOTICE);
//error_reporting(E_ALL);

define("BENCHMARK",false);
if(BENCHMARK){
    include_once 'Benchmark/Timer.php';
    $timer = new Benchmark_Timer;
    $timer->start();
}

/*
 * Output Compression;
 */
//ob_start("ob_gzhandler");

define("CACHE",false);
define("DEBUG",false);

define ("BANNER_ADS",false);

define('GERMAN',1);
define('ENGLISH',2);

define('TRUE',1);
define('FALSE',0);


define('FORM_FILE_DIR', '../include/pear/');

require_once 'Cache/OutputCompression.php';
define(CACHE_STORAGE_CLASS, 'file');
define(CACHE_DIR, 'cache/');

$cache = new Cache_Output(CACHE_STORAGE_CLASS, array('cache_dir' => CACHE_DIR));
// if (!($content = $cache->start($cache->generateID($REQUEST_URI)))) {

require_once 'PEAR.php';

require_once 'HTML/Template/ITX.php';
require_once 'DB.php';
require_once 'Config.php';

//require_once "admin/tree_helper.php";
require_once 'HTTP.php';
require_once 'DB/Pager.php';

require_once 'Menu_Browser_DB.php';
require_once "mms_template_menu.php";
require_once "mms_nav.php";

if(BANNER_ADS){
    include_once "phpAdsNew/phpadsnew.inc.php";
}

PEAR::setErrorHandling(PEAR_ERROR_CALLBACK,"catchError");


/*
 * user-defined error handler
 * logs errors to a file
 */

function catchError(&$obj)
{
        $str .= "<b>Error code</b>: " . $obj->code . "<br>\n";
        $str .= "<b>Error message</b>: " . $obj->message . "<br>\n";
        $str .= "<b>Debug string</b>: " . $obj->userinfo . "<br>\n\n";

        echo $str;
}


/*
 * reads config file
 */

$blocks=array();
$data= array() ;

$conf = new Config('IniFile') ;
$conf->parseInput('rubbercity.ini') ;

$blocks = $conf -> getBlocks( "/" ) ;

foreach( $blocks as $block ) {
    $cfg[$block] = $conf -> getValues( "/".$block ) ;
}

/*
 * Sets the language according to browser settings
 */

$langSupported=array("de"=>true,"en"=>true);
define("LC_LANG",HTTP::negotiateLanguage($langSupported,'en'));
$langSelected='rc_'.LC_LANG;
include_once $cfg['path']['i18n'].LC_LANG.".php";
$lang = new $langSelected;



$heute                  = date("Ymd",time());    // current date

/*
 * Template
 */

function tpl_translate($args) {
    global $lang;
    return $lang->translate($args[0]);
}

$_tplset='screen';


$tpl = new IntegratedTemplateExtension($cfg['path']['template']);
$tpl->loadTemplateFile($_tplset.'/'.$cfg['template']['base']);
$tpl->setCallbackFunction('tr', 'tpl_translate');
$tpl->performCallback();


/*
 * Opens database connection
 */

$db_con= DB::connect($cfg['database']['dsn']);
//$db_con->setErrorHandling(PEAR_ERROR_CALLBACK, "DBcatchError");

if (DB::isError($db_con)) {
        die ($db_con->getMessage());
}
$db_con->setFetchMode(DB_FETCHMODE_OBJECT);


$menu=new mms_template_menu;

//$th=new treehelper;


if(BANNER_ADS){
    $banner_array=view_raw("468x60");
    $content["BANNER"]   = $banner_array["html"];
}
//$content["MENU_ROWS"] = $menu->get("rows");


class HTML_menu_browser_topnav extends HTML_menu_browser{

    function HTML_menu_browser_topnav($DSN = '', $table = '', $query=""){
        $this->HTML_Menu_browser($DSN, $table, $query);
    }

    function makeURL($eintrag){
        $url=$eintrag['url'];
        return $url;
    }

}



    $browser = new HTML_menu_browser_topnav($cfg['database']['dsn']);
    $browser->setQuery("SELECT * FROM rc_navigation");
    $topMenuEntries=$browser->getMenu();

  //  print_r($topMenuEntries);

    $topMenu = new mms_template_menu($topMenuEntries,'rows',"REQUEST_URI");


/**
  Replaces an URI with the HTML-Clickable equivalent

  @param    string  str The string to be parsed an replaced
  @return   string  str The tag-added string
  @see      make_link

*/

function replace_uri($str) {
    $pattern = '#(^|[^\"=]{1})(http://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm';
    return preg_replace($pattern,"\\1<a href=\"\\2\\3\">\\3</a>\\4",$str);
} // end func replace_uri




/**
  Checks if the URI to translate is prefixed with an internet protocol
  if not, the protocol is added. Calls then replace_uri.

  @see      replace_uri
  @param    string  str The string to be parsed an replaced
  @return   string  str The tag-added string

*/

function make_link($str){
    if(!preg_match("/mailto:|http:/", $str)){
        if(preg_match("/@/", $str)){
            $str="mailto:".$str;
        }
        if(preg_match("/www/", $str)){
        $str="http://".$str;
        }

    }
    return replace_uri($str);
}   // end func make_link


/**
  Generates different types of URLs. Either /index.php?module=foo?id=bar
  or /foo/bar.html which is useful for sites who qant to appear static (needs apache's mod_rewrite)

  @param    array   params  The information about the url
                            (module affected, operation in the module, id)
  @param    string  mode    Whether to generate url using index.php or dirctory-like urls
                            (allowed vaues: plain, rewrite)
*/

function url($params,$mode='plain'){

    //generate url with parameters

    if ($mode=='plain'){
        $url='/index.php?';
        $urlparams='';
        if (is_array($params)){
            foreach($params as $key => $value){
                $urlparams.=$key.'='.$value.'&';
            }
            $urlparams=substr($urlparams,0,-1);
        }
        else{
            $urlparams=$params;
        }
        $url.=$urlparams;
    }

    // generate url suitable for mod_rewrite

    elseif($mode=='rewrite'){
        if($params['module']){
            $module=$params['module'].'/';
        }
        if(array_key_exists('action',$params)&&array_key_exists('id',$params)){
            $action=$params['action'].'/'.$params['id'].'.html';
        }
        else{
            $action=$params['action'].'.html';
        }
        $url='/'.$module.$action;
    }
    return $url;
}    // end func url


//$content["MENU_ROWS"] = toc_toplevel();
$content["MENU_ROWS"]=$topMenu->get('rows');

include_once 'modules/news/mod_op_overview.php';
$news=new newsOverview();
$news->setLimit(5);
$content['RIGHTNAV']=$news->get();
$tpl->setVariable("LC_AKTUELLES",$lang->translate('Aktuelles'));

$nav = new mms_nav($cfg['path']['template']."/".$_tplset.'/leftnav.tpl.html');
$nav->setTitle('Navigation');
$nav->setImage('pic/tastatur.jpg');
//$nav->setMenu(toc_sublevel(2));



switch ($_GET['module']){
    case 'company':
        $_module='company';
        break;
    case 'product':
        $_module='product';
        break;
    case 'news':
        $_module='news';
        break;
    case 'compound':
        $_module='compound';
        break;
    case 'rubbertv':
        $_module='rubbertv';
        break;
    case 'contact':
        $_module='contact';
        break;
    case 'rubbergame':
        $_module='rubbergame';
        break;
    case 'infolinks':
        $_module='infolinks';
        break;
    case 'jobs':
        $_module='jobs';
        break;
    default:
       // $_module='notfound';
       $_module='news';
}


include 'modules/'.$_module.'/'.$_module.$cfg['suffix']['code'];

// append

$tpl->setVariable("LEFTNAV",$nav->get());
$tpl->setvariable($content);


    $tpl->show();
    $cache->endPrint(+1000);
    if(DEBUG){
        echo 'cache miss<br>';
    }
/*}
else {
    $cache->printContent();
    if(DEBUG){
        echo 'cache hit<br>';
    }
}     */

if(BENCHMARK){
    $timer->stop();
    echo '<small>php created this page in '.$timer->timeElapsed().' seconds</small>' ;
}

?>