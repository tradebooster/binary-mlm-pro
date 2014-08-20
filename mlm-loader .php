<?php

session_start();
/*
  Plugin Name: Binary MLM Pro
  Plugin URI: http://tradebooster.com
  Description: The only Binary MLM plugin for Wordpress. Run a full blown MLM website from within 
  your favourite CMS.
  Version: 3.0
  Author: Tradebooster
  Author URI: http://tradebooster.com
  License: GPL
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
/** Constants **************************************************************** */
global $wpdb, $current_user;
mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);

if (!defined('WP_BINARY_MLM_ULR')){
    define('WP_BINARY_MLM_ULR', 'http://wpbinarymlm.com');
}
// Path and URL
if (!defined('MLM_PLUGIN_DIR')) {
    define('MLM_PLUGIN_DIR', WP_PLUGIN_DIR . '/binary-mlm-pro');
}
if (!defined('MLM_PLUGIN_NAME')) {
    define('MLM_PLUGIN_NAME', 'binary-mlm-pro');
}
define('MLM_URL', plugins_url('', __FILE__));
if (!defined('MYPLUGIN_VERSION_KEY')) {
    define('MYPLUGIN_VERSION_KEY', 'myplugin_version');
}
if (!defined('MYPLUGIN_VERSION_NUM')) {
    define('MYPLUGIN_VERSION_NUM', '3.0');
}
add_option(MYPLUGIN_VERSION_KEY, MYPLUGIN_VERSION_NUM);

//include all the core funcitons file
require_once(MLM_PLUGIN_DIR . '/Class.php');
require_once(MLM_PLUGIN_DIR . '/mlm-constant.php');
require_once(MLM_PLUGIN_DIR . '/common-functions.php');
require_once(MLM_PLUGIN_DIR . '/mlm-access-control.php');
require_once(MLM_PLUGIN_DIR . '/mlm_core/mlm-core-schema.php');

//include all  file for admin manage
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-user-update-profile.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-user-account.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-view-user-network.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-member-license-setting.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-mlm-pending-withdrawal.php' );
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-mlm-withdrawal-process.php' );
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-mlm-sucessed-withdrawal.php' );
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-mlm-epins-reports.php' );
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-reports.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-mlm-payout-reports.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-create-first-user.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-mlm-dashboard.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-mlm-settings.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-pay-cycle.php' );

//include all  file for user Interface 
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-registration-page.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-view-network.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-network-details.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-left-group-details.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-right-group-details.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-personal-group-details.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-total-sales.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-my-payout-page.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-my-payout-details-page.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-po-file-editor.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-update-profile.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-change-password.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-join-network.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-epin-update.php');

//HOOK INTO WORDPRESS
add_action('init', 'register_shortcodes');
/* Runs  when plugin is activated */
register_activation_hook(__FILE__, 'mlm_install');
//1st agru is the TITLE & second is CONTENT
$pages = array(0 => array('title' => MLM_REGISTRATION_TITLE,
        'slug' => 'register-new-user',
        'shortcode' => MLM_REGISTRATIN_SHORTCODE,
        'page' => 'mlm_registration_page'),
    1 => array('title' => MLM_VIEW_NETWORK_TITLE,
        'slug' => 'network',
        'shortcode' => MLM_VIEW_NETWORK_SHORTCODE,
        'page' => 'mlm_network_page'),
    2 => array('title' => MLM_NETWORK_DETAILS_TITLE,
        'slug' => 'network-details',
        'shortcode' => MLM_NETWORK_DETAILS_SHORTCODE,
        'page' => 'mlm_network_details_page'),
    3 => array('title' => MLM_VIEW_GENEALOGY_TITLE,
        'slug' => 'genealogy',
        'shortcode' => MLM_VIEW_GENEALOGY_SHORTCODE,
        'page' => 'mlm_network_genealogy_page'),
    4 => array('title' => MLM_LEFT_GROUP_DETAILS_TITLE,
        'slug' => 'left-group',
        'shortcode' => MLM_LEFT_GROUP_DETAILS_SHORTCODE,
        'page' => 'mlm_left_group_details_page'),
    5 => array('title' => MLM_RIGHT_GROUP_DETAILS_TITLE,
        'slug' => 'right-group',
        'shortcode' => MLM_RIGHT_GROUP_DETAILS_SHORTCODE,
        'page' => 'mlm_right_group_details_page'),
    6 => array('title' => MLM_PERSONAL_GROUP_DETAILS_TITLE,
        'slug' => 'personal-group',
        'shortcode' => MLM_PERSONAL_GROUP_DETAILS_SHORTCODE,
        'page' => 'mlm_personal_group_details_page'),
    7 => array('title' => MLM_MY_CONSULTANT_TITLE,
        'slug' => 'my-consultants',
        'shortcode' => MLM_MY_CONSULTANT_SHORTCODE,
        'page' => 'mlm_consultant_details_page'),
    8 => array('title' => MLM_MY_PAYOUTS,
        'slug' => 'my-payouts',
        'shortcode' => MLM_MY_PAYOUTS_SHORTCODE,
        'page' => 'mlm_my_payout_page'),
    9 => array('title' => MLM_MY_PAYOUT_DETAILS,
        'slug' => 'my-payouts-details',
        'shortcode' => MLM_MY_PAYOUT_DETAILS_SHORTCODE,
        'page' => 'mlm_my_payout_details_page'),
    10 => array('title' => MLM_UPDATE_PROFILE_TITLE,
        'slug' => 'update-profile',
        'shortcode' => MLM_UPDATE_PROFILE_SHORTCODE,
        'page' => 'mlm_update_profile_page'),
    11 => array('title' => MLM_CHANGE_PASSWORD_TITLE,
        'slug' => 'change-password',
        'shortcode' => MLM_CHANGE_PASSWORD_SHORTCODE,
        'page' => 'mlm_change_password_page'),
    12 => array('title' => MLM_EPIN_UPDATE_TITLE,
        'slug' => 'epin-update',
        'shortcode' => MLM_EPIN_UPDATE_SHORTCODE,
        'page' => 'mlm_epin_update_page'),
    13 => array('title' => JOIN_NETWORK,
        'slug' => 'join-network',
        'shortcode' => JOIN_NETWORK_SHORTCODE,
        'page' => 'join_network'),
);

/* * *****New Install Plugin****** */
$run_once = get_option('menu_check');
if (!$run_once) {
    add_action('create_pages', 'register_plugin_page', 10, 1);
    do_action('create_pages', $pages);
    require_once(MLM_PLUGIN_DIR . '/TemplateValues.php');
    foreach ($MLMMemberInitialData AS $key => $value) {
        update_option($key, $value);
    }
    add_action('init', 'createTheMlmMenu');
}
/* * *****Upgrade Plugin****** */
$upgrade_page_menu_check = get_option('upgrade_page_menu_check');
if (empty($upgrade_page_menu_check)) {
    add_action('create_pages', 'register_plugin_page', 10, 1);
    do_action('create_pages', $pages);
    add_action('init', 'createTheMlmMenu');
    require_once(MLM_PLUGIN_DIR . '/TemplateValues.php');
    foreach ($MLMMemberInitialData AS $key => $value) {
        update_option($key, $value);
    }
    update_option('upgrade_page_menu_check', '1');
}
//shows custom message after plugin activation
add_action('admin_notices', 'show_message_after_plugin_activation');
/* Runs wher plugin is deactivated */
register_deactivation_hook(__FILE__, 'mlm_deactivate');

/* Runs wher plugin is Uninstall */
register_uninstall_hook(__FILE__, 'mlm_remove');

if (is_admin()) {
    /* Call the html code */
    add_action('admin_menu', 'mlm_admin_menu');
}

/* Array */
$mlm_settings = get_option('wp_mlm_general_settings');
$paymenntStatusArr = array(0 => 'Unpaid', 1 => 'Paid');
if (isset($mlm_settings['ePin_activate']) && $mlm_settings['ePin_activate'] == '1') {
    $paymenntStatusArr[2] = 'Free Pin';
}

add_action('init', 'load_javascript');
//add_action('init', 'fb_redirect_2');
add_filter("login_redirect", "mlm_login_redirect", 10, 3);
add_action('wp_logout', 'logout_session');
add_action('plugins_loaded', 'myplugin_load_textdomain');

$new_version = '3.0';
if (get_option(MYPLUGIN_VERSION_KEY) != $new_version) {
    add_action('plugins_loaded', 'mlm_core_install_epins');
    add_action('plugins_loaded', 'mlm_core_install_product_price');
    add_action('plugins_loaded', 'mlm_core_update_mlm_users');
    add_action('plugins_loaded', 'mlm_core_modify_mlm_users');
    add_action('plugins_loaded', 'mlm_core_update_mlm_leftleg');
    add_action('plugins_loaded', 'mlm_core_update_mlm_rightleg');
    add_action('plugins_loaded', 'mlm_core_alter_epins');
    add_action('plugins_loaded', 'mlm_core_insert_into_country');
    add_action('plugins_loaded', 'mlm_core_insert_into_currency');
    add_action('plugins_loaded', 'net_amount_payout');
    update_option(MYPLUGIN_VERSION_KEY, $new_version);
}

//if (get_option(MYPLUGIN_VERSION_KEY) == $new_version) {
else {
    add_action('plugins_loaded', 'mlm_core_install_epins');
    add_action('plugins_loaded', 'mlm_core_install_product_price');
    add_action('plugins_loaded', 'mlm_core_update_mlm_users');
    add_action('plugins_loaded', 'mlm_core_modify_mlm_users');
    add_action('plugins_loaded', 'mlm_core_update_mlm_leftleg');
    add_action('plugins_loaded', 'mlm_core_update_mlm_rightleg');
    add_action('plugins_loaded', 'mlm_core_alter_epins');
    add_action('plugins_loaded', 'mlm_core_insert_into_country');
    add_action('plugins_loaded', 'mlm_core_insert_into_currency');
    add_action('plugins_loaded', 'net_amount_payout');
    update_option(MYPLUGIN_VERSION_KEY, $new_version);
}

add_action('plugins_loaded', 'mlm_core_install_epins');

add_role('mlm_user', __('MLM User'));
$role_ckeck = get_option('update_user_role_check');
if (empty($role_ckeck)) {
    add_action('init', 'update_user_role');
    update_option('update_user_role_check', 1);
}
/* * *********Upgrade plugin process************** */
$BMP_Instance = new BMP();

add_filter('site_transient_update_plugins', array(&$BMP_Instance, 'Plugin_Update_Notice'));
add_filter('plugins_api', array(&$BMP_Instance, 'Plugin_Info_Hook'), 10, 3);

add_filter('upgrader_pre_install', array(&$BMP_Instance, 'Pre_Upgrade'), 10, 2);
add_filter('upgrader_post_install', array(&$BMP_Instance, 'Post_Upgrade'), 10, 2);

add_action('admin_notices', array(&$BMP_Instance, 'UpdateNag'));
add_action('admin_init', array(&$BMP_Instance, 'dismiss_mlm_update_notice'));
add_action('admin_init', array(&$BMP_Instance, 'Upgrade_Check'));

/* * *********Upgrade plugin process************** */

$RunOnce = get_option('upgrade_plugin_mlm');
if (!$RunOnce) {
    add_action('init', 'set_product_price');
    update_option('upgrade_plugin_mlm', true);
}

// Add settings link on plugin page
function your_plugin_settings_link($links) { 
  $settings_link = "<a href='".admin_url()."/admin.php?page=admin-settings'>Setting</a>"; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'your_plugin_settings_link' );
?>