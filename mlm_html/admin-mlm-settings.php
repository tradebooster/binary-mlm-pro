<?php

require_once("php-form-validation.php");
require_once("admin-mlm-general-settings.php");
require_once("admin-mlm-eligibility-settings.php");
require_once("admin-mlm-payout-settings.php");
require_once("admin-mlm-bonus-settings.php");
require_once("admin-create-first-user.php");
require_once("admin-mlm-payout-run.php");
require_once("admin-mlm-epin-settings.php");
require_once("admin-mlm-reset-all-data.php");
require_once("admin-paypal-detail-page.php");
require_once("admin-mlm-email-template.php");
require_once("admin-mlm-product-price.php");

function adminMLMSettings() {
    global $pagenow, $wpdb;
    $table_prefix = mlm_core_get_table_prefix();
    $mlm_settings = get_option('wp_mlm_general_settings');
    $sql = "SELECT COUNT(*) AS num FROM {$table_prefix}mlm_users";
    $num = $wpdb->get_var($sql);

    if ($num == 0) {
        $tabs = array(
            'createuser' => __('Create First User', 'binary-mlm-pro'),
            'general' => __('General', 'binary-mlm-pro'),
            'eligibility' => __('Eligibility', 'binary-mlm-pro'),
            'payout' => __('Payout', 'binary-mlm-pro'),
            'bonus' => __('Bonus', 'binary-mlm-pro'),
            'email' => __('Email Settings', 'binary-mlm-pro'),
        );
        if (isset($mlm_settings['ePin_activate']) && $mlm_settings['ePin_activate'] == '1') {
            $tabs['epin_settings'] = __('ePins', 'binary-mlm-pro');
            $tabs['product_price'] = __('Manage Products', 'binary-mlm-pro');
        }
        /**
         * Detect plugin. For use in Admin area only.
         */
        include_once ABSPATH . '/wp-admin/includes/plugin.php';
        if (is_plugin_active('mlm-paypal-mass-pay/load-data.php')) {
            //plugin is activated
            $tabs['paypal_detail'] = __('Paypal Details', 'binary-mlm-pro');
        }

        $tabs['reset_all_data'] = __('Reset All MLM Data', 'binary-mlm-pro');
        $tabval = 'createuser';
        $tabfun = 'register_first_user';
    }
    else {
        $tabs = array(
            'general' => __('General', 'binary-mlm-pro'),
            'eligibility' => __('Eligibility', 'binary-mlm-pro'),
            'payout' => __('Payout', 'binary-mlm-pro'),
            'bonus' => __('Bonus', 'binary-mlm-pro'),
            'email' => __('Email Settings', 'binary-mlm-pro'),
        );
        if (isset($mlm_settings['ePin_activate']) && $mlm_settings['ePin_activate'] == '1') {
            $tabs['epin_settings'] = __('ePins', 'binary-mlm-pro');
            $tabs['product_price'] = __('Manage Products', 'binary-mlm-pro');
        }
        if (is_plugin_active('mlm-paypal-mass-pay/load-data.php')) {
            //plugin is activated
            $tabs['paypal_detail'] = __('Paypal Details', 'binary-mlm-pro');
        }

        $tabs['reset_all_data'] = __('Reset All MLM Data', 'binary-mlm-pro');
        $tabval = 'general';
        $tabfun = 'mlmGeneral';
    }
    if (!empty($_GET['tab'])) {
        if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'createuser')
            $current = 'createuser';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'general')
            $current = 'general';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'eligibility')
            $current = 'eligibility';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'payout')
            $current = 'payout';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'bonus')
            $current = 'bonus';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'email')
            $current = 'email';

        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'epin_settings')
            $current = 'epin_settings';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'product_price')
            $current = 'product_price';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'paypal_detail')
            $current = 'paypal_detail';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'reset_all_data')
            $current = 'reset_all_data';
    }
    else
        $current = $tabval;

    $links = array();

    _e('<div id="icon-themes" class="icon32"><br></div>');
    _e("<h1>MLM Settings</h1>", "binary-mlm-pro");
    _e('<h2 class="nav-tab-wrapper">');

    foreach ($tabs as $tab => $name) {
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        _e("<a class='nav-tab$class' href='?page=admin-settings&tab=$tab'>$name</a>");
    }
    _e('</h2>');

    if (!empty($_GET['tab'])) {
        if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'createuser')
            register_first_user();
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'general')
            mlmGeneral();
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'eligibility')
            mlmEligibility();
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'payout')
            mlmPayout();
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'bonus')
            mlmBonus();
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'email')
            mlmEmailTemplates();

        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'epin_settings')
            epin_tab();
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'product_price')
            mlmProductPrice();
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'paypal_detail')
            $current = Paypal_Detail();
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'reset_all_data')
            adminMlmReserAllData();
    }
    else
        $tabfun();
}

//end function
?>