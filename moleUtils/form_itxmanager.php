<?php

require_once 'HTML_OOH_Form/layoutmanager/form_itmanager.php';

class itxmanager extends itmanager{

   /**
  * Sets the template.
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

  }

?>
