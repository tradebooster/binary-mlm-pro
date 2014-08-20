<?php

require_once('../../../wp-config.php');
$g_criteria = "";
$g_criteria1 = "";
$g_criteria2 = "";
$g_criteria3 = "";

if (isset($_REQUEST['do'])) {
    $g_criteria1 = trim($_REQUEST['do']);
}

if (isset($_REQUEST['event'])) {
    $g_criteria2 = trim($_REQUEST['event']);
}


switch ($g_criteria1) {

    case "statuschange":
        updatePaymentStatus($_REQUEST['userId'], $_REQUEST['status']);
        insert_refferal_commision($_REQUEST['userId']);
        echo $output = !empty($_REQUEST['status']) ? $_REQUEST['name'] : '';
        break;
}
?>
