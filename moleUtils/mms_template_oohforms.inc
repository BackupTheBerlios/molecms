<?php

# version 1.0.1, 2001/04/23 tf

# mms_template_oohforms.inc

#
# Passt die OOHForms-Bibliothek so an, dass nicht
# direkt auf den Bildschirm geschrieben wird, sondern
# ein String zurückgegeben wird, der sich mit der
# Template-Klasse ausgeben lässt.
#

#changes:
#	2001/04/23 changed require to require_once

require_once("oohforms.inc");

/**
 * Class template_form provides a simple interface for uploading files onto the webserver.
 * @access  public
 * @package form helper
*/
class template_form extends form {

	function start($jvs_name="",$method="",$action="",$target="",$form_name="") {
		print $this->get_start($jvs_name,$method,$action,$target,$form_name)."<dl>";
	}

	function finish($after="",$before="") {
		print "</dl>" . $this->get_finish($after, $before);
	}


function get_element($name,$value=false) {
    $str = "";
    if($this->label)
        $str .= "<em>".$this->elements[$name]["label"] . "</em> ";
    $x   = 0;
    $flag_nametranslation = false;

    // see add_element: translate $foo[int] to $foo{int}
#   Original pattern
#   if (preg_match("/(w+)\[(\d+)\]/i", $name, $regs) {
    if (ereg("([a-zA-Z_]+)\[([0-9]+)\]", $name, $regs)) {
       $org_name = $name;
       $name = sprintf("%s{%s}", $regs[1], $regs[2]);
       $flag_nametranslation = true;
    }

    if (!isset($this->elements[$name]))
      return false;

    if (!isset($this->elements[$name]["which"]))
      $this->elements[$name]["which"] = 0;

    $el = $this->elements[$name]["ob"];
    if (true == $falg_nametranslation)
      $el->name = $org_name;

    if (false == $value)
       $value = $el->value;

    if ($this->elements[$name]["label"])
      $str .= $this->elements[$name]["label"];

    if ($this->elements[$name]["frozen"])
      $str .= "<dt>".$el->label ."</dt><dd>" . $el->self_get_frozen($value,$this->elements[$name]["which"]++, $x)."</dd>";
    else
      $str .= "<dt>".$el->label ."</dt><dd>" . $el->self_get($value,$this->elements[$name]["which"]++, $x)."</dd>";
    $this->elements[$name]["ndx_array"][] = $this->n;
    $this->n += $x;

    return $str;
  }
}

?>
