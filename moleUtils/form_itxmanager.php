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
// $Id: form_itxmanager.php,v 1.2 2002/09/03 16:07:49 moleman Exp $


require_once 'HTML_OOH_Form/layoutmanager/form_itmanager.php';

/**
* "Layoutmanager" using a template.
*
* Well, this is not a classical layout manager. It's rather a wrapper
* to IT[X] templates without any logics off it's own.
* Enhanced, so it can use the ITX-Callback functions so parts of the template
* can be translated.
* This requires a global function called tr() somewhere in the application.
*
* Encapsulate strings to translate in func_tr("translate me").
*
* @author   tfranz  <tfranz@moleman.de>
* @package  HTML_OOH_Form
*/
class itxmanager extends itmanager{

    /**
    * Sets the template and performs the translation callback.
    *
    * @param  string    template file
    * @param  string    path of the template file
    * @param  boolean   remove unknown/unused variables?
    * @param  boolean   remove empty blocks?
    */
    function setTemplate($tpl, $tpl_root = '', $removeUnknownVariables = true, $removeEmptyBlocks = true) {

        $this->tpl = new IntegratedTemplateExtension();
        if ('' != $tpl_root)
        $this->tpl->setRoot($tpl_root);
    
        $this->tpl->loadTemplatefile($tpl, $removeUnknownVariables, $removeEmptyBlocks);
        $this->tpl->setCallbackFunction('tr', 'tpl_translate');
    
        $this->tpl->performCallback();
    } // end setTemplate

} // end class itxmanager

?>