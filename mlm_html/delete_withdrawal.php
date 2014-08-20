<?php

include("../../../../wp-config.php");
global $table_prefix;
global $wpdb;
if (isset($_POST['wdel_id'])) {
    $user_id = $_POST['wdel_id'];
    $pay_id = $_POST['pay_id'];
    $sql = "UPDATE {$table_prefix}mlm_payout SET `withdrawal_initiated` = '0', `withdrawal_initiated_comment` = '', `withdrawal_initiated_date` = '0000-00-00' WHERE `user_id` = '" . $user_id . "' AND `payout_id`='" . $pay_id . "'";
    $wpdb->query($sql);
}

if (isset($_POST['wint_id'])) {
    $memberId = $_POST['wint_id'];
    $pay_id = $_POST['pay_id'];
    $comment = $_POST['name'];
    $amount = $_POST['total_amt'];
    $sql = "UPDATE {$table_prefix}mlm_payout SET `withdrawal_initiated`=1, `withdrawal_initiated_comment` = '" . $comment . "', `withdrawal_initiated_date` = NOW() WHERE `user_id` = '" . $memberId . "' AND `payout_id`='" . $pay_id . "'";
    $wpdb->query($sql);
    if ($u = get_option('withdrawal_mail', true) == 1) {
        WithDrawaiProcessedMail($memberId, $comment, $wamount);
    }
}
?>