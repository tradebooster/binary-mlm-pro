<?php

function adminReports() {
    global $pagenow, $wpdb;
    $general_settings = get_option('wp_mlm_general_settings');
    if (!empty($general_settings['product_price'])) {
        $tabs = array(
            'earningreports' => __('Earning Reports', 'binary-mlm-pro'),
            'epinreports' => __('ePin Report', 'binary-mlm-pro'),
            'withdrawalreport' => __('Withdrawal Report', 'binary-mlm-pro'),
            'payoutreports' => __('Payout Reports', 'binary-mlm-pro'),
        );

        $tabval = 'erningreports';
        $tabfun = 'earningReports';
    }
    else {
        $tabs = array(
            'epinreports' => __('ePin Report', 'binary-mlm-pro'),
            'withdrawalreport' => __('Withdrawal Report', 'binary-mlm-pro'),
            'payoutreports' => __('Payout Reports', 'binary-mlm-pro'),
        );

        $tabval = 'epinreports';
        $tabfun = 'adminMLMePinsReports';
    }

    if (!empty($_GET['tab'])) {
        if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-reports' && $_GET['tab'] == 'earningreports')
            $current = 'earningreports';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-reports' && $_GET['tab'] == 'epinreports')
            $current = 'epinreports';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-reports' && $_GET['tab'] == 'withdrawalreport')
            $current = 'withdrawalreport';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-reports' && $_GET['tab'] == 'payoutreports')
            $current = 'payoutreports';
    }
    else
        $current = $tabval;

    $links = array();

    _e('<div id="icon-themes" class="icon32"><br></div>');
    _e("<h1>MLM Reports</h1>", "binary-mlm-pro");
    _e('<h2 class="nav-tab-wrapper">');

    foreach ($tabs as $tab => $name) {
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        _e("<a class='nav-tab$class' href='?page=admin-reports&tab=$tab'>$name</a>");
    }
    _e('</h2>');


    if (!empty($_GET['tab'])) {
        if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-reports' && $_GET['tab'] == 'earningreports')
            earningReports();
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-reports' && $_GET['tab'] == 'epinreports')
            adminMLMePinsReports();
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-reports' && $_GET['tab'] == 'withdrawalreport')
            adminMLMSucessWithdrawals();
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-reports' && $_GET['tab'] == 'payoutreports')
            adminMLMPayoutReports();
    }
    else
        $tabfun();
}

function earningReports() {
    require_once('earning-reports.php');
}

?>