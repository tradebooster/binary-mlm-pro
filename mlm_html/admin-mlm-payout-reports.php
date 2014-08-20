<?php

function mlm_payout_reports() {
    global $wpdb, $table_prefix;
    require_once('admin-mlm-payout--reports-list-table.php');
    $objpayouts = new PayoutReport_List_Table();
    $objpayouts->prepare_items();
    ?>

    <div class='wrap'>
        <div id="icon-users" class="icon32"></div><h1><?php _e('Payout Report', 'binary-mlm-pro'); ?></h1>
        <div class="notibar msginfo" style="margin:10px;">
            <a class="close"></a>
            <p><?php _e('Given below is the list of all Payout requests that have been successfully processed.', 'binary-mlm-pro'); ?></p>
        </div>	
    </div>	
    <form id="processed_report" name="myform" method="GET" action="">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <input type="hidden" name="tab" value="payoutreport" />

        <?php
        if (empty($_GET['id'])) {
            $objpayouts->display();
        }
        else {
            $payout_id = $_GET['id'];
            $results = $wpdb->get_results("select * from {$table_prefix}mlm_commission where payout_id='$payout_id'");

            foreach ($results as $result) {
                $pkey = $wpdb->get_var("select user_key from {$table_prefix}mlm_users where id='$result->parent_id'");
                $unames = explode(',', $result->child_ids);
                foreach ($unames as $uname) {
                    $ukey = $wpdb->get_var("select user_key from {$table_prefix}mlm_users where username='$uname'");
                    $wpdb->query("update {$table_prefix}mlm_leftleg set payout_id='$payout_id' where pkey='$pkey' and ukey='$ukey' and commission_status='1'");
                    $wpdb->query("update {$table_prefix}mlm_rightleg set payout_id='$payout_id' where pkey='$pkey' and ukey='$ukey' and commission_status='1'");
                }
            }
            ?>

            <table width="100%" border="0" cellpadding="10" cellspacing="0">
                <tr><td>&nbsp;</td></tr>
                <tr style="background:#fff;border-color:#bbb;color:#555;">
                    <th>S.No</th>
                    <th>Ref.</th>
                    <th>Full Name</th>
                    <th>Referral Commission</th>
                    <th>Pair Commission</th>
                    <th>Bonus</th>
                    <th>Amount</th>
                    <th>Pairs</th>
                    <th>Downline</th>
                </tr>
                <?php
                $id = $_GET['id'];
                $sql = "select * from {$table_prefix}mlm_payout where payout_id = $id";
                $results = $wpdb->get_results($sql, ARRAY_A);
                $i = 0;
                $per_page = 5;
                foreach ($results as $row) {
                    $i++;
                    $user_id = $row['user_id'];
                    $query = "SELECT user_id,user_key,username from {$table_prefix}mlm_users where id = '$user_id'";
                    $rows = $wpdb->get_row($query,ARRAY_A);
                    $firstname = get_user_meta($rows['user_id'], 'first_name', true);
                    $lastname = get_user_meta($rows['user_id'], 'last_name', true);
                    $user_key = $rows['user_key'];
                    $username = $rows['username'];
                    $record = calculatelegUsersByPayoutId($user_key, $payout_id);
                    ?>
                    <tr style=" background-color: #f9f9f9;">
                        <td align='center' style="background-color: #f9f9f9;"><?php echo $i ?></td>
                        <td align='center' style="background-color: #f9f9f9;"><?php echo $username; ?></td>
                        <td align='center' style="background-color: #f9f9f9;"><?php echo $firstname . ' ' . $lastname ?></td>
                        <td align='center' style="background-color: #f9f9f9;"><?php echo $row['referral_commission_amount']; ?></td>
                        <td align='center' style="background-color: #f9f9f9;"><?php echo $row['commission_amount']; ?></td>
                        <td align='center' style="background-color: #f9f9f9;"><?php echo $row['bonus_amount']; ?></td>
                        <td align='center' style="background-color: #f9f9f9;"><?php echo $row['total_amt']; ?></td>
                        <td align='center' style="background-color: #f9f9f9;"><?php echo $record['p']; ?></td>
                        <td align='center' style="background-color: #f9f9f9;"><strong>Left:</strong><?php echo $record['l'] . '<br />' ?><strong>Right:</strong><?php echo $record['r'] ?></td>

                    </tr>
                    <?php
                }
                ?>
            </table>

            <div><br/><br/><input type="button" name="back" id="back" value="&laquo; <?php _e('Back', 'binary-mlm-pro'); ?> " class='button-primary' onclick="window.history.back()" style="width:80px">   </div>
                <?php
            }
            ?>
    </form>	
    <script language='JavaScript' type='text/javascript'>
        var frmvalidator = new Validator('myform');
        //frmvalidator.addValidation('datefrom','req', 'Please enter from date');

    </script>
    <?php
    extract($_REQUEST); //echo '<pre>';print_r($_REQUEST);echo '</pre>';

    $sql = "SELECT * FROM {$table_prefix}mlm_payout_master ORDER BY date DESC";
    $rs = $wpdb->get_results($sql, ARRAY_A);
    $i = 0;
    $listArr = array();
    $listArr[-1]['id'] = __('Payout ID', 'binary-mlm-pro');
    $listArr[-1]['date'] = __('Payout Date', 'binary-mlm-pro');
    $listArr[-1]['View'] = __('Details', 'binary-mlm-pro');

    $num = $wpdb->num_rows;
    if ($num > 0) {
        foreach ($rs as $row) {
            $listArr[$i]['id'] = $row['id'];
            $listArr[$i]['date'] = $row['date'];
            $listArr[$i]['View'] = $row['id'];
            $i++;
        }
    }

    $value = serialize($listArr);
    ?>

    <?php
}
?>