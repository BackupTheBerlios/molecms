<?php

class perm{

var $tablename = "phplib_auth_user";

var $permissions = array(
                            "user"       => 1,
                            "customer"     => 3,
                            "editor"     => 7,
                            "supervisor" => 15,
                            "admin"      => 31
                          );

var $userperms = array ();

function perm($a){
    $db = DB::connect(DSN);
    if (DB::isError($db)) {
        die ($db->getMessage());
    }
    $perms = $db->getOne("SELECT perms FROM $this->tablename WHERE username='".$a->getUsername()."'");
    $db->disconnect();
    $this->userperms=$perms;

}


function have_perm($p) {

    $pageperm = split(",", $p);
    $userperm = split(",", $this->userperms);

    list ($ok0, $pagebits) = $this->permsum($pageperm);
    list ($ok1, $userbits) = $this->permsum($userperm);

    $has_all = (($userbits & $pagebits) == $pagebits);
    if (!($has_all && $ok0 && $ok1) ) {
      return false;
    } else {
      return true;
    }
  }


  function permsum($p) {
    global $auth;

    if (!is_array($p)) {
      return array(false, 0);
    }
    $perms = $this->permissions;

    $r = 0;
    reset($p);
    while(list($key, $val) = each($p)) {
      if (!isset($perms[$val])) {
        return array(false, 0);
      }
      $r |= $perms[$val];
    }

    return array(true, $r);
  }

   function perm_islisted($perms, $look_for) {
    $permlist = explode( ",", $perms );
    while( list($a,$b) = each($permlist) ) {
      if( $look_for == $b ) { return true; };
    };
    return false;
  }

    ## Return a complete <select> tag for permission
  ## selection.

  function perm_sel($name, $current = "", $class = "") {
    reset($this->permissions);

    $ret = sprintf("<select multiple name=\"%s[]\"%s>\n",
      $name,
      ($class!="")?" class=$class":"");
    while(list($k, $v) = each($this->permissions)) {
      $ret .= sprintf(" <option%s%s>%s\n",
                $this->perm_islisted($current,$k)?" selected":"",
                ($class!="")?" class=$class":"",
                $k);
    }
    $ret .= "</select>";

    return $ret;
  }

  ##
  ## Dummy Method. Must be overridden by user.
  ##
  function perm_invalid($does_have, $must_have) {
    printf("Access denied.\n");
  }


}

?>
