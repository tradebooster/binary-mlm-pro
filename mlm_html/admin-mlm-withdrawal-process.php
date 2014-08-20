<?php

function mlm_withdrawal_process() {
    /**
     * Detect plugin. For use in Admin area only.
     */
    include_once ABSPATH . '/wp-admin/includes/plugin.php';
    if (is_plugin_active('mlm-paypal-mass-pay/load-data.php')) {
        //plugin is activated
        mlm_withdrawal_process_MASS_Active();
    }
    else {
        mlm_withdrawal_process_MASS_Inactive();
    }
}

function mlm_withdrawal_process_MASS_Inactive() {
    global $table_prefix;
    global $wpdb;
    ?>

    <div class='wrap'>
        <div id="icon-users" class="icon32"></div><h1><?php _e('Process Individual User Withdrawal', 'binary-mlm-pro'); ?></h1><br />
        <div class="notibar msginfo" style="margin:10px;">
            <a class="close"></a>
            <p><?php _e('Use the form below to process an individual user withdrawal.', 'binary-mlm-pro'); ?></p>
            <p><strong><?php _e('Cash', 'binary-mlm-pro'); ?></strong> - <?php _e('Simply records a cash payment against the withdrawal with no further details.', 'binary-mlm-pro'); ?></p>
            <p><strong><?php _e('Cheque', 'binary-mlm-pro'); ?></strong> - <?php _e('Specify the Cheque Number, Cheque Date and Bank Name.', 'binary-mlm-pro'); ?></p>
            <p><strong><?php _e('Bank Transfer', 'binary-mlm-pro'); ?></strong> - <?php _e('Specify the Beneficiary Name, Account Number, Bank Name and Bank Transfer Code (optional).', 'binary-mlm-pro'); ?></p>
            <p><strong><?php _e('Other', 'binary-mlm-pro'); ?></strong> - <?php _e('For any other mode of payment. Just specify the payment details in the input box provided.', 'binary-mlm-pro'); ?></p>
        </div>	
    </div>

    <?php
    if (isset($_POST['member_name'])) {
        $mname = $_POST['member_name'];
        $mid = $_POST['member_id'];
        $mpid = $_POST['member_payout_id'];
        $memail = $_POST['member_email'];
        $wamount = $_POST['withdrawal_amount'];

        if (isset($_POST['paydone'])) {
            $payout_id = $_POST['payout_id'];
            $user_id = $_POST['user_id'];


            if (!empty($_POST['cheque_no']))
                $cheque_no = $_POST['cheque_no'];
            else
                $cheque_no = '';
            if (!empty($_POST['cheque_date']))
                $cheque_date = $_POST['cheque_date'];
            else
                $cheque_date = '';
            if (!empty($_POST['cbank_name']))
                $bank_name = $_POST['cbank_name'];
            else
                $bank_name = '';

            if (!empty($_POST['btbank_name']))
                $user_bank_name = $_POST['btbank_name'];
            else
                $user_bank_name = '';
            if (!empty($_POST['btaccount_no']))
                $user_bank_account_no = $_POST['btaccount_no'];
            else
                $user_bank_account_no = '';
            if (!empty($_POST['bt_code']))
                $banktransfer_code = $_POST['bt_code'];
            else
                $banktransfer_code = '';
            if (!empty($_POST['bt_benificiary']))
                $beneficiary = $_POST['bt_benificiary'];
            else
                $beneficiary = '';
            if (!empty($_POST['pmode']))
                $payment_mode = $_POST['pmode'];
            else
                $payment_mode = '';
            if (!empty($_POST['specified']))
                $comment = $_POST['specified'];
            else
                $comment = '';

            $sql = "UPDATE {$table_prefix}mlm_payout SET `payment_mode`='" . $payment_mode . "',`cheque_no`='" . $cheque_no . "',
		`cheque_date`='" . $cheque_date . "',`bank_name`='" . $bank_name . "',`banktransfer_code`='" . $banktransfer_code . "', 
		`user_bank_name`='" . $user_bank_name . "',`user_bank_account_no`='" . $user_bank_account_no . "',
		`beneficiary`='" . $beneficiary . "',`payment_processed`='1',`payment_processed_date`= NOW(), 
		`other_comments`='" . $comment . "' WHERE `payout_id`= '" . $payout_id . "' AND `user_id`= '" . $user_id . "'";

            $res = $wpdb->query($sql);
            if ($res == '1') {
                ?>
                <script>window.location.href = "<?php echo site_url() . '/wp-admin/admin.php?page=admin-mlm-pending-withdrawal' ?>"</script>
                <?php
                if ($u = get_option('process_withdrawal_mail', true) == 1) {
                    WithDrawaiProcessedMail($mid, $payment_mode, $wamount, $payout_id);
                }
            }
        }
    }
    ?>
    <div class='wrap'>
        <form method="POST" action="" id="paydone_form" name="payment_complete">
            <table border="0" cellpadding="5" cellspacing="0">
                <tr>
                    <td><?php _e('Member Id', 'binary-mlm-pro') ?></td>
                    <td><input type="text" name="member_id" id="mid" size="40" value="<?php if (!empty($mid)) _e($mid); ?>" readonly></td>
                </tr>
                <tr>
                    <td><?php _e('Member User Name', 'binary-mlm-pro') ?></td>
                    <td><input type="text" name="member_name" id="mname" size="40" value="<?php if (!empty($mname)) _e($mname); ?>" readonly></td>
                </tr>
                <tr>
                    <td><?php _e('Payout Id', 'binary-mlm-pro') ?></td>
                    <td><input type="text" name="member_payout_id" id="mpid" size="40" value="<?php if (!empty($mpid)) _e($mpid); ?>" readonly></td>
                </tr>
                <tr>
                    <td><?php _e('Member Email', 'binary-mlm-pro') ?></td>
                    <td><input type="text" name="member_email" id="memail" size="40" value="<?php if (!empty($memail)) _e($memail); ?>" readonly></td>
                </tr>
                <tr>
                    <td><?php _e('Amount', 'binary-mlm-pro') ?></td>
                    <td><input type="text" name="withdrawal_amount" id="wamount" size="40" value="<?php if (!empty($wamount)) _e($wamount); ?>" readonly></td>
                </tr>
                <tr>
                    <td><?php _e('Payment Mode', 'binary-mlm-pro') ?></td>
                    <td>
                        <input name="pmode" type="radio" checked="checked" value="cash"><?php _e('Cash', 'binary-mlm-pro') ?><br/>
                        <input name="pmode" type="radio" value="cheque"><?php _e('Cheque', 'binary-mlm-pro') ?><br/>
                        <div class="ptype" id="mode-cheque" name="cheque_info" style="display:none;padding:6px 0px;">
                            <input type="text" name="cheque_no" id="cno" value="" placeholder="Cheque Number" disabled="disabled" required>
                            <input type="date" name="cheque_date" id="cdate" value="" placeholder="Cheque Date" disabled="disabled" required>
                            <input type="text" name="cbank_name" id="cbname" value="" placeholder="Bank Name" disabled="disabled" required>
                        </div>
                        <input name="pmode" type="radio" value="bank-transfer"><?php _e('Bank Transfer', 'binary-mlm-pro') ?><br/>
                        <div class="ptype" id="mode-bank-transfer" name="bank-transfer_info" style="display:none;padding:6px 0px;">
                            <input type="text" name="bt_benificiary" id="btbe" value="" placeholder="Beneficiary Name" disabled="disabled" required>
                            <input type="text" name="btaccount_no" id="btano" value="" placeholder="Account Number" disabled="disabled" required>
                            <input type="text" name="btbank_name" id="btbname" value="" placeholder="Bank Name" disabled="disabled" required>
                            <input type="text" name="bt_code" id="btcode" value="" placeholder="Bank Transfer Code" disabled="disabled">
                        </div>
                        <input name="pmode" type="radio" value="other"><?php _e('Other', 'binary-mlm-pro') ?><br/>
                        <div class="ptype" id="mode-other" name="other" style="display:none;padding:6px 0px;">
                            <input type="text" name="specified" size="30" disabled="disabled" required>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input type='hidden' name='payout_id' value='<?php _e($mpid); ?>'>
                        <input type='hidden' name='user_id' value='<?php _e($mid); ?>'>
                        <input type="submit" name="paydone" id="paydone" value="<?php _e('Process', 'binary-mlm-pro') ?>">
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $("input[name='pmode']").click(function() {
                var method = $(this).val();
                $("div.ptype").hide();
                $("#mode-" + method).show();
                if (method == 'cheque') {
                    $("#mode-" + method + " input").removeAttr('disabled');
                    $("#mode-bank-transfer input").attr('disabled', 'disabled');
                    $("#mode-other input").attr('disabled', 'disabled');
                }
                else if (method == 'bank-transfer') {
                    $("#mode-" + method + " input").removeAttr('disabled');
                    $("#mode-cheque input").attr('disabled', 'disabled');
                    $("#mode-other input").attr('disabled', 'disabled');
                }
                else if (method == 'other') {
                    $("#mode-" + method + " input").removeAttr('disabled');
                    $("#mode-cheque input").attr('disabled', 'disabled');
                    $("#mode-bank-transfer input").attr('disabled', 'disabled');
                }
                else {
                    $("#mode-bank-transfer input").attr('disabled', 'disabled');
                    $("#mode-cheque input").attr('disabled', 'disabled');
                    $("#mode-other input").attr('disabled', 'disabled');
                }

            });
        });
    </script>

<?php } ?>