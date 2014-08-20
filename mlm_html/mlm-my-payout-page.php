<?php

function mlm_my_payout_page($id = '') {
    if ($id == '') {
        $detailsArr = my_payout_function();
        $summaryArr = my_payout_summary_function();
    }
    else {
        $detailsArr = my_payout_function($id);
        $summaryArr = my_payout_summary_function($id);
    }
    $mlm_settings = get_option('wp_mlm_general_settings');
//_e("<pre>");print_r($detailsArr); exit; 
    $page_id = get_post_id('mlm_my_payout_details_page');
    ?>
    <table width="50%" border="0" cellspacing="10" cellpadding="1" id="payout-page">
        <tr>
            <td colspan="2"><strong><?php _e('Summaries', 'binary-mlm-pro'); ?></strong></td>

        </tr>
        <tr>
            <td><?php _e('Total Amount Credited', 'binary-mlm-pro'); ?></td>
            <td><?php echo $mlm_settings['currency'] . ' '; ?><?php echo (!empty($summaryArr['total_amount'])) ? $summaryArr['total_amount'] : '0'; ?></td>
        </tr>
        <tr>
            <td><?php _e('Pending Payments', 'binary-mlm-pro'); ?></td>
            <td><?php echo $mlm_settings['currency'] . ' '; ?><?php echo (!empty($summaryArr['pending_amount'])) ? $summaryArr['pending_amount'] : '0'; ?></td>
        </tr>
        <tr>
            <td><?php _e('Processed Payments', 'binary-mlm-pro'); ?></td>
            <td><?php echo $mlm_settings['currency'] . ' '; ?><?php echo (!empty($summaryArr['processed_amount'])) ? $summaryArr['processed_amount'] : '0'; ?></td>
        </tr>
        <tr>
            <td><?php _e('Available Amount', 'binary-mlm-pro'); ?></td>
            <td><?php echo $mlm_settings['currency'] . ' '; ?><?php echo (!empty($summaryArr['available_amount'])) ? $summaryArr['available_amount'] : '0'; ?></td>
        </tr>

    </table>
    <?php
    if (count($detailsArr) > 0) {
        ?>
        <table width="100%" border="0" cellspacing="10" cellpadding="1" id="payout-page">
            <tr>
                <td> <?php  _e('Date', 'binary-mlm-pro'); ?></td>
                <td> <?php  _e('Amount', 'binary-mlm-pro'); ?></td>
                <td> <?php  _e('Status', 'binary-mlm-pro'); ?></td>
                <td> <?php  _e('Action', 'binary-mlm-pro'); ?></td>
            </tr>
            <?php
            //echo "<pre>"; print_r($detailsArr);
            foreach ($detailsArr as $row) :

                //print_r($row); die;
                $amount = ($row->total_amt <= $row->cap_limit || $row->cap_limit == 0.00) ? $row->capped_amt : $row->capped_amt . '(capped)';
                if ($row->withdrawal_initiated == 0 && $row->payment_processed == 0) {
                    $status = '<span style="color:#0DA443;">Available</span>';
                }
                else if ($row->withdrawal_initiated == 1 && $row->payment_processed == 0) {
                    $status = '<span style="color:#FF0000;">Pending</span>';
                }
                else if ($row->withdrawal_initiated == 1 && $row->payment_processed == 1) {
                    $status = '<span>Processed</span>';
                }
                ?>
                <tr>
                    <td><?php echo $row->payoutDate ?></td>
                    <td><?php echo $mlm_settings['currency'] . ' ' . $amount ?></td>
                    <td><?php echo $status ?></td>
                    <?php if ($id == '') { ?>
                        <td><a href="<?php echo get_post_id_or_postname_for_payout('mlm_my_payout_details_page', $row->payout_id) ?>" style="text-decoration:none;"><?php _e('View', 'binary-mlm-pro'); ?></a></td>
                        <?php
                    }
                    else {
                        ?>
                        <td><a href="?page=mlm-user-account&ac=payout-details&pid=<?php echo $row->payout_id ?>" style="text-decoration:none;"><?php _e('View', 'binary-mlm-pro'); ?></a></td>				
                        <?php
                    }
                    ?>

                </tr>

            <?php endforeach; ?>

        </table>
        <?php
    }
    else {
        ?>
        <div class="no-payout"><?php _e('You have not earned any commisssions yet.', 'binary-mlm-pro'); ?> </div>

        <?php
    }
}
?>
