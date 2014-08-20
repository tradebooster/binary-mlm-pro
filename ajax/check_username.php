<?php

require_once( "../../../../wp-config.php" );

$action = $_REQUEST['action'];
//$table_prefix = mlm_core_get_table_prefix();

global $wpdb, $table_prefix;
if ($action == 'username') {
    $q = $_GET['q'];
    $uname = $wpdb->get_var("SELECT user_login FROM {$table_prefix}users WHERE username = '$q'");
    if ($uname)
        _e("<span class='errormsg'>Sorry! The specified username is not available for registration.</span>", "binary-mlm-pro");
    else
        _e("<span class='msg'>Congratulations! The username is available.</span>", "binary-mlm-pro");
}
else if ($action == 'sponsor') {

    $q = $_GET['q'];
    $sname = $wpdb->get_var("SELECT user_login FROM {$table_prefix}mlm_users WHERE `user_login` = '$q'");

    if (!$sname)
        _e("<span class='errormsg'>Sorry! The specified sponsor is not available for registration.</span>", "binary-mlm-pro");
    //else
    //echo "<span class='msg'>Congratulations! Your sponsor is <b> ".ucwords(strtolower($sname))."</b> .</span>";
}
?>