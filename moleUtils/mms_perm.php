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
// $Id: mms_perm.php,v 1.2 2002/09/03 16:07:49 moleman Exp $

class perm{


    /** @var    string  $tablename  the table containig the permissions per user
    */
    var $tablename = "phplib_auth_user";

    /** @var    array   $permissions    the list of possible permissions
    */
    var $permissions = array(
        "user"       => 1,
        "customer"   => 3,
        "editor"     => 7,
        "supervisor" => 15,
        "admin"      => 31
    );
    
    /** @var    array   $userperms  the userpermissions
    */
    var $userperms = array ();


    
    /** Constructor
    * Initializes the userperms
    * @param    object auth $a  the authorized user
    * @param    string  $tablename  the table containig the permissions per user
    * @author   tfranz  <tfranz@moleman.de>
    */
    function perm($a,$tablename){
        $this->tablename=$tablename;
        $db = DB::connect(DSN);
        if (DB::isError($db)) {
            die ($db->getMessage());
        }
        $perms = $db->getOne("SELECT perms FROM $this->tablename WHERE username='".$a->getUsername()."'");
        $db->disconnect();
        $this->userperms=$perms;

    } // end func perm


    /** checks, if the user has the required permission
    * @param    string p the required permission
    * @return   bool
    * @author   tfranz <tfranz@moleman.de>
    */
    function havePerm($p)
    {
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
    } // end func havePerm


    /** checks if the user has the required permission
    * @deprecated
    * @author   tfranz <tfranz@moleman.de>
    */
    function have_perm($p)
    {
        return $this->haveperm($p);
    } // end func have_perm



    /** Logically or's all the rights and returns a pair (valid, or_result).
    * If valid is true, an or_result is provided. If valid is false, the
    * or_result is undefined and one or more of the rights do not exist at all.
    * This is a severe error and the application should be halted at once.
    * @param    array   $p  the current user's permissions
    * @return   or_result
    */
    function permsum($p)
    {
       // global $auth;

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
    } // end func permsum



    /** checks if a permission occurs in the perm-array
    * @param    array   $perms  the permission array to search in
    * @param    string  $look_for   the permission to find
    * @return   boolean
    */
    function permIsListed($perms, $look_for)
    {
        $permlist = explode( ",", $perms );
        while( list($a,$b) = each($permlist) ) {
          if( $look_for == $b ) { return true; };
        };
        return false;
    } // end func permIsListed


    
    /**checks if a permission occurs in the perm-array
    * @deprecated
    * @see    perm::permIsListed
    */
    function perm_islisted($perms, $look_for)
    {
        return $this->permIsListed($perms,$look_for);
    } // end func perm_islisted



    /**Return a complete <select> tag for permission selection.
    */
    function perm_sel($name, $current = "", $class = "")
    {
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
    } // end func perm_sel


    /**Called in case of an access violation.
    * Dummy method must be overriden by ubser
    * @param  string  $does_have  a string listing the rights the user actually has.
    * @param  string  $must_have  the rights the page requires.
    * @author tfranz  <tfranz@moleman.de>
    */
    function perm_invalid($does_have, $must_have)
    {
        printf("Access denied.\n");
    }

    
}

?>