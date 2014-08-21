<?php
add_filter('manage_users_columns', 'add_parent');
add_filter('manage_users_custom_column', 'add_parent_column_value', 10, 3);

function add_parent($columns) {
    $columns['parent_name'] = __('Parent', 'binary-mlm-pro');
    return $columns;
}

function add_parent_column_value($value, $column_name, $user_id) {
    global $wpdb;

    if ('parent_name' == $column_name) {
        return 'test';
    }
}

add_action('get_footer', 'saveCookies', 25);

function saveCookies() {
    global $wpdb;

    if (!empty($_GET['sp_name'])) {

        $sp_name = $wpdb->get_var("select username from {$wpdb->prefix}mlm_users where username='" . $_GET['sp_name'] . "'");
        if ($sp_name) {
            ?>	<script type='text/javascript'>
                jQuery.cookie('sp_name', '<?php echo $sp_name ?>', {path: '/'});
            </script>
            <?php
        }
    }
    else if (!empty($_REQUEST['sp'])) {
        $sp_name = $wpdb->get_var("select username from {$wpdb->prefix}mlm_users where user_key='" . $_REQUEST['sp'] . "'");
        if ($sp_name) {
            ?>	<script type='text/javascript'>
                jQuery.cookie('sp_name', '<?php echo $sp_name ?>', {path: '/'});
            </script>
            <?php
        }
    }
}

function calculatelegUsersByPayoutId($user_key, $payout_id) {
    for ($x = $payout_id; $x >= 0; $x--) {
        $pid[] = $x;
    }
    $payout_id = implode("','", $pid);
    $left_users = totalLeftLegUsersByPayoutId($user_key, $payout_id);
    $right_users = totalRightLegUsersByPayoutId($user_key, $payout_id);
    if ($left_users < $right_users) {
        $pairs = $left_users;
    }
    else {
        $pairs = $right_users;
    }
    return array('l' => $left_users, 'r' => $right_users, 'p' => $pairs);
}

function updatePaymentStatus($user_id, $status) {
    global $wpdb;
    if (isset($user_id) && isset($status)) {
        $mlm_general_settings = get_option('wp_mlm_general_settings');
        $product_price = $mlm_general_settings['product_price'];
        $sql = "UPDATE {$wpdb->prefix}mlm_users 
                SET payment_status = '" . $status . "' , payment_date='" . current_time('mysql') . "'
                WHERE user_id = '" . $user_id . "'";
        $rs = $wpdb->query($sql);
        if (!$rs) {
            _e("<span class='error' style='color:red'>Updating Fail</span>");
        }
    }
}

function getProducPrice($user_id) {
    global $wpdb;
    $var = $wpdb->get_var("SELECT product_price FROM {$wpdb->prefix}mlm_users WHERE user_id='$user_id'");
    return $var;
}

function insert_refferal_commision($user_id) {
    global $wpdb;
    $date = current_time('mysql');
    $mlm_payout = get_option('wp_mlm_payout_settings');
    $refferal_amount = $mlm_payout['referral_commission_amount'];
    if ($mlm_payout['dir_ref_comm_type'] == 'Percentage') {
        $refferal_amount = getProducPrice($user_id) * $refferal_amount / 100;
    }

    $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}mlm_users WHERE user_id=$user_id");
    $sponsor_key = $row->sponsor_key;
    $child_id = $row->id;
    if ($sponsor_key != 0) {
        $sponsor = $wpdb->get_row("SELECT id FROM {$wpdb->prefix}mlm_users WHERE user_key='" . $sponsor_key . "'");
        $sponsor_id = $sponsor->id;
        $sql = "INSERT INTO {$wpdb->prefix}mlm_referral_commission SET date_notified ='$date',sponsor_id='$sponsor_id',child_id='$child_id',amount='$refferal_amount',payout_id='0' ON DUPLICATE KEY UPDATE child_id='$child_id'";
        $rs = $wpdb->query($sql);
    }
}

function getUIDbyKey($key) {
    global $wpdb;
    $user_id = $wpdb->get_var("SELECT user_id FROM {$wpdb->prefix}mlm_users WHERE `user_key` = '$key'");
    return $user_id;
}

function SendMailToAll($user_key, $parent_key, $sponsor) { 
    global $wpdb;
    $user_id = getUIDbyKey($user_key);
    $user_info = get_userdata($user_id);
    $parent_username = getusernamebykey($parent_key);
    $sponsor_username = getusernamebykey($sponsor);
    $siteownwer = get_bloginfo('name');
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html; charset=iso-8859-1" . "\r\n";
    $headers .= "From: " . get_option('admin_email') . "<" . get_option('admin_email') . ">" . "\r\n";
    $subject = get_option('networkgrowing_email_subject', true);
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mlm_leftleg where ukey='$user_key' UNION ALL  SELECT * FROM {$wpdb->prefix}mlm_rightleg  where ukey='$user_key'");
    foreach ($results as $value) { 
        $pid = getUIDbyKey($value->pkey);
        $puser_info = get_userdata($pid);
        $pname = $puser_info->user_login;
        $pemail = $puser_info->user_email; 
        $message = nl2br(htmlspecialchars(get_option('networkgrowing_email_message', true)));
        $message = str_replace('[pname]', $pname, $message);
        $message = str_replace('[firstname]', $user_info->first_name, $message);
        $message = str_replace('[lastname]', $user_info->last_name, $message);
        $message = str_replace('[email]', $user_info->user_email, $message);
        $message = str_replace('[username]', $user_info->user_login, $message);
        $message = str_replace('[sponsor]', $sponsor_username, $message);
        $message = str_replace('[parent]', $parent_username, $message);
        $message = str_replace('[sitename]', $siteownwer, $message);
        wp_mail($pemail, $subject, $message, $headers);
    }
}

// If apply for with drawal From Front End
 
function WithDrawaiInitiatedMail($mlmId, $comment, $payoutId) {
    global $wpdb;
    $res = $wpdb->get_var("SELECT user_id FROM {$wpdb->prefix}mlm_users WHERE `id` = '" . $mlmId. "'");
    $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}mlm_payout WHERE `payout_id` = '$payoutId' AND user_id='$mlmId'");
    $user_info = get_userdata($res); 
    $siteownwer = get_bloginfo('name');
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
    $headers .= "From: " . get_option('admin_email') . "<" . get_option('admin_email') . ">" . "\r\n";
    $subject = get_option('withdrawalintiate_email_subject', true);
    $message = nl2br(htmlspecialchars(get_option('withdrawalintiate_email_message', true)));
    $message = str_replace('[firstname]', $user_info->first_name, $message);
    $message = str_replace('[lastname]', $user_info->last_name, $message);
    $message = str_replace('[email]', $user_info->user_email, $message);
    $message = str_replace('[username]', $user_info->user_login, $message);
    $message = str_replace('[amount]', $row->capped_amt, $message);
    $message = str_replace('[mode]', $row->payment_mode, $message);
    $message = str_replace('[comment]', $comment, $message);
    $message = str_replace('[payoutid]', $payoutId, $message);
    $message = str_replace('[sitename]', $siteownwer, $message); 
    wp_mail(get_option('admin_email'), $subject, $message, $headers);
    wp_mail($user_info->user_email, $subject, $message, $headers);
}

function WithDrawaiProcessedMail($userId, $mode, $amount, $payoutId) {
    global $wpdb;
    $res = $wpdb->get_var("SELECT user_id FROM {$wpdb->prefix}mlm_users WHERE `id` = '" . $userId . "' ");
    $user_info = get_userdata($res);
    $siteownwer = get_bloginfo('name');

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
    $headers .= "From: " . get_option('admin_email') . "<" . get_option('admin_email') . ">" . "\r\n";

    $subject = get_option('withdrawalProcess_email_subject', true);

    $message = nl2br(htmlspecialchars(get_option('withdrawalProcess_email_message', true)));
    $message = str_replace('[firstname]', $user_info->first_name, $message);
    $message = str_replace('[lastname]', $user_info->last_name, $message);
    $message = str_replace('[email]', $user_info->user_email, $message);
    $message = str_replace('[username]', $user_info->user_login, $message);
    $message = str_replace('[amount]', $amount, $message);
    $message = str_replace('[withdrawalmode]', $mode, $message);
    $message = str_replace('[payoutid]', $payoutId, $message);
    $message = str_replace('[sitename]', $siteownwer, $message);

    wp_mail(get_option('admin_email'), $subject, $message, $headers);
    wp_mail($user_info->user_email, $subject, $message, $headers);
}

function PayoutGeneratedMail($mlmuserId, $amount, $payoutMasterId) {
    global $wpdb;
    $res = $wpdb->get_var("SELECT user_id FROM {$wpdb->prefix}mlm_users WHERE `id` = '" . $mlmuserId . "' ");
    $user_info = get_userdata($res);
    $siteownwer = get_bloginfo('name');

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
    $headers .= "From: " . get_option('admin_email') . "<" . get_option('admin_email') . ">" . "\r\n";

    $subject = get_option('runpayout_email_subject', true);
    $message = nl2br(htmlspecialchars(get_option('runpayout_email_message', true)));
    $message = str_replace('[firstname]', $user_info->first_name, $message);
    $message = str_replace('[lastname]', $user_info->last_name, $message);
    $message = str_replace('[email]', $user_info->user_email, $message);
    $message = str_replace('[username]', $user_info->user_login, $message);
    $message = str_replace('[amount]', $amount, $message);
    $message = str_replace('[payoutid]', $payoutMasterId, $message);
    $message = str_replace('[sitename]', $siteownwer, $message);
    wp_mail(get_option('admin_email'), $subject, $message, $headers);
    wp_mail($user_info->user_email, $subject, $message, $headers);
}

function mlm_install() {

    mlm_core_install_users();
    mlm_core_install_leftleg();
    mlm_core_install_rightleg();
    mlm_core_install_country();
    mlm_core_install_currency();
    mlm_core_install_bonus();
    mlm_core_install_commission();
    mlm_core_install_payout_master();
    mlm_core_install_payout();
    mlm_core_install_refe_comm();
    mlm_core_update_payout();
    mlm_core_update_payout_master();
    /* load all initial values */
}

function register_plugin_page($pages) {
    foreach ($pages as $page) {
        $post_id = register_page($page['title'], $page['slug'], $page['shortcode']);
//$post_id = register_page($page['title'], $page['shortcode']);
        if (!empty($post_id)) {
            update_post_meta($post_id, $page['page'], $page['page']);
            if ($page['page'] != 'mlm_registration_page')
                update_post_meta($post_id, '_mlm_is_members_only', 'true');
        }
    }
}

function mlm_deactivate() {
//delete post from wp_posts and wp_postmeta table
    $Pages = array('mlm_registration_page', 'mlm_network_page', 'mlm_network_details_page',
        'mlm_network_genealogy_page', 'mlm_left_group_details_page', 'mlm_right_group_details_page',
        'mlm_personal_group_details_page', 'mlm_consultant_details_page', 'mlm_my_payout_page',
        'mlm_my_payout_details_page', 'mlm_update_profile_page', 'mlm_change_password_page',
        'mlm_epin_update_page', 'join_network'
    );
    $slugs = array('register-new-user', 'network', 'network-details', 'genealogy', 'left-group',
        'right-group', 'personal-group', 'my-consultants', 'my-payouts', 'my-payouts-details',
        'update-profile', 'change-password', 'epin-update', 'join-network'
    );
    $mlmpages = array_combine($Pages, $slugs);
    foreach ($mlmpages as $page => $slug) {
        $post_id1 = get_post_id($page);
        $post_id2 = get_page_id_by_slug($slug);
        wp_delete_post($post_id1, true);
        wp_delete_post($post_id2, true);
    }
    delete_option('menu_check');
    $term = get_term_by('name', MENU_NAME, 'nav_menu');
    wp_delete_term($term->term_id, 'nav_menu');
}

function mlm_remove() {
    mlm_core_drop_tables();
//delete the data from wp_options table
    delete_option('wp_mlm_general_settings');
    delete_option('wp_mlm_eligibility_settings');
    delete_option('wp_mlm_payout_settings');
    delete_option('wp_mlm_bonus_settings');
    delete_option('menu_check');
    delete_option('update_user_role_check');
    $theme_slug = get_option('stylesheet');
    delete_option("theme_mods_$theme_slug");
//delete the menu name form wp_terms table
}

function logout_session() {
    unset($_SESSION['search_user']);
    unset($_SESSION['session_set']);
    unset($_SESSION['userID']);
    unset($_SESSION['ajax']);
}

function fb_redirect_2() {
    global $current_user;
    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);

    $post_id = get_post_id('mlm_network_details_page');
    if ($user_role == 'subscriber' && $_SESSION['ajax'] != 'ajax_check') {
//if ( preg_match('#wp-admin/?(index.php)?$#', $_SERVER['REQUEST_URI']) ) 
        {
            if (function_exists('admin_url')) {
                wp_redirect(get_option('siteurl') . "/?page_id=$post_id");
            }
            else {
                wp_redirect(get_option('siteurl'));
            }
        }
    }
}

function mlm_login_redirect($redirect_to, $request, $user) {
//is there a user to check?
    if (!empty($user->roles)) {
        if (is_array($user->roles)) {
//check for admins
            if (in_array("administrator", $user->roles)) {
// redirect them to the default place
                return admin_url();
            }
            else {
                return home_url();
            }
        }
    }
}

function myplugin_load_textdomain() {
    load_plugin_textdomain('binary-mlm-pro', NULL, '/binary-mlm-pro/languages/');
}

function update_user_role() {
    global $wpdb;
    $results = $wpdb->get_results("select user_id from {$wpdb->prefix}mlm_users");
    foreach ($results as $result) {
        wp_update_user(array
            (
            'ID' => $result->user_id,
            'role' => 'mlm_user'
        ));
    }
}

function set_product_price() {
    global $wpdb;
    $mlm_settings = get_option('wp_mlm_general_settings');
    
    if (!empty($mlm_settings['product_price'])) {
        $price = $mlm_settings['product_price'];
        $insert = "INSERT INTO {$wpdb->prefix}mlm_product_price set product_name='MLM Product 1',product_price='$price'";
        $wpdb->query($insert);
        $p_id = $wpdb->get_var("select p_id from {$wpdb->prefix}mlm_product_price order by p_id DESC limit 1");
        $wpdb->query("update {$wpdb->prefix}mlm_epins set p_id='" . $p_id . "' where point_status='1'");
        $wpdb->query("update {$wpdb->prefix}mlm_epins set p_id='1' where point_status='0'");
        $wpdb->query("update {$wpdb->prefix}mlm_users set product_price='" . $price . "'");
        $results = $wpdb->get_results("select * from {$wpdb->prefix}mlm_epins where user_key!='0' AND point_status='0'");
        $num_row = $wpdb->num_rows;
        if ($num_row > 0) {
            foreach ($results as $result) {
                $wpdb->query("update {$wpdb->prefix}mlm_users set product_price='0' where user_key='" . $result->user_key . "'");
            }
        }
    }
}
?>
