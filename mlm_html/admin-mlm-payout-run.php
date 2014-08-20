<?php

function adminMLMPayout() {
    $msg = '';
    $displayData = '';
    $payout_settings = get_option('wp_mlm_payout_settings');
    if ($payout_settings['service_charge_type'] == 'Fixed')
        $sct = 'Fixed';
    if ($payout_settings['service_charge_type'] == 'Percentage')
        $sct = '%';
    if (isset($_REQUEST['distribute_commission_bonus'])) {
        $msg .= mlmDistributeCommission();
        $msg .= ' & ';
        $msg .= mlmDistributeBonus();
        $msg .="&nbsp;Distributed Successfull";
    }


    if (isset($_REQUEST['pay_cycle'])) {
        $payoutArr = payoutRun();
    }

    if (isset($_REQUEST['pay_actual_amount'])) {
        $msg = wpbmlm_run_pay_cycle();
    }
    ?>
    <div class='wrap'>
        <div id="icon-users" class="icon32"></div><h1><?php _e('MLM Payout', 'binary-mlm-pro'); ?></h1><br />

        <div class="notibar msginfo">
            <a class="close"></a>
            <p>	<?php _e('Use this screen to run the Payout routine for your network. While testing the plugin use the Distribute Commission and Bonus button below after adding a few members to the network. On a live site the following URL needs to be scheduled (cron job) to run every hour for the commission and bonus routines.', 'binary-mlm-pro'); ?>

            </p>

            <p><?php echo MLM_URL ?>/cronjobs/commission-bonus.php</p>

            <p><?php _e('The commission and bonus routines would simply keep distributing the commission and bonus amounts in the member accounts. They would not show up in their account till the time the Payout Routine is not run. This script can be run manually once every week, every fortnight or every month depending on the payout cycle of the network. Alternately, please schedule (cron job) the following URL as per the frequency of the payout cycle.', 'binary-mlm-pro'); ?></p>

            <p><?php echo MLM_URL ?>/cronjobs/paycycle.php</p>

            <?php echo payoutLicMsg() ?>

        </div>
        <div class='updated fade'><p><?php _e('Please ensure that you have marked all the members as Paid who have Paid in the current Payout cycle. In case not, then', 'binary-mlm-pro') ?> <a href="<?php echo get_bloginfo('url') ?>/wp-admin/users.php" ><?php _e('Click Here', 'binary-mlm-pro') ?></a> <?php _e('to go to the User Listing Page and mark those members as Paid.', 'binary-mlm-pro') ?></div>
        <div style="font-size:18px; padding:10px; color:#0000CC; "><?php if (!empty($payoutArr['directRun'])) _e($payoutArr['directRun']) ?><?php (!empty($msg)) ? _e($msg) : '' ?></div>	



        <form name="frm" method="post" action="">

            <div class="payout-run">
                <input class="button-primary" type="submit" name="distribute_commission_bonus" value="<?php _e('Distribute Commission and Bonus', 'binary-mlm-pro'); ?>" id="distribute_commission_bonus" /> 
            </div>

            <div class="payout-run">
                <input class="button-primary" type="submit" name="pay_cycle" value="<?php _e('Run Payout Routine', 'binary-mlm-pro'); ?>" id="pay_cycle" /> 
            </div>
            <!-- Dislay data -->
            <?php if (!empty($payoutArr['displayData']) && $payoutArr['displayData'] != '') {
                ?>
                <table width="100%" border="1" cellspacing="0" cellpadding="5" align="left" style="margin:20px 0 20px 0">
                    <tr>
                        <th scope="row"><?php _e('S.No', 'binary-mlm-pro'); ?></th>
                        <th scope="row"><?php _e('Username', 'binary-mlm-pro'); ?></th>
                        <th scope="row"><?php _e('Name', 'binary-mlm-pro'); ?></th>
                        <th scope="row"><?php _e('Commission', 'binary-mlm-pro'); ?></th>
                        <th scope="row"><?php _e('Direcr Referral Commission', 'binary-mlm-pro'); ?></th>
                        <th scope="row"><?php _e('Bonus', 'binary-mlm-pro'); ?></th>
                        <th scope="row"><?php _e('Cap Limit', 'binary-mlm-pro') ?></th>
                        <th scope="row"><?php _e('Service Charge', 'binary-mlm-pro'); ?><span tyle="font-size:10px">(<?php echo $sct ?>)</span></th>
                        <th scope="row"><?php _e('Tax', 'binary-mlm-pro'); ?></th>
                        <th scope="row"><?php _e('Net Amount', 'binary-mlm-pro'); ?></th>
                    </tr>

                    <?php
                    //print_r($payoutArr); die;
                    if ($payoutArr['displayData'] != 'None') {
                        $i = 1;
                        $listArr[-1]['username'] = __('Member Username', 'binary-mlm-pro');
                        $listArr[-1]['name'] = __('Member Email', 'binary-mlm-pro');
                        $listArr[-1]['commission'] = __('Withdrawal Initiate Date', 'binary-mlm-pro');
                        $listArr[-1]['dirRefcommission'] = __('Withdrawal Comment', 'binary-mlm-pro');
                        $listArr[-1]['bonus'] = __('Amount', 'binary-mlm-pro');
                        $listArr[-1]['cap_limit'] = __('Payout Id', 'binary-mlm-pro');
                        $listArr[-1]['service_charge'] = __('Payout Date', 'binary-mlm-pro');
                        $listArr[-1]['tax'] = __('Payout Date', 'binary-mlm-pro');
                        $listArr[-1]['net_amount'] = __('Payout Date', 'binary-mlm-pro');

                        foreach ($payoutArr['displayData'] as $row) {
                            ?>
                            <tr>
                                <td align="center"><?php echo $i; ?></td>
                                <td align="center"><?php echo $row['username']; ?></td>
                                <td align="center"><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                                <td align="center"><?php echo number_format($row['commission'], 2, '.', ''); ?></td>
                                <td align="center"><?php echo number_format($row['dirRefcommission'], 2, '.', ''); ?></td>
                                <td align="center"><?php echo number_format($row['bonus'], 2, '.', ''); ?></td>
                                <td align="center"><?php echo ($row['total_amt'] >= $row['cap_limit']) ? $row['cap_limit'] : 'N/A'; ?></td>
                                <td align="center"><?php echo number_format($row['service_charge'], 2, '.', ''); ?></td>
                                <td align="center"><?php echo number_format($row['tax'], 2, '.', ''); ?></td>


                                <td align="center"><?php
                                    echo
                                    ($row['total_amt'] >= $row['cap_limit'] && !empty($row['cap_limit'])) ?
                                            ($row['cap_limit'] == '0.00' ? number_format($row['net_amount'], 2, '.', '') : number_format($row['net_amount'], 2, '.', '') . '(capped)') : number_format($row['net_amount'], 2, '.', '');
                                    ?></td>
                            </tr>
                            <?php
                            $listArr[$i]['username'] = $row['username'];
                            $listArr[$i]['name'] = $row['first_name'] . " " . $row['last_name'];
                            $listArr[$i]['commission'] = number_format($row['commission'], 2, '.', '');
                            $listArr[$i]['dirRefcommission'] = number_format($row['dirRefcommission'], 2, '.', '');
                            $listArr[$i]['bonus'] = number_format($row['bonus'], 2, '.', '');
                            $listArr[$i]['cap_limit'] = ($row['total_amt'] >= $row['cap_limit']) ? $row['cap_limit'] : 'N/A';
                            $listArr[$i]['service_charge'] = number_format($row['service_charge'], 2, '.', '');
                            $listArr[$i]['tax'] = number_format($row['tax'], 2, '.', '');
                            $listArr[$i]['net_amount'] = ($row['total_amt'] >= $row['cap_limit'] && !empty($row['cap_limit'])) ?
                                    ($row['cap_limit'] == '0.00' ? number_format($row['net_amount'], 2, '.', '') : number_format($row['net_amount'], 2, '.', '') . '(capped)') : number_format($row['net_amount'], 2, '.', '');

                            $i++;
                        }
                    }
                    else {
                        ?>
                        <tr>
                            <td colspan="10" align="center"><?php _e('There is no any eligible member Found in this Payout.', 'binary-mlm-pro'); ?> </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table><br>
                <div class="payout-run" style="float:right;">
                    <input class="button-primary" type="submit" name="pay_actual_amount" value="<?php _e('All is Well. Commit.', 'binary-mlm-pro'); ?>" id="pay_actual_amount" /> 
                </div>
                <div class="payout-run" style="float:right;">
                    <a class="button-primary" href="?page=mlm-payout" ><?php _e('Something wrong. Cancel.', 'binary-mlm-pro'); ?></a> 
                </div>
                <?php
                $value = serialize($listArr);
                ?>
                <form method="post" action="<?php echo plugins_url() ?>/binary-mlm-pro/mlm_html/export.php">
                    <input type="hidden" name ="listarray" value='<?php echo $value ?>' />
                    <input type="hidden" name ="filename" value='payout-list-report' />
                    <input type="hidden" name ="pay_cycle" value='Run Payout Routine' />
                    <input  style='display:none'type="submit" name="export_csv" value="<?php _e('Export to CSV', 'binary-mlm-pro'); ?>" class="button-primary" style="margin-top:20px;"/></form>
                <?php
            }
            ?>
            <!-- End display data -->
            <div style="clear:both;"></div>	

        </form>


    </div>
    <?php
}
?>