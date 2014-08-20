<?php

require_once('../../../wp-config.php');
global $wpdb;
$table_prefix = mlm_core_get_table_prefix();

extract($_REQUEST);
$user_key = getuserkeybyid($user_id);
if (!empty($epin)) {
    $sql = "SELECT * FROM {$table_prefix}mlm_epins WHERE epin_no='" . $epin . "' AND status=1";
    $results = $wpdb->get_results($sql);
    if ($wpdb->num_rows != 1) {
        $sql = "update {$table_prefix}mlm_epins set user_key='{$user_key}', date_used=now(), status=1 where epin_no ='{$epin}' ";
        $epinUpdate = $wpdb->query($sql);
        if (!empty($epinUpdate)) {
            $userUpdate = mlmUserUpdateePin($user_id, $epin);

            $status = $wpdb->get_var("SELECT payment_status 
							FROM {$table_prefix}mlm_users 
							WHERE `user_id` = '" . $user_id . "'");
            if ($status == '1') {
                insert_refferal_commision($user_id);
            }
        }
    }


    if ($epinUpdate && $userUpdate) {
        _e("<span class='error' style='color:green'>Congratulations! Your account is now set to Active.</span>");
    }
    else {
        _e("<span class='error' style='color:red'>Sorry. You have entered an invalid ePin.</span>");
    }
}
?>