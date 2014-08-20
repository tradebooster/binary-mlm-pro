<?php

function wpbmlm_run_pay_cycle() {

    $returnVar = wpbmlm_run_pay_cycle_functions();
    return $returnVar;
}

function wpbmlm_run_pay_cycle_functions() {

    $payoutMasterId = createPayoutMaster();

    global $table_prefix, $wpdb;

    $sql = "SELECT id FROM {$table_prefix}mlm_users WHERE id IN(SELECT parent_id AS id FROM {$table_prefix}mlm_commission WHERE payout_id =0 UNION SELECT sponsor_id AS id FROM {$table_prefix}mlm_referral_commission 
	WHERE payout_id =0 UNION SELECT mlm_user_id AS id FROM {$table_prefix}mlm_bonus WHERE payout_id =0 )";

    $rs = $wpdb->get_results($sql);
    if ($wpdb->num_rows > 0) {
        foreach ($rs as $row) {

            $userId = $row->id;
            $commissionAmt = getCommissionById($userId);
            $directReffComm = getReferralCommissionById($userId);
            $bonusAmt = getBonusAmountById($userId);
            $totalAmt = $commissionAmt + $directReffComm + $bonusAmt;
            $payout_settings = get_option('wp_mlm_payout_settings');
            $tax = $payout_settings['tds'];
            $capLimitAmt = !empty($payout_settings['cap_limit_amount']) ? $payout_settings['cap_limit_amount'] : '';
            $service_charge = $payout_settings['service_charge'];
            if ($totalAmt <= $capLimitAmt) {
                $sub_total = $totalAmt;
            }
            else {
                $sub_total = empty($capLimitAmt) ? $totalAmt : ($capLimitAmt == '0.00' ? $totalAmt : $capLimitAmt);
            }


            if ($payout_settings['service_charge_type'] == 'Fixed')
                $service_charge = $service_charge;
            if ($payout_settings['service_charge_type'] == 'Percentage')
                $service_charge = round(($sub_total) * $service_charge / 100, 2);

            $taxAmt = round(($sub_total) * $tax / 100, 2);
            $netAmt = round($sub_total - $taxAmt - $service_charge);
            $user_info = getUserInfoByMlmUserId($userId);





            /*             * ********************************************************
              INSERT INTO PAYOUT TABLE
             * ********************************************************* */
            $sql_payout = "INSERT INTO 
							{$table_prefix}mlm_payout
							(
								user_id, date, payout_id, commission_amount,referral_commission_amount , 
								bonus_amount,total_amt,capped_amt,cap_limit, tax, service_charge
							) 
							VALUES 					
							(
								'" . $userId . "', '" . current_time('mysql') . "', '" . $payoutMasterId . "', '" . $commissionAmt . "', '" . $directReffComm . "', 
								'" . $bonusAmt . "','" . $totalAmt . "','" . $netAmt . "', '" . $capLimitAmt . "', '" . $taxAmt . "', '" . $service_charge . "'
							)";

            $wpdb->query("UPDATE {$table_prefix}mlm_referral_commission set payout_id='$payoutMasterId' where sponsor_id='$userId' AND payout_id=0");
            $wpdb->query($sql_payout);
            $insert_id = $wpdb->get_var("select id from {$table_prefix}mlm_payout order by id DESC limit 1");
            if ($u = get_option('payout_mail', true) == 1) {
                PayoutGeneratedMail($userId, $netAmt, $payoutMasterId);
            }
            validate_local_lic_key();
            /* Process withdrawal Automatically start */
            $mlm_general_settings = get_option('wp_mlm_general_settings');
            if ($mlm_general_settings['process_withdrawal'] == 'Automatically') {
                $sql = "UPDATE {$table_prefix}mlm_payout SET `withdrawal_initiated`=1,  `withdrawal_initiated_date` = NOW() WHERE `user_id` = '" . $userId . "' AND `id`='" . $insert_id . "'";
                $wpdb->query($sql);
                WithDrawaiInitiatedMail($userId, 'During payout Distribution', $payoutMasterId);
            }
            /* Process withdrawal Automatically End */
            /*             * *********************************************************
              Update Commission table Payout Id
             * ********************************************************* */
            if (isset($insert_id) && $insert_id > 0) {
                $sql_comm = "UPDATE {$table_prefix}mlm_commission 
								SET 
									payout_id= '" . $payoutMasterId . "'
								WHERE 
									parent_id = '" . $userId . "' AND 
									payout_id = '0'
								";
                $wpdb->query($sql_comm);
            }
            /*             * *********************************************************
              Update Bonus table Payout Id
             * ********************************************************* */
            if (isset($insert_id) && $insert_id > 0) {
                $sql_bon = "UPDATE {$table_prefix}mlm_bonus 
								SET 
									payout_id= '" . $payoutMasterId . "'
								WHERE 
									mlm_user_id = '" . $userId . "' AND 
									payout_id = '0'
								";
                $wpdb->query($sql_bon);
            }
        }
    }
    /* return $sql_payout; */
    return "Payout Run Successfully";
}

function createPayoutMaster() {
    global $wpdb, $table_prefix;
    $mlm_payout = get_option('wp_mlm_payout_settings');
    $capLimitAmt = $mlm_payout['cap_limit_amount'];
    $sql = "INSERT INTO {$table_prefix}mlm_payout_master(date,cap_limit) VALUES ('" . current_time('mysql') . "','$capLimitAmt')";
    $wpdb->query($sql);
    $pay_master_id = $wpdb->get_var("select id from {$table_prefix}mlm_payout_master order by id DESC limit 1");
    return $pay_master_id;
}

function getBonusAmountById($userId) {
    global $wpdb, $table_prefix;
    $sql = "SELECT 
				amount, SUM(amount) AS bonus, payout_id 
			FROM 
				{$table_prefix}mlm_bonus 
			WHERE 
				mlm_user_id ='" . $userId . "' and payout_id = 0
			GROUP BY 
				mlm_user_id 			
		";

    $row = $wpdb->get_row($sql, ARRAY_A);

    if ($row) {
        $bonus = $row['bonus'];
    }

    if (!empty($bonus))
        return $bonus;
}

function wpbmlm_run_pay_display_functions() {
    global $table_prefix, $wpdb;

    $sql = "SELECT 	id FROM {$table_prefix}mlm_users 
            WHERE id IN(SELECT parent_id AS id FROM {$table_prefix}mlm_commission 
            WHERE payout_id =0 
            UNION 
            SELECT sponsor_id AS id FROM {$table_prefix}mlm_referral_commission 	
            WHERE payout_id =0 
            UNION 
            SELECT mlm_user_id AS id FROM {$table_prefix}mlm_bonus 
            WHERE payout_id =0 )";

    $rs = $wpdb->get_results($sql);
    if ($wpdb->num_rows > 0) {
        $i = 0;
        foreach ($rs as $row) {
            $userId = $row->id;
            $commissionAmt = getCommissionById($userId);
            $directReffComm = getReferralCommissionById($userId);
            $bonusAmt = getBonusAmountById($userId);
            $totalAmt = $commissionAmt + $directReffComm + $bonusAmt;
            $payout_settings = get_option('wp_mlm_payout_settings');
            $tax = $payout_settings['tds'];
            $service_charge = $payout_settings['service_charge'];
            $capLimitAmt = !empty($payout_settings['cap_limit_amount']) ? $payout_settings['cap_limit_amount'] : '';
            if ($totalAmt <= $capLimitAmt) {
                $total = $totalAmt;
            }
            else {
                $total = empty($capLimitAmt) ? $totalAmt : ($capLimitAmt == '0.00' ? $totalAmt : $capLimitAmt);
            }
            $taxAmt = round(($total) * $tax / 100, 2);
            if ($payout_settings['service_charge_type'] == 'Fixed')
                $service_charge = $service_charge;
            if ($payout_settings['service_charge_type'] == 'Percentage')
                $service_charge = round(($total) * $service_charge / 100, 2);
            $user_info = getUserInfoByMlmUserId($userId);

            $displayDataArray[$i] ['username'] = $user_info->user_login;
            $displayDataArray[$i] ['first_name'] = $user_info->first_name;
            $displayDataArray[$i] ['last_name'] = $user_info->last_name;
            $displayDataArray[$i] ['commission'] = getCommissionById($userId);
            $displayDataArray[$i] ['dirRefcommission'] = $directReffComm;
            $displayDataArray[$i] ['total_amt'] = $totalAmt;
            $displayDataArray[$i] ['cap_limit'] = $capLimitAmt;
            $displayDataArray[$i] ['bonus'] = $bonusAmt;
            $displayDataArray[$i] ['tax'] = $taxAmt;
            $displayDataArray[$i] ['service_charge'] = $service_charge == "" ? 0.00 : $service_charge;
            $displayDataArray[$i] ['net_amount'] = round(($total - $service_charge - $taxAmt));
            $i++;
        }
    }
    else
        $displayDataArray = "None";

    return $displayDataArray;
}

function getUserInfoByMlmUserId($mlmUserId) {
    global $table_prefix, $wpdb;
    $user_id = $wpdb->get_var("SELECT user_id FROM {$table_prefix}mlm_users WHERE id = $mlmUserId");
    $user_info = get_userdata($user_id);
    return $user_info;
}

function getCommissionById($userId) {
    global $table_prefix, $wpdb;
    $sql = "SELECT SUM(amount) AS commission FROM {$table_prefix}mlm_commission WHERE  payout_id=0 AND parent_id=$userId GROUP BY parent_id";
    $commission = $wpdb->get_var($sql);

    return $commission;
}

function getReferralCommissionById($userId) {
    global $table_prefix, $wpdb;
    $sql = "SELECT SUM(amount) AS reff_comm FROM {$table_prefix}mlm_referral_commission WHERE   sponsor_id ='$userId' AND   payout_id=0 GROUP BY sponsor_id";
    $reff_comm = $wpdb->get_var($sql);

    return $reff_comm;
}

?>
