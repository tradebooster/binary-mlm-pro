<?php

function mlm_my_payout_details_page($id = '') {
    global $table_prefix;
    global $wpdb;
    global $date_format;
    $url = plugins_url();

    if ($id == '')
        $detailArr = my_payout_details_function();
    else
        $detailArr = my_payout_details_function($id);



    if (count($detailArr) > 0) {

        $memberId = $detailArr['memberId'];
        $payoutId = $detailArr['payoutId'];
        $comissionArr = getCommissionByPayoutId($memberId, $payoutId);
        $dirRefComm = getDirectReferralCommissionByPayoutId($memberId, $payoutId);
        $bonusArr = getBonusByPayoutId($memberId, $payoutId);
        $statusArray = getWithDrawal($memberId, $payoutId);
        $mlm_settings = get_option('wp_mlm_general_settings');
        $mlm_payouts = get_option('wp_mlm_payout_settings');
        $capLimitAmt = $mlm_payouts['cap_limit_amount'];
        $total_amt = $detailArr['total_amt'];
        $capped_amt = $detailArr['capped_amt'];
        $cap_limit = $detailArr['cap_limit'];
        ?>
        <!--<script src="initiate.js" type="text/javascript"></script>-->
        <table width="100%" border="0" cellspacing="10" cellpadding="1">
            <tr>
                <td colspan="2"><input type="button" value="Back" onclick="window.history.back()" class="primary"</td>
            </tr>
            <tr>
                <td width="40%" valign="top">
                    <table width="100%" border="0" cellspacing="10" cellpadding="1">
                        <tr>
                            <td colspan="2"><strong> <?php  _e('Personal Information', 'binary-mlm-pro'); ?></strong></td>
                        </tr>
                        <tr>
                            <td scope="row"><?php _e('Title', 'binary-mlm-pro'); ?></td>
                            <td> <?php  _e('Details', 'binary-mlm-pro'); ?></td>
                        </tr>
                        <tr>
                            <td scope="row"><?php _e('Name', 'binary-mlm-pro'); ?></td>
                            <td><?php echo $detailArr['name'] ?></td>
                        </tr>
                        <tr>
                            <td scope="row"><?php _e('ID', 'binary-mlm-pro'); ?></td>
                            <td><?php echo $detailArr['userKey'] ?></td>
                        </tr>
                        <tr>
                            <td scope="row"><?php _e('Payout ID', 'binary-mlm-pro'); ?></td>
                            <td><?php echo $detailArr['payoutId'] ?></td>
                        </tr>
                        <tr>
                            <td scope="row"><?php _e('Date', 'binary-mlm-pro'); ?></td>
                            <td><?php echo $detailArr['payoutDate'] ?></td>
                        </tr>
                    </table>
                </td>
                <td width="40%">
                    <table width="100%" border="0" cellspacing="10" cellpadding="1">
                        <tr>
                            <td><strong><?php _e('Payout Details', 'binary-mlm-pro'); ?></strong></td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" border="0" cellspacing="10" cellpadding="1">
                                    <tr>
                                        <td colspan="2"><strong><?php _e('Commission', 'binary-mlm-pro'); ?></strong></td>
                                    </tr>

                                    <tr>
                                        <td><?php _e('User Name', 'binary-mlm-pro'); ?></td>
                                        <td><?php _e('Amount', 'binary-mlm-pro'); ?></td>
                                    </tr>
                                    <?php foreach ($comissionArr as $comm) : ?>

                                        <tr>
                                            <td><?php echo $comm['child_ids'] ?></td>
                                            <td><?php echo $mlm_settings['currency'] . ' ' . $comm['amount'] ?></td>
                                        </tr>

                                    <?php endforeach; ?>


                                </table>
                                <?php if (count($dirRefComm) > 0) : ?>
                                    <table width="100%" border="0" cellspacing="10" cellpadding="1">
                                        <tr>
                                            <td colspan="2"><strong><?php _e('Direct Referral Commission', 'binary-mlm-pro'); ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><?php _e('User Name', 'binary-mlm-pro'); ?></td>
                                            <td><?php _e('Amount', 'binary-mlm-pro'); ?></td>
                                        </tr>
                                        <?php foreach ($dirRefComm as $comm) : ?>

                                            <tr>
                                                <td><?php echo $comm['username'] ?></td>
                                                <td><?php echo $mlm_settings['currency'] . ' ' . $comm['amount'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if (count($bonusArr) > 0) : ?>
                            <tr>
                                <td>
                                    <table width="100%" border="0" cellspacing="10" cellpadding="1">
                                        <tr>
                                            <td colspan="2"><strong><?php _e('Bonus', 'binary-mlm-pro'); ?></strong></td>
                                        </tr>
                                        <?php foreach ($bonusArr as $bonus) : ?>
                                            <tr>
                                                <td><?php echo $bonus['bonusDate'] ?> </td>
                                                <td><?php echo $mlm_settings['currency'] . ' ' . $bonus['amount'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>

                                    </table>
                                </td>
                            </tr>
                        <?php endif; ?>

                    </table>
                </td>
            </tr>
        </table>


        <table width="100%" border="0" cellspacing="10" cellpadding="1" class="payout-summary">
            <tr>
                <td colspan="2"><strong><?php _e('Payout Summary', 'binary-mlm-pro'); ?></strong></td>
            </tr>
            <tr>
                <td width="50%"><?php _e('Commission Amount', 'binary-mlm-pro'); ?></td>
                <td width="50%" class="right"><?php echo $mlm_settings['currency'] . ' ' . $detailArr['commamount'] ?></td>
            </tr>
            <?php if ($detailArr['dirrefommamount'] != 0.00) {
                ?>
                <tr>
                    <td width="50%"><?php _e('Direct Referral Commission Amount', 'binary-mlm-pro'); ?></td>
                    <td width="50%" class="right"><?php echo $mlm_settings['currency'] . ' ' . $detailArr['dirrefommamount'] ?></td>
                </tr>
            <?php } ?>
            <tr>
                <td width="50%"><?php _e('Bonus Amount', 'binary-mlm-pro'); ?></td>
                <td width="50%" class="right" ><?php echo $mlm_settings['currency'] . ' ' . $detailArr['bonusamount'] ?></td>
            </tr>
            <tr>
                <td width="50%"><?php _e('Sub-Total', 'binary-mlm-pro'); ?></td>
                <td width="50%" class="right"><?php echo $mlm_settings['currency'] . ' ' . $detailArr['total_amt'] ?></td>
            </tr>
            <?php
            if ($detailArr['total_amt'] >= $detailArr['cap_limit'] && !empty($detailArr['cap_limit'])) {
                if ($detailArr['cap_limit'] == '0.00') {
                    
                }
                else {
                    ?>
                    <tr>
                        <td width="50%"><?php _e('Cap Limt', 'binary-mlm-pro'); ?></td>
                        <td width="50%" class="right"><?php echo $mlm_settings['currency'] . ' ' . $detailArr['cap_limit'] ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            <tr>
                <td width="50%"><?php _e('Service Charge', 'binary-mlm-pro'); ?></td>
                <td width="50%" class="right"><?php echo $mlm_settings['currency'] . ' ' . $detailArr['servicecharges'] ?></td>
            </tr>
            <tr>
                <td width="50%"><?php _e('Tax', 'binary-mlm-pro'); ?>	</td>
                <td width="50%" class="right"><?php echo $mlm_settings['currency'] . ' ' . $detailArr['tax'] ?></td>
            </tr>
            <tr>
                <td width="50%"><strong><?php _e('Net Amount', 'binary-mlm-pro'); ?><?php echo (($detailArr['total_amt'] >= $detailArr['cap_limit'] && !empty($detailArr['cap_limit']))) ? ($detailArr['cap_limit'] == '0.00' ? '' : '(capped)') : ''; ?></strong>	</td>
                <td width="50%" class="right"><strong><?php echo $mlm_settings['currency'] . ' ' . $detailArr['capped_amt'] ?></strong></td>
            </tr>
            <tr>
                <td colspan="2" class="right">
                    <div id="message" style="display:none;color:#0DA443;"><?php
                        _e
                                ('Your Withdrawal Request Initiated', 'binary-mlm-pro');
                        ?></div>
                    <div id="status_message" style="display:none;color:#FF0000;Font-weight:bold;"><?php _e('Withdrawal Pending', 'binary-mlm-pro'); ?></div>
                    <?php if ($statusArray[0]['payment_processed'] == '0' && $statusArray[0]['withdrawal_initiated'] == '1') {
                        ?>
                        <div id="status_message_pending" style="color:#FF0000;Font-weight:bold;"><?php _e('Withdrawal Pending', 'binary-mlm-pro'); ?></div>
                        <?php
                    }
                    else if ($statusArray[0]['payment_processed'] == '1' && $statusArray[0]['withdrawal_initiated'] == '1') {
                        ?>
                        <div id="status_message_processed" style="color:green;Font-weight:bold;"><?php _e('Payment Processed', 'binary-mlm-pro'); ?></div>
                        <a class='view-payment' id='<?php _e($memberId); ?>' href='javascript:void(0);'><?php _e('View Payment Detail', 'binary-mlm-pro'); ?></a>

                        <?php
                    }
                    else if ($statusArray[0]['payment_processed'] == '0' && $statusArray[0]['withdrawal_initiated'] == '0') {
                        ?>

                        <div id="comment_form">
                            <form name="contact" method="post" action="">
                                <fieldset>
                                    <table>
                                        <tr>
                                            <td style="border:none"><label for="name1" id="name_label"><b><?php _e('Comment, if any', 'binary-mlm-pro'); ?>:</b>&nbsp;&nbsp;</label></td>
                                            <td style="border:none"><input type="text" name="name1" id="name1" size="30" value="" class="text-input"/>
                                                <input type="hidden" name="wint_id" id="memberid" value="<?php _e($memberId); ?>"/>
                                                <input type="hidden" name="pay_id" id="payoutid" value="<?php _e($payoutId); ?>"/>
                                                <input type="hidden" name="total_amt" id="total_amt" value="<?php _e($detailArr['capped_amt']) ?>" /></td>
                                        </tr>
                                        <tr><td style="border:none;"></td>
                                            <td style="border:none; text-align: left"><input type="submit" name="submit" class="button" style="margin:0px;float:left" id="submit_btn" value="<?php _e('Initiate withdrawal', 'binary-mlm-pro') ?>" /></td></tr>
                                    </table>



                                </fieldset>
                            </form>
                        </div>

                    <?php } ?>

                </td>
            </tr>
            <div class='show-payment-detail' style="display:none;">
                <tr class='show-payment-detail' style="display:none;"><td colspan='2'><?php _e(paymentDeatil($memberId, $payoutId)); ?></td></tr></div>
        </table>

        <script type="text/javascript">



            jQuery(document).ready(function($) {
                $(".view-payment").click(function() {
                    $(".show-payment-detail").toggle();
                });
            });

            (function($) {
                $(".button").click(function() {
                    var name = $("input#name1").val();
                    var memberid = $('#memberid').val();
                    var payoutid = $('#payoutid').val();
                    var total_amt = $('#total_amt').val();
                    var dataString = 'name=' + name + '&wint_id=' + memberid + '&pay_id=' + payoutid + '&total_amt=' + total_amt;
                    if (confirm("<?php _e('This would initiate a withdrawal for this payout. Would you like to proceed?', 'binary-mlm-pro') ?>")) {
                        $.ajax({
                            type: "POST",
                            url: "<?php _e($url); ?>/binary-mlm-pro/mlm_html/delete_withdrawal.php",
                            data: dataString,
                            success: function() {
                                $('#comment_form').html("<div id='message'></div>");
                                $('#message').html("<h3 class='initiatedmsg'><?php echo __('Your Withdrawal Request Initiated.', 'binary-mlm-pro') ?></h3>")
                                        .hide()
                                        .fadeIn(1500, function() {
                                            $('#message').append("<?php echo __('Thanks for Patience.', 'binary-mlm-pro') ?>");
                                        });
                            }
                        });
                        return false;
                    }
                });
            })(jQuery);
        </script>

        <?php
    }
    else {

        _e("<div class='notfound'>It Seems some error. Please contact adminisistrator " . get_option('admin_email') . " for this issue.</div>");
    }
}

function my_payout_details_function($id = '') {
    if (is_user_logged_in() && isset($_REQUEST['pid'])) {

        global $table_prefix;
        global $wpdb;
        global $current_user;
        get_currentuserinfo();

        if ($id == '')
            $userId = $current_user->ID;
        else
            $userId = $id;

        $sql = "SELECT {$table_prefix}mlm_users.id AS id , {$table_prefix}mlm_users.user_key FROM {$table_prefix}users,{$table_prefix}mlm_users WHERE {$table_prefix}mlm_users.username = {$table_prefix}users.user_login AND {$table_prefix}users.ID = '" . $userId . "'";
        $res = $wpdb->get_results($sql, ARRAY_A);
        if (!empty($res)) {
            $mlm_user_id = $res[0]['id'];
            $mlm_user_key = $res[0]['user_key'];
        }
        else {
            $mlm_user_id = '';
            $mlm_user_key = '';
        }


        $sql = "SELECT 
					id, user_id, DATE_FORMAT(`date`,'%d %b %Y') as payoutDate, payout_id, commission_amount,referral_commission_amount ,
					bonus_amount,total_amt, capped_amt,cap_limit, banktransfer_code, cheque_no, 
					cheque_date, bank_name, user_bank_name, user_bank_account_no, 
					tax, service_charge, dispatch_date, courier_name, awb_no 
				FROM 
					{$table_prefix}mlm_payout 
				WHERE 
					payout_id = '" . $_REQUEST['pid'] . "' AND 
					user_id = '" . $mlm_user_id . "'";

        $row = $wpdb->get_row($sql, ARRAY_A);
        $payoutDetail = array();
        if ($row) {

            $payoutDetail['memberId'] = $mlm_user_id;
            $payoutDetail['name'] = $current_user->user_firstname . ' ' . $current_user->user_lastname;
            $payoutDetail['userKey'] = $mlm_user_key;

            $payoutDetail['payoutId'] = $_REQUEST['pid'];
            $payoutDetail['payoutDate'] = $row['payoutDate'];

            /* Conmmission */
            $payoutDetail['commamount'] = $row['commission_amount'];
            $payoutDetail['dirrefommamount'] = $row['referral_commission_amount'];
            $payoutDetail['bonusamount'] = $row['bonus_amount'];
            $payoutDetail['total_amt'] = $row['total_amt'];
            $payoutDetail['capped_amt'] = $row['capped_amt'];
            $payoutDetail['cap_limit'] = $row['cap_limit'];
            $payoutDetail['servicecharges'] = $row['service_charge'];
            $payoutDetail['tax'] = $row['tax'];
        }

        return $payoutDetail;
    }
    else {

        return null;
    }
}

function getCommissionByPayoutId($memberId, $payoutId) {
    global $table_prefix;
    global $wpdb;
    if (isset($memberId) && isset($payoutId)) {
        $sql = "SELECT 
					id, date_notified, parent_id, child_ids, amount, payout_id 
				FROM 
					{$table_prefix}mlm_commission 
				WHERE 
					parent_id = '" . $memberId . "' AND 
					payout_id = '" . $payoutId . "' 
				";

        $myrows = $wpdb->get_results($sql, ARRAY_A);

        return $myrows;
    }
    else
        return null;
}

function getDirectReferralCommissionByPayoutId($memberId, $payoutId) {
    global $table_prefix;
    global $wpdb;
    if (isset($memberId) && isset($payoutId)) {
        $sql = "SELECT 
					*
				FROM 
					{$table_prefix}mlm_referral_commission LEFT JOIN  {$table_prefix}mlm_users ON {$table_prefix}mlm_referral_commission.child_id = {$table_prefix}mlm_users.id
				WHERE 
					sponsor_id = '" . $memberId . "' AND 
					payout_id = '" . $payoutId . "' 
				";

        $myrows = $wpdb->get_results($sql, ARRAY_A);

        return $myrows;
    }
    else
        return null;
}

function getBonusByPayoutId($memberId, $payoutId) {
    global $table_prefix;
    global $wpdb;
    if (isset($memberId) && isset($payoutId)) {
        $sql = "SELECT 
					 id, DATE_FORMAT(`date_notified`,'%d %b %Y') as bonusDate, mlm_user_id, amount, payout_id 
				FROM 
					{$table_prefix}mlm_bonus 
				WHERE 
					mlm_user_id = '" . $memberId . "' AND 
					payout_id = '" . $payoutId . "' 
				";

        $myrows = $wpdb->get_results($sql, ARRAY_A);

        return $myrows;
    }
    else
        return null;
}

function getWithDrawal($memberId, $payoutId) {
    global $table_prefix;
    global $wpdb;

    if (isset($memberId) && isset($payoutId)) {
        $sql = "SELECT 
					 withdrawal_initiated, payment_processed
				FROM 
					{$table_prefix}mlm_payout
				WHERE 
					user_id = '" . $memberId . "' AND 
					payout_id = '" . $payoutId . "' 
				";

        $myrows = $wpdb->get_results($sql, ARRAY_A);

        return $myrows;
    }
    else
        return null;
}

function paymentDeatil($memberId, $payoutId) {
    global $table_prefix;
    global $wpdb;
    global $date_format;


    if (isset($memberId) && isset($payoutId)) {
        $sql = "SELECT *
				FROM 
					{$table_prefix}mlm_payout
				WHERE 
					user_id = '" . $memberId . "' AND 
					payout_id = '" . $payoutId . "' 
				";

        $myrows = $wpdb->get_results($sql, ARRAY_A);

        $detail = $myrows[0];
        if ($detail['payment_mode'] == 'cheque') {
            ?>

            <table><tr>
                    <th><?php echo __('Withdrawal Date', 'binary-mlm-pro'); ?></th>
                    <th><?php echo __('Payment Date', 'binary-mlm-pro'); ?> </th>
                    <th><?php echo __('Payment Mode', 'binary-mlm-pro'); ?> </th>
                    <th><?php echo __('Payment Detail', 'binary-mlm-pro'); ?></th></tr>
                <tr><td><?php echo $detail['withdrawal_initiated_date'] ?></td>
                    <td><?php echo $detail['payment_processed_date']; ?></td>
                    <td><?php echo $detail['payment_mode'] ?></td>
                    <td><?php echo __('Cheque No', 'binary-mlm-pro'); ?>:&nbsp;<?php echo $detail['cheque_no'] ?><br/>
                        <?php echo __('Cheque Date', 'binary-mlm-pro'); ?>: &nbsp;<?php echo $detail['cheque_date'] ?><br/>
                        <?php echo __('Bank Name', 'binary-mlm-pro'); ?>:&nbsp;<?php echo $detail['bank_name'] ?><br/></td></tr></table>
            <?php
        }
        else
        if ($detail['payment_mode'] == 'bank-transfer') {
            ?>
            <table><tr>
                    <th><?php echo __('Withdrawal Date', 'binary-mlm-pro'); ?></th>
                    <th><?php echo __('Payment Date', 'binary-mlm-pro'); ?> </th>
                    <th><?php echo __('Payment Mode', 'binary-mlm-pro'); ?> </th>
                    <th><?php echo __('Payment Detail', 'binary-mlm-pro'); ?></th></tr>
                <tr><td><?php echo $detail['withdrawal_initiated_date']; ?></td>
                    <td><?php echo $detail['payment_processed_date']; ?></td>
                    <td><?php echo $detail['payment_mode']; ?></td>
                    <td><?php echo __('Benificiary', 'binary-mlm-pro'); ?>:&nbsp;<?php echo $detail['beneficiary']; ?><br/>
                        <?php echo __('Account No', 'binary-mlm-pro'); ?>: &nbsp;<?php echo $detail['user_bank_account_no']; ?><br/>
                        <?php echo __('Banktransfer Code', 'binary-mlm-pro'); ?>: &nbsp;<?php echo $detail['banktransfer_code']; ?><br/>
                        <?php echo __('Bank Name', 'binary-mlm-pro'); ?>:&nbsp;<?php echo $detail['user_bank_name']; ?> <br/></td></tr></table>
            <?php
        }
        else if ($detail['payment_mode'] == 'cash') {
            ?>
            <table><tr>
                    <th><?php echo __('Withdrawal Date', 'binary-mlm-pro'); ?></th>
                    <th><?php echo __('Payment Date', 'binary-mlm-pro'); ?> </th>
                    <th><?php echo __('Payment Mode', 'binary-mlm-pro'); ?> </th></tr>
                <tr><td><?php echo $detail['withdrawal_initiated_date']; ?></td>
                    <td><?php echo $detail['payment_processed_date']; ?></td>
                    <td><?php echo $detail['payment_mode']; ?></td></tr></table>
            <?php
        }
        else
        if ($detail['payment_mode'] == 'other') {
            ?>
            <table><tr>
                    <th><?php echo __('Withdrawal Date', 'binary-mlm-pro'); ?></th>
                    <th><?php echo __('Payment Date', 'binary-mlm-pro'); ?> </th>
                    <th><?php echo __('Payment Mode', 'binary-mlm-pro'); ?> </th>
                    <th><?php echo __('Payment Detail', 'binary-mlm-pro'); ?></th></tr>
                <tr><td><?php echo $detail['withdrawal_initiated_date']; ?></td>
                    <td><?php echo $detail['payment_processed_date']; ?></td>
                    <td><?php echo $detail['payment_mode']; ?></td>
                    <td><?php echo $detail['other_comments']; ?></td></tr></table>
            <?php
        }
    }
}
?>
