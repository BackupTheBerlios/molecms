<?php
  function check_alnumspace($in) {
    if (eregi("[^a-z����0-9 ]+", $in) {
      return false;
    }
    return $in;
  }

  function check_numeric($in) {
    if (ereg("[^0-9]", $in) {
      return false;
    }
    return $in+0;
  }
?>
