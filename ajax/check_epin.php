<?php

require_once( "../../../../wp-config.php" );

global $wpdb, $table_prefix;

$q = $_GET['q'];
$epin = $wpdb->get_var("SELECT epin_no FROM {$table_prefix}mlm_epins WHERE epin_no = '$q' AND status=0");
if ($epin) {
    _e("<span class='msg'>Congratulations! This ePin is available.</span>", "binary-mlm-pro");
}
else {
    _e("<span class='errormsg'>Sorry! This ePin is not Valid or already Used .</span>", "binary-mlm-pro");
}
?>
