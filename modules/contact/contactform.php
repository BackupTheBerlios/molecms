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
// $Id: contactform.php,v 1.1 2002/08/07 09:25:44 moleman Exp $

require_once 'HTML_OOH_Form/form.php';
require_once 'HTML_OOH_Form/form_xmlfactory.php';
require_once 'HTML_OOH_Form/form_page_form.php';

function email(&$form) {
  $v = $form->getValue("email");
  if ($v && !preg_match("/@/", $v))
    $form->setValidationError("email", "Your email seems to be wrong. Please correct or remove it.");
}

$x = new form_xmlfactory('modules/contact/contact.xml');
$x->autoloadValues();
$p = new form_page_form(&$x,'Kontakt');


$content["CONTENT"]=$p->get(true);


?>