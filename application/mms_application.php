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
// $Id: mms_application.php,v 1.2 2002/09/03 16:06:58 moleman Exp $


//phpinfo();
require_once 'PEAR.php';
require_once 'Menu_Browser_DB.php';
require_once 'DB.php';
require_once 'DB/Pager.php';

require_once "mms_template_menu.php";
require_once "mms_nav.php";





//error_reporting(E_ALL & ~E_NOTICE);
//error_reporting(E_ALL);

define('GERMAN',1);
define('ENGLISH',2);

define('TRUE',1);
define('FALSE',0);

PEAR::setErrorHandling(PEAR_ERROR_CALLBACK,"catchError");


    /*
     * user-defined error handler
     */

function catchError(&$obj)
{
    $str .= "<b>Error code</b>: " . $obj->code . "<br>\n";
    $str .= "<b>Error message</b>: " . $obj->message . "<br>\n";
    $str .= "<b>Debug string</b>: " . $obj->userinfo . "<br>\n\n";

    echo $str;
} // end func catchError


class HTML_menu_browser_topnav extends HTML_menu_browser{

    function HTML_menu_browser_topnav($DSN = '', $table = '', $query="")
    {
        $this->HTML_Menu_browser($DSN, $table, $query);
    } // end constructor


    function makeURL($eintrag)
    {
        $url=$eintrag['url'];
        return $url;
    }   // end func makeURL

} // end class HTML_menu_browser_topnav


class mms_application extends PEAR{

    /** @var    boolean $executeBenchmark   sets whether the benchmark component should be initialized
    */
    var $executeBenchmark=false;

    /** the benchmark timer
    */
    var $timer;



    /** Constructor
    * Initializes the application parts depending on the supplied options
    * @param    array   $options    The options passed to the application
    * @author   tfranz  <tfranz@moleman.de>
    */
    function mms_application($options)
    {
        $this->PEAR();
        $this->setOptions($options);

        $this->parseConfig();
        define('FORM_FILE_DIR', $this->cfg['form']['dir']);

        if($this->benchmark){
            $this->initBenchmark();
        }
    } // end constructor


    /**
     * reads config file
     */
    function parseConfig()
    {
        require_once 'Config/Config.php';
        $blocks=array();
        $data= array() ;

        $conf = new Config('IniFile');
        $conf->parseInput('rubbercity.ini');

        $blocks = $conf -> getBlocks( "/" );

        foreach( $blocks as $block ) {
            $cfg[$block] = $conf->getValues( "/".$block ) ;
        }

        $this->cfg=$cfg;
    } // end func parseConfig



    /** Initializes the benchmark class
    * @author   tfranz  <tfranz@moleman.de>
    */
    function initBenchmark()
    {
        include_once 'Benchmark/Timer.php';
        $this->timer = new Benchmark_Timer;
        $this->timer->start();
    } // end func initBenchmark


    /** sets the benchmark enabled or disabled
    * @param    boolean $enabled    enabled true or false
    * @return   boolean the value to wich the benchmark is set
    */
    function setBenchmark($enabled=true)
    {
        $this->executeBenchmark=$enabled;
        return $enabled;
    } // end func setBenchmark


    /** returns whether the benchmark should be executed
    @return boolean
    */
    function getBenchmark()
    {
        return $this->benchmark;
    } // end func getbenchmark



    
    /** initializes the pear userland cache
    */
    function initCache()
    {
        include_once 'Cache/OutputCompression.php';
        $this->cache = new Cache_Output($this->cfg['cache']['method'], array('cache_dir' => $this->cfg['cache']['directory']));
    } // end func InitCache




    /*
     * Sets the translator language according to browser settings
     */
    function initTranslator()
    {
        $langSupported=array("de"=>true,"en"=>true);
        include_once 'HTTP.php';
        define("LC_LANG",HTTP::negotiateLanguage($langSupported,$this->cfg['i18n']['default']));
        $langSelected='rc_'.LC_LANG;
        include_once $this->cfg['i18n']['directory'].LC_LANG.".php";
        $this->lang = new $langSelected;
    } // end func initTranslator



    /**
     * Template translator
     */

    function tpl_translate($args)
    {
        global $lang;
        return $lang->translate($args[0]);
    }



    /** initializes the template class
    */
    function initTemplate()
    {
        include_once 'HTML/Template/ITX.php';
        $tpl = new IntegratedTemplateExtension($this->cfg['path']['template']);
        $tpl->loadTemplateFile($_tplset.'/'.$this->cfg['template']['base']);
        $tpl->setCallbackFunction('tr', 'tpl_translate');
        $tpl->performCallback();
    } // end func initTemplate
    

    /** Opens database connection
     */
    function initDB()
    {
        $this->dbCon= DB::connect($this->cfg['database']['dsn']);
        //$db_con->setErrorHandling(PEAR_ERROR_CALLBACK, "DBcatchError");
    
        if (DB::isError($this->dbCon)) {
                die ($this->dbCon->getMessage());
        }
        $this->dbCon->setFetchMode(DB_FETCHMODE_OBJECT);
    } // end func initDB
    


    
    /** Initializes the adserver Module
    */
    function initAdvertising()
    {
        include_once "phpAdsNew/phpadsnew.inc.php";
        $banner_array=view_raw("468x60");
        $content["BANNER"]   = $banner_array["html"];
    } // end func initAdvertising



    /** initializes the navigation
    */
    function initMenu()
    {
        $browser = new HTML_menu_browser_topnav($this->cfg['database']['dsn']);
        $browser->setQuery("SELECT * FROM rc_navigation");
        $topMenuEntries=$browser->getMenu();

        $this->topMenu = new mms_template_menu($topMenuEntries,'rows',"REQUEST_URI");

        $this->content["MENU_ROWS"]=$this->topMenu->get('rows');

    } // end func initMenu






    /** determines the module sufficient to handle the current task
    * @return   string  module
    * @author   tfranz  <tfranz@moleman.de>
    */
    function getSelectedModule()
    {
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

        return $_module;

    } // end func getSelectedModule


    
    /** runs a module
    * @param    string  $module
    * @author   tfranz  <tfranz@moleman.de>
    */
    function runModule($module)
    {
        include $this->cfg['module']['directory'].$module.$this->cfg['suffix']['code'];
    } // end func runModule

    
    /** runs the appropriate module
    * @see  getSelectedModule
    * @author   tfranz  <tfranz@moleman.de>
    */
    function runSelectedModule()
    {
        $this->runModule($this->getSelectedModule);
    } // end funcrunSelectedModule


    
    /** shows the output page
    */
    function show()
    {
        $this->tpl->setVariable("LEFTNAV",$nav->get());
        $this->tpl->setVariable($content);


        $this->tpl->show();
        if($this->cache){
            $this->cache->printContent();
        } else {
            $this->cache->endPrint(+1000);
        }

    } // end func show



    
    /** Destructor
    */
    function _mms_application(){
        if($this->benchmark){
            $this->benchmark->stop();
            echo '<small>php created this page in '.$timer->timeElapsed().' seconds</small>' ;
        }
    }
}
?>