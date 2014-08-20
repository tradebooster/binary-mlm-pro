<?php

function mlmePinUpdate() {

    $mlm_settings = get_option('wp_mlm_general_settings');

    if (isset($mlm_settings['ePin_activate']) && $mlm_settings['ePin_activate'] == '1') {
        global $wpdb;
        global $current_user;
        $user_id = $current_user->ID;
        $table_prefix = mlm_core_get_table_prefix();
        $user = get_userdata($user_id);
        $user_key = $wpdb->get_var("select user_key from {$table_prefix}mlm_users where user_id='{$user->ID}'");
        /* check that it is mlm user or not */
        $res = $wpdb->get_row("SELECT epin_no FROM {$table_prefix}mlm_epins WHERE user_key = '" . $user_key . "'");
        $path = "'" . plugins_url() . "/" . MLM_PLUGIN_NAME . "'";

        if ($wpdb->num_rows > 0) {

            $payment_status = $wpdb->get_var("select payment_status from {$table_prefix}mlm_users where user_id='{$user->ID}'");
            if ($payment_status == '1') {
                echo '<div style="background:#FFCC99;padding:10px;">' . __('Your status is already to set to Paid in the system. You cannot activate your membership again with an ePin.', 'binary-mlm-pro') . '</div>';
            }
            else if ($payment_status == '2') {
                echo '<div style="background:#FFCC99;padding:10px;">' . __('Your status is already set to Active in the system. You cannot activate your membership again with an ePin.', 'binary-mlm-pro') . '</div>';
            }
        }
        else {
            $not_mlm = $wpdb->get_row("select id from {$table_prefix}mlm_users where user_id='{$user->ID}'");
            if ($wpdb->num_rows == '0') {
                echo '<div style="background:#FFCC99;padding:10px;">' . __('Not MLM User', 'binary-mlm-pro') . '</div>';
            }
            else {
                $payment_status = $wpdb->get_var("select payment_status from {$table_prefix}mlm_users where user_id='{$user->ID}'");
                if ($payment_status == '1') {
                    echo '<div style="background:#FFCC99;padding:10px;">' . __('Your status is already to set to Paid in the system. You cannot activate your membership again with an ePin.', 'binary-mlm-pro') . '</div>';
                }
                else if ($payment_status == '2') {
                    echo '<div style="background:#FFCC99;padding:10px;">' . __('Your status is already set to Active in the system. You cannot activate your membership again with an ePin.', 'binary-mlm-pro') . '</div>';
                }
                else {
                    $epin = '<input type="text" name="epin" id="epin_' . $user_id . '"><input type="button" value="Update ePin" id="update_' . $user_id . '" onclick="setePinUser(' . $path . ',' . $user_id . ',document.getElementById(\'epin_' . $user_id . '\').value);"><span id="epinmsg_' . $user_id . '"></span>';
                    echo $epin;
                }
            }
        }
    }
    else {
        echo '<div style="background:#FFCC99;padding:10px;">' . __('Sorry. You are not allowed to access this page due to administrative permissions.', 'binary-mlm-pro') . '</div>';
    }
}
