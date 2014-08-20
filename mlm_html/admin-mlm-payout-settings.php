<?php

function mlmPayout() {
    //get database table prefix
    $table_prefix = mlm_core_get_table_prefix();

    $error = '';
    $chk = 'error';
    //most outer if condition 
    if (isset($_POST['mlm_payout_settings'])) {
        $pair1 = sanitize_text_field($_POST['pair1']);
        $pair2 = sanitize_text_field($_POST['pair2']);
        $initial_pair = sanitize_text_field($_POST['initial_pair']);
        $initial_amount = sanitize_text_field($_POST['initial_amount']);
        $further_amount = sanitize_text_field($_POST['further_amount']);

        if (checkPair($pair1, $pair2))
            $error .= '<br/>' . __("\n Your pair ratio is wrong.", "binary-mlm-pro");

        if (checkInputField($initial_pair))
            $error .= '<br/>' . __("\n Your initial pair value is wrong.", "binary-mlm-pro");

        if (checkInputField($initial_amount))
            $error .= '<br/>' . __("\n Your initial amount value is wrong.", "binary-mlm-pro");

        if (checkInitial($further_amount))
            $error .= '<br/>' . __("\n Your further amount value is wrong.", "binary-mlm-pro");

        //if any error occoured
        if (!empty($error))
            $error = nl2br($error);
        else {
            $chk = '';
            update_option('wp_mlm_payout_settings', $_POST);
            $url = get_bloginfo('url') . "/wp-admin/admin.php?page=admin-settings&tab=bonus";
            _e("<script>window.location='$url'</script>");
            $msg = __("<span style='color:green;'>Your payout settings has been successfully updated.</span>", "binary-mlm-pro");
        }
    }// end outer if condition
    if ($chk != '') {
        $mlm_settings = get_option('wp_mlm_payout_settings');
        include 'js-validation-file.html';
        ?>


        <div class='wrap1'>
            <h2><?php _e('Payout Settings', 'binary-mlm-pro'); ?>  </h2>
            <div class="notibar msginfo">
                <a class="close"></a>
                <p><?php _e('Use this screen to define the basic parameters of your pay plan.', 'binary-mlm-pro'); ?></p>
                <p><strong><?php _e('Pair', 'binary-mlm-pro'); ?> - </strong><?php _e('How many paid members in the left and right leg individually will make 1 pair for calculating commissions.', 'binary-mlm-pro'); ?></p>
                <p><strong><?php _e('Initial Pairs', 'binary-mlm-pro'); ?></strong> - <?php _e('To incentivise members in the initial stages the amount paid for initial pairs is slightly higher than the regular payout amount.', 'binary-mlm-pro'); ?>
                    <?php _e('Specify the number of initial pairs for which you would like to pay a higher payout amount.', 'binary-mlm-pro'); ?></p>
                <p><strong><?php _e('Initial Pair Amount', 'binary-mlm-pro'); ?> - </strong><?php _e('This is the per pair amount that is paid for the each Initial Pair.', 'binary-mlm-pro'); ?></p>
                <p><strong><?php _e('Further Pair Amount', 'binary-mlm-pro'); ?> - </strong> <?php _e('This is the payout amount for every Pair after the Initial Pairs.', 'binary-mlm-pro'); ?></p>
                <p><strong><?php _e('Direct Referral Commission', 'binary-mlm-pro'); ?> - </strong><?php _e('This is the amount paid to a sponsor for sponsoring a new member in the network. This amount is paid for an infinite number of referrals.', 'binary-mlm-pro'); ?></p>
                <p><strong><?php _e('Service Charges', 'binary-mlm-pro'); ?> - </strong> <?php _e('An amount that is deducted from each Payout paid to the member as a fixed Service Charge. eg. $2 as processing fee for each payout.', 'binary-mlm-pro'); ?></p>
                <p><strong><?php _e('Tax Deduction', 'binary-mlm-pro'); ?> - </strong><?php _e('Some countries have a legislation of deducting Income Tax at source while making commission payments to your members.', 'binary-mlm-pro'); ?></p>
                <p><strong><?php _e('Cap Limit', 'binary-mlm-pro'); ?> - </strong><?php _e('Maximum amount that can be paid to a member in one payout cycle. Anything above the cap limit will be flushed out.', 'binary-mlm-pro'); ?></p>
            </div>
            <?php if ($error) : ?>
                <div class="notibar msgerror">
                    <a class="close"></a>
                    <p> <strong><?php _e('Please Correct the following Error', 'binary-mlm-pro'); ?> :</strong> <?php echo $error; ?></p>
                </div>
                <?php
            endif;

            $pair1 = (isset($_POST['pair1']) ? $_POST['pair1'] : (isset($mlm_settings['pair1']) ? $mlm_settings['pair1'] : ''));
            $pair2 = (isset($_POST['pair2']) ? $_POST['pair2'] : (isset($mlm_settings['pair2']) ? $mlm_settings['pair2'] : ''));
            $initial_pair = (isset($_POST['initial_pair']) ? $_POST['initial_pair'] : (isset($mlm_settings['initial_pair']) ? $mlm_settings['initial_pair'] : ''));
            $initial_amount = (isset($_POST['initial_amount']) ? $_POST['initial_amount'] : (isset($mlm_settings['initial_amount']) ? $mlm_settings['initial_amount'] : ''));
            $init_pair_comm_type = (isset($_POST['init_pair_comm_type']) ? $_POST['init_pair_comm_type'] : (isset($mlm_settings['init_pair_comm_type']) ? $mlm_settings['init_pair_comm_type'] : ''));
            $further_amount = (isset($_POST['further_amount']) ? $_POST['further_amount'] : (isset($mlm_settings['further_amount']) ? $mlm_settings['further_amount'] : ''));
            $furt_amou_comm_type = (isset($_POST['furt_amou_comm_type']) ? $_POST['furt_amou_comm_type'] : (isset($mlm_settings['furt_amou_comm_type']) ? $mlm_settings['furt_amou_comm_type'] : ''));
            $referral_commission_amount = (isset($_POST['referral_commission_amount']) ? $_POST['referral_commission_amount'] : (isset($mlm_settings['referral_commission_amount']) ? $mlm_settings['referral_commission_amount'] : ''));
            $dir_ref_comm_type = (isset($_POST['dir_ref_comm_type']) ? $_POST['dir_ref_comm_type'] : (isset($mlm_settings['dir_ref_comm_type']) ? $mlm_settings['dir_ref_comm_type'] : ''));
            $service_charge = (isset($_POST['service_charge']) ? $_POST['service_charge'] : (isset($mlm_settings['service_charge']) ? $mlm_settings['service_charge'] : ''));
            $service_charge_type = (isset($_POST['service_charge_type']) ? $_POST['service_charge_type'] : (isset($mlm_settings['service_charge_type']) ? $mlm_settings['service_charge_type'] : ''));
            $tds = (isset($_POST['tds']) ? $_POST['tds'] : (isset($mlm_settings['tds']) ? $mlm_settings['tds'] : ''));
            $cap_limit_amount = (isset($_POST['cap_limit_amount']) ? $_POST['cap_limit_amount'] : (isset($mlm_settings['cap_limit_amount']) ? $mlm_settings['cap_limit_amount'] : ''));
            ?>
            <form name="admin_payout_settings" method="post" action="">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
                    <tr>
                        <th scope="row" class="admin-settings">
                            <a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('admin-mlm-pair');"><?php _e('Pair', 'binary-mlm-pro'); ?> <span style="color:red;">*</span>: </a>
                        </th>
                        <td>
                            <input type="text" name="pair1" id="pair1" size="2" value="<?php echo $pair1 ?>"> : 
                            <input type="text" name="pair2" id="pair2" size="2" value="<?php echo $pair2 ?>">
                            <div class="toggle-visibility" id="admin-mlm-pair"><?php _e('Please mention here pair ratio.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" class="admin-settings">
                            <a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-initial-pair');">
                                <?php _e('Initial Pairs', 'binary-mlm-pro'); ?> <span style="color:red;">*</span>: </a>
                        </th>
                        <td>
                            <input type="text" name="initial_pair" id="initial_pair" size="2" value="<?php echo $initial_pair ?>">
                            <div class="toggle-visibility" id="admin-mlm-initial-pair"><?php _e('Please mention here initial pair.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" class="admin-settings">
                            <a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-initial-amount');">
                                <?php _e('Initial Pair Amount', 'binary-mlm-pro'); ?> <span style="color:red;">*</span>: </a>
                        </th>
                        <td>
                            <input type="text" name="initial_amount" id="initial_amount" size="10" value="<?php echo $initial_amount ?>">
                            <select name="init_pair_comm_type" id="init_pair_comm_type" required>
                                <option value="">Select type</option>
                                <option value="Fixed" <?php echo $init_pair_comm_type == 'Fixed' ? 'selected="selected"' : '' ?>  selected="selected">Fixed</option>
                                <option value="Percentage" <?php echo $init_pair_comm_type == 'Percentage' ? 'selected="selected"' : '' ?>>Percentage</option>
                            </select>
                            <div class="toggle-visibility" id="admin-mlm-initial-amount"><?php _e('Please mention here initial amount.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" class="admin-settings">
                            <a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-further-amount');">
                                <?php _e('Further Pair Amount', 'binary-mlm-pro'); ?> <span style="color:red;">*</span>: </a>
                        </th>
                        <td>
                            <input type="text" name="further_amount" id="further_amount" size="10" value="<?php echo $further_amount ?>">
                            <select name="furt_amou_comm_type" id="furt_amou_comm_type" required >
                                <option value="">Select type</option>
                                <option value="Fixed" <?php echo $furt_amou_comm_type == 'Fixed' ? 'selected="selected"' : '' ?>  selected="selected" >Fixed</option>
                                <option value="Percentage" <?php echo $furt_amou_comm_type == 'Percentage' ? 'selected="selected"' : '' ?>>Percentage</option>
                            </select>
                            <div class="toggle-visibility" id="admin-mlm-further-amount"><?php _e('Please mention here further pair amount.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="admin-settings">
                            <a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-direct-referal-commission');">
                                <?php _e('Direct Referral Commission', 'binary-mlm-pro'); ?>:</a>
                        </th>
                        <td>
                            <input type="text" name="referral_commission_amount" id="referral_commission_amount" size="10" value="<?php echo $referral_commission_amount ?>">
                            <select name="dir_ref_comm_type" id="dir_ref_comm_type" >
                                <option value="">Select type</option>
                                <option value="Fixed" <?php echo $dir_ref_comm_type == 'Fixed' ? 'selected="selected"' : '' ?> selected="selected">Fixed</option>
                                <option value="Percentage" <?php echo $dir_ref_comm_type == 'Percentage' ? 'selected="selected"' : '' ?>>Percentage</option>
                            </select> 
                            <div class="toggle-visibility" id="admin-mlm-direct-referal-commission"><?php _e('Please specify referral_commission_amount.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="admin-settings">
                            <a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-service-charege');">
                                <?php _e('Service Charge (If any)', 'binary-mlm-pro'); ?> :</a>
                        </th>
                        <td>
                            <input type="text" name="service_charge" id="service_charge" size="10" value="<?php echo $service_charge ?>">
                            <select name="service_charge_type" id="service_charge_type" >
                                <option value="">Select type</option>
                                <option value="Fixed" <?php echo $service_charge_type == 'Fixed' ? 'selected="selected"' : '' ?>>Fixed</option>
                                <option value="Percentage" <?php echo $service_charge_type == 'Percentage' ? 'selected="selected"' : '' ?>>Percentage</option>
                            </select> 
                            <div class="toggle-visibility" id="admin-mlm-service-charege"><?php _e('Please specify service charge.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" class="admin-settings">
                            <a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-tds');">
                                <?php _e('Tax Deduction', 'binary-mlm-pro'); ?> :</a>
                        </th>
                        <td>
                            <input type="text" name="tds" id="tds" size="10" value="<?php echo $tds ?>">&nbsp;%
                            <div class="toggle-visibility" id="admin-mlm-tds"><?php _e('Please specify TDS.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="admin-settings">
                            <a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-cap_limit');">
                                <?php _e('Cap Limit Amount', 'binary-mlm-pro'); ?> :</a>
                        </th>
                        <td>
                            <input type="text" name="cap_limit_amount" id="cap_limit_amount" size="10" value="<?php echo $cap_limit_amount ?>">
                            <div class="toggle-visibility" id="admin-mlm-cap_limit"><?php _e('Please specify Cap Limit Amount.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>        
                </table>
                <p class="submit">
                    <input type="submit" name="mlm_payout_settings" id="mlm_payout_settings" value="<?php _e('Update Options', 'binary-mlm-pro'); ?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
                </p>
            </form>
            <script language="JavaScript">
                populateArrays();
            </script>

        </div>
        <?php
    } // end if statement
    else {
        echo $msg;
    }
}

//end mlmPayout function
?>