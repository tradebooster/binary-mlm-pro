<div class="wrap">
    <div id="icon-users" class="icon32"><br/></div>
    <h2><?php _e('Earning Reports', 'binary-mlm-pro'); ?></h2>
</div>

<?php
global $wpdb;
global $table_prefix;
global $date_format;


extract($_REQUEST);
if (isset($datefrom) && !empty($datefrom)) {
    $datefrom1 = explode("/", $datefrom);

    $datefromfinal = $datefrom1[2] . '-' . $datefrom1[1] . '-' . $datefrom1[0] . ' 00:00:00';
    $timestamp = mktime(0, 0, 0, $datefrom1[1], $datefrom1[0], $datefrom1[2]);
    $month_name = date("F", $timestamp);
    $day = date("dS", $timestamp);

    $from = $day . ' ' . $month_name . ',' . $datefrom1[2];
}
else {
    $year = date('Y');
    $month = date('m');
    $day = '01';
    $datefromfinal = $year . '-' . $month . '-' . $day . ' 00:00:00';
    $timestamp = mktime(0, 0, 0, $month);
    $month_name = date("F", $timestamp);
    $from = $day . 'st ' . $month_name . ',' . $year;
}
if (isset($dateto) && !empty($dateto)) {
    $dateto1 = explode("/", $dateto);
    $datetofinal = $dateto1[2] . '-' . $dateto1[1] . '-' . ($dateto1[0] . ' 23:59:59');
    $timestamp = mktime(0, 0, 0, $dateto1[1], $dateto1[0], $dateto1[2]);
    $month_name = date("F", $timestamp);
    $day = date("dS", $timestamp);
    $to = $day . ' ' . $month_name . ',' . $dateto1[2];
}
else {
    $year = date('Y');
    $month = date('m');
    $day1 = date('d');
    $day = date('dS');
    $datetofinal = $year . '-' . $month . '-' . ($day1 . ' 23:59:59');
    $timestamp = mktime(0, 0, 0, $month);
    $month_name = date("F", $timestamp);
    $to = $day . ' ' . $month_name . ',' . $year;
}
if (isset($datefromfinal) && isset($datetofinal)) {
    $between = "AND wu.user_registered BETWEEN '$datefromfinal' AND '$datetofinal'";
    $between1 = "WHERE date BETWEEN '$datefromfinal' AND '$datetofinal'";
    $date_used = "AND date_used BETWEEN '$datefromfinal' AND '$datetofinal'";
}
else {
    $between = '';
    $between1 = '';
    $date_used = '';
}

//$total_amount = $wpdb->get_var("select sum(epin_value) from {$table_prefix}mlm_epins where status=1 $date_used");

$total_product_price = $wpdb->get_var("SELECT SUM(product_price) as total from {$table_prefix}mlm_users as mu INNER JOIN {$table_prefix}users as wu ON mu.user_id = wu.ID WHERE payment_status='1' $between");
$payout_paid = $wpdb->get_var("SELECT sum(total_amt) as amount from {$table_prefix}mlm_payout $between1");

if (isset($total_paid_users)) {
    $total_paid_users = $total_paid_users;
}
else {
    $total_paid_users = '0';
}

if (isset($payout_paid)) {
    $payout_paid = $payout_paid;
}
else {
    $payout_paid = '0';
}
$general_settings = get_option('wp_mlm_general_settings');
//$total_amount=$general_settings['product_value']*$total_paid_users;
$total_amount = $total_product_price;
$net_earning = $total_amount - $payout_paid;
?>
<div>&nbsp;</div>

<div>&nbsp;</div>
<script type="text/javascript">
    var popup1, popup2, splofferpopup1;
    var bas_cal, dp_cal11, dp_cal2, ms_cal; // declare the calendars as global variables 
    window.onload = function() {
        dp_cal11 = new Epoch('dp_cal11', 'popup', document.getElementById('datefrom'));
        dp_cal12 = new Epoch('dp_cal12', 'popup', document.getElementById('dateto'));
    };
</script>
<form id="processed_report" method="GET" action="">
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
    <input type="hidden" name="tab" value="earningreports" />
    <div style='width:300px;margin-left:30%'>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td align='left'><strong><?php _e('Date From', 'binary-mlm-pro'); ?></strong><strong>:</strong></td>
                <td align='right'><input type="text" name="datefrom" id="datefrom"></td>
            </tr>
            <tr><td><br/></td></tr>
            <tr>
                <td align='left'><strong><?php _e('Date To', 'binary-mlm-pro'); ?></strong><strong>:</strong></td>
                <td align='right'><input type="text" name="dateto" id="dateto"></td>
            </tr>
            <tr><td><br/></td></tr>
            <tr>
                <td></td>
                <td align='right' colspan='2'>
                    <input type="reset" name="reset" value="Reset" onclick="window.location = '<? admin_url() ?>'admin.php / ?page = admin - mlm - reports & tab = earningreports" style="float:right;">
                    <input type="submit" name="submit" value=" Go " style="float:right;"></td>
            </tr>
            <tr>
                <td><div align="center"></div></td>
            </tr>
        </table>
    </div>

</form>
<div>&nbsp;</div>
<?php
echo '<strong>' . __('Period', 'binary-mlm-pro') . ':</strong> ' . $from . __(' to ', 'binary-mlm-pro') . $to;
;
?>
<div>&nbsp;</div>
<table cellspacing="0" class="wp-list-table widefat fixed toplevel_page_admin-mlm-reports">

    <tr><td><strong><?php _e('Gross Earnings', 'binary-mlm-pro'); ?> :&nbsp;&nbsp;<?php echo $total_amount; ?></strong><br/><br/></td></tr>
    <tr><td><strong><?php _e('Payouts', 'binary-mlm-pro'); ?> :&nbsp;&nbsp;<?php echo $payout_paid; ?></strong><br/><br/></td></tr>
    <tr><td><strong><?php _e('Net Earnings', 'binary-mlm-pro'); ?>:&nbsp;&nbsp;<?php echo $net_earning; ?></strong><br/><br/></td></tr>


</table>