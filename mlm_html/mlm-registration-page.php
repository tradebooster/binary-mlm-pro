<?php
require_once("php-form-validation.php");

function register_user_html_page() {  
    global $wpdb, $current_user;
    $user_id = $current_user->ID;
    $table_prefix = mlm_core_get_table_prefix();
    $error = '';
    $chk = 'error';
    include_once ABSPATH . '/wp-admin/includes/plugin.php';
    if (!empty($_GET['sp_name'])) {
        $sp_name = $wpdb->get_var("select username from {$table_prefix}mlm_users where username='" . $_GET['sp_name'] . "'");
        if ($sp_name) {
            ?>
            <script type='text/javascript'>
                $.cookie('sp_name', '<?php echo $sp_name ?>', {path: '/'});
            </script>
            <?php
        }
    }
    else if (!empty($_REQUEST['sp'])) {
        $sp_name = getusernamebykey($_REQUEST['sp']);
        if ($sp_name) {
            ?>
            <script type='text/javascript'>
                $.cookie('sp_name', '<?php echo $sp_name ?>', {path: '/'});
            </script>
            <?php
        }
    }
    else {
        $sp_name = empty($_COOKIE["sp_name"]) ? '' : $_COOKIE["sp_name"];
    }
    get_currentuserinfo();
    $mlm_general_settings = get_option('wp_mlm_general_settings');
    if (is_user_logged_in()) {
        $sponsor_name = $current_user->user_login;
        $readonly_sponsor = 'readonly';
    }
    else if (isset($_REQUEST['sp']) && $_REQUEST['sp'] != '') {
        $sponsorName = getusernamebykey($_REQUEST['sp']);
        if (isset($sponsorName) && $sponsorName != '') {
            $readonly_sponsor = 'readonly';
            $sponsor_name = $sponsorName;
        }
        else {
            redirectPage(home_url(), array());
            exit;
        }
    }
    else if (!empty($_REQUEST['sp_name'])) {
        $sponsorName = $_REQUEST['sp_name'];
        if (!empty($sponsorName)) {
            $readonly_sponsor = 'readonly';
            $sponsor_name = $sponsorName;
        }
        else {
            redirectPage(home_url(), array());
            exit;
        }
    }
    else {
        $readonly_sponsor = '';
    }
    /* script for auto insert users================================================ */

    if ($_SERVER['HTTP_HOST'] == '192.168.100.100') {
        echo '<form name="form1"action="" method="post">
      <input type="number" min="0" max="99" name="id"/>
      <input type="number" min="0" max="1" name="epin"/>
      <input type="number" min="0" max="1" name="leg"/>
      <input type="submit"/></form>';
        $epinstatus = isset($_POST['epin']) ? $_POST['epin'] : '';
        if ($epinstatus != '')
            $epin_no = $wpdb->get_var("select epin_no from {$table_prefix}mlm_epins where  point_status='$epinstatus' AND status=0 limit 1 ");
        if (isset($_POST['id'])) {
            $z = $_POST['id'];
            $_POST = array('firstname' => 'binary' . $z,
                'lastname' => 'binary' . $z,
                'username' => 'binary' . $z,
                'password' => 'binary' . $z,
                'confirm_password' => 'binary' . $z,
                'email' => 'binary' . $z . '@gmail.com',
                'confirm_email' => 'binary' . $z . '@gmail.com',
                'sponsor' => !empty($sponsor_name) ? $sponsor_name : '',
                'submit' => 'submit',
                'leg' => $_POST['leg'],
                'epin' => $epin_no,
                'paypal_id' => 'binary' . $z . '@gmail.com',
            );
        }      //'epin'=>!empty($epin_no)?$epin_no:'',
//echo "<pre>"; print_r($_SERVER); echo "</pre>";
    }
    /* ===========================================================Close Auto Insert. */
    //most outer if condition
    if (isset($_POST['submit'])) {
        $firstname = sanitize_text_field($_POST['firstname']);
        $lastname = sanitize_text_field($_POST['lastname']);
        $username = sanitize_text_field($_POST['username']);
        $epin = sanitize_text_field(isset($_POST['epin']) ? $_POST['epin'] : '');
        $sponsor = sanitize_text_field($_POST['sponsor']);
        $password = sanitize_text_field($_POST['password']);
        $confirm_pass = sanitize_text_field($_POST['confirm_password']);
        $email = sanitize_text_field($_POST['email']);
        $confirm_email = sanitize_text_field($_POST['confirm_email']);
        $invalid_usernames = array('admin');
        $username = sanitize_user($username);
        if (!validate_username($username) || in_array($username, $invalid_usernames)) {
            $error .= "\n Username is invalid.";
        }
        if (username_exists($username)) {
            $error .= "\n Username already exists.";
        }
        if (empty($sponsor)) {
            $sponsor = $wpdb->get_var("select `username` FROM {$table_prefix}mlm_users order by id asc limit 1");
        }
        if (!empty($epin) && epin_exists($epin)) {
            $error .= "\n ePin already issued or wrong ePin.";
        }
        if (!empty($mlm_general_settings['sol_payment']) && empty($epin)) {
            $error .= "\n Please enter your ePin.";
        }
        else if (empty($_POST['epin_value']) && empty($epin)) {
            $error .= "\n Please either enter the ePin or select the Product.";
        }
        if (checkInputField($password)) {
            $error .= "\n Please enter your password.";
        }
        if (confirmPassword($password, $confirm_pass)) {
            $error .= "\n Please confirm your password.";
        }
        if (checkInputField($sponsor)) {
            $error .= "\n Please enter your sponsor name.";
        }
        if (checkInputField($firstname)) {
            $error .= "\n Please enter your first name.";
        }
        if (checkInputField($lastname)) {
            $error .= "\n Please enter your last name.";
        }
        if (!is_email($email)) {
            $error .= "\n E-mail address is invalid.";
        }
        if (email_exists($email)) {
            $error .= "\n E-mail address is already in use.";
        }
        if (confirmEmail($email, $confirm_email)) {
            $error .= "\n Please confirm your email address.";
        }
        include_once ABSPATH . '/wp-admin/includes/plugin.php';
        if (is_plugin_active('mlm-paypal-mass-pay/load-data.php')) {
            $paypalId = sanitize_text_field($_POST['paypal_id']);
            if (checkInputField($paypalId)) {
                $error .= "\n Please enter your Paypal id.";
            }
        }
        $sql = "SELECT COUNT(*) num, `user_key` FROM {$table_prefix}mlm_users WHERE `username` = '" . $sponsor . "'";
        $intro = $wpdb->get_row($sql);
        if (isset($_GET['l']) && $_GET['l'] != '')
            $leg = $_GET['l'];
        else
            @$leg = $_POST['leg'];
        if (isset($leg) && $leg != '0') {
            if ($leg != '1') {
                $error .= "\n You have enter a wrong placement.";
            }
        }
        //generate random numeric key for new user registration
        $user_key = generateKey();
        //if generated key is already exist in the DB then again re-generate key
        do {
            $check = $wpdb->get_var("SELECT COUNT(*) ck FROM {$table_prefix}mlm_users WHERE `user_key` = '" . $user_key . "'");
            $flag = 1;
            if ($check == 1) {
                $user_key = generateKey();
                $flag = 0;
            }
        } while ($flag == 0);
        //check parent key exist or not
        if (isset($_GET['k']) && $_GET['k'] != '') {
            if (!checkKey($_GET['k']))
                $error .= "\n Parent key does't exist.";
            // check if the user can be added at the current position
            $checkallow = checkallowed($_GET['k'], $leg);
            if ($checkallow >= 1)
                $error .= "\n You have enter a wrong placement.";
        }
        if (!isset($leg)) {
            $key = $wpdb->get_var("SELECT user_key FROM {$table_prefix}mlm_users WHERE user_id = '$user_id'");
            $l = totalLeftLegUsers($key);
            $r = totalRightLegUsers($key);
            if ($l < $r) {
                $leg = '0';
            }
            else {
                $leg = '1';
            }
        }
        // outer if condition
        if (empty($error)) {
            // inner if condition
            if ($intro->num == 1) {
                $sponsor = $intro->user_key;

                $sponsor1 = $sponsor;
                //find parent key
                if (isset($_GET['k']) && $_GET['k'] != '') {
                    $parent_key = $_GET['k'];
                }
                else {
                    $readonly_sponsor = '';
                    do {
                        $sql = "SELECT `user_key` FROM {$table_prefix}mlm_users WHERE parent_key = '" . $sponsor1 . "' AND 
				leg = '" . $leg . "' AND banned = '0'";
                        $spon = $wpdb->get_var($sql);
                        $num = $wpdb->num_rows;
                        if ($num) {
                            $sponsor1 = $spon;
                        }
                    } while ($num == 1);
                    $parent_key = $sponsor1;
                }
                $user = array
                    (
                    'user_login' => $username,
                    'user_pass' => $password,
                    'first_name' => $firstname,
                    'last_name' => $lastname,
                    'user_email' => $email,
                    'user_registered' => current_time('mysql'),
                    'role' => 'mlm_user'
                );

                // return the wp_users table inserted user's ID
                $user_id = wp_insert_user($user);


                /* Send e-mail to admin and new user  */
                wp_new_user_notification($user_id, $password);
                $pc = isset($mlm_general_settings['product_price']) ? $mlm_general_settings['product_price'] : '0';

                //insert the data into fa_user table

                if (!empty($epin)) {
                    $pointResult = $wpdb->get_row("select p_id,point_status from {$table_prefix}mlm_epins where epin_no = '{$epin}'");
                    $pointStatus = $pointResult->point_status;
                    $productPrice = $wpdb->get_var("SELECT product_price FROM {$table_prefix}mlm_product_price WHERE p_id = '" . $pointResult->p_id . "'");
                    // to epin point status 1 
                    if ($pointStatus[0] == '1') {
                        $paymentStatus = '1';
                        $payment_date = current_time('mysql');
                    }
                    // to epin point status 1 
                    else if ($pointStatus[0] == '0') {
                        $paymentStatus = '2';
                        $payment_date = current_time('mysql');
                    }
                }
                else if (!empty($_POST['epin_value'])) { 
                    $productPrice = $wpdb->get_var("SELECT product_price FROM {$table_prefix}mlm_product_price WHERE p_id = '" . $_POST['epin_value'] . "'");
                    $paymentStatus = '0';
                    $payment_date = '0000-00-00 00:00:00';
                }
                else { // to non epin 
                    $paymentStatus = '0';
                    $payment_date = '0000-00-00 00:00:00';
                }


                 $insert = "INSERT INTO {$table_prefix}mlm_users
						   (
								user_id, username, user_key, parent_key, sponsor_key, leg,payment_date,payment_status,product_price
							) 
							VALUES
							(
								'" . $user_id . "','" . $username . "', '" . $user_key . "', '" . $parent_key . "', '" . $sponsor . "', '" . $leg . "','" . $payment_date . "','" . $paymentStatus . "','" . $productPrice . "'
							)";

                // if all data successfully inserted
                if ($wpdb->query($insert)) { //begin most inner if condition
                    //entry on Left and Right Leg tables
                    if ($leg == 0) {
                        $insert = "INSERT INTO {$table_prefix}mlm_leftleg set  pkey='" . $parent_key . "',ukey='" . $user_key . "'";
                        $insert = $wpdb->query($insert);
                        if ($u = get_option('network_mail', true) == 1) {
                        }
                    }
                    else if ($leg == 1) {
                        $insert = "INSERT INTO {$table_prefix}mlm_rightleg set pkey='" . $parent_key . "',ukey='" . $user_key . "'";
                        $insert = $wpdb->query($insert);
                        if ($u = get_option('network_mail', true) == 1) {
                        }
                    }
                    SendMailToAll($user_key,$parent_key,$sponsor);
                    //begin while loop
                    while ($parent_key != '0') {
                        $query = "SELECT COUNT(*) num, parent_key, leg 
								  FROM {$table_prefix}mlm_users 
								  WHERE user_key = '" . $parent_key . "'
								  AND banned = '0'";
                        $result = $wpdb->get_row($query);
                        if ($result->num == 1) {
                            if ($result->parent_key != '0') {
                                if ($result->leg == 1) {
                                    $tbright = "INSERT INTO {$table_prefix}mlm_rightleg set pkey='" . $result->parent_key . "',ukey='" . $user_key . "' ";
                                    $tbright = $wpdb->query($tbright);
                                    if ($u = get_option('network_mail', true) == 1) {
                                    }
                                }
                                else {
                                    $tbleft = "INSERT INTO {$table_prefix}mlm_leftleg set pkey='" . $result->parent_key . "',ukey='" . $user_key . "' ";
                                    $tbleft = $wpdb->query($tbleft);
                                    if ($u = get_option('network_mail', true) == 1) {
                                    }
                                }
                            }
                            $parent_key = $result->parent_key;
                        }
                        else {
                            $parent_key = '0';
                        }
                    }//end while loop
                    if (isset($epin) && !empty($epin)) {
                        $sql = "update {$table_prefix}mlm_epins set user_key='{$user_key}', date_used='" . current_time('mysql') . "', status=1 where epin_no ='{$epin}' ";
                        $wpdb->query($sql);
                        }
                    if ($paymentStatus == 1) {
                            insert_refferal_commision($user_id);
                        }
                    if (is_plugin_active('mlm-paypal-mass-pay/load-data.php')) {

                        update_user_meta($user_id, 'mlm_user_paypalid', $paypalId, FALSE);
                    }
                    

                    $chk = '';
                    $msg = "<span style='color:green;'>Congratulations! You have successfully registered in the system.</span>";
                }//end most inner if condition
            } //end inner if condition
            else
                $error = "\n Sponsor does not exist in the system.";
        }//end outer if condition
    }//end most outer if condition
    //if any error occoured
    if (!empty($error))
        $error = nl2br($error);

    if ($chk != '') {

        include 'js-validation-file.html';
        ?>

        <?php
        $user_roles = $current_user->roles;
        $user_role = array_shift($user_roles);
        $general_setting = get_option('wp_mlm_general_settings');
        if (is_user_logged_in()) {
            if (!empty($general_setting['wp_reg']) && !empty($general_setting['reg_url']) && $user_role != 'mlm_user') {
                echo "<script>window.location ='" . site_url() . '/' . $general_setting['reg_url'] . "'</script>";
            }
        }
        else {
            if (!empty($general_setting['wp_reg']) && !empty($general_setting['reg_url'])) {
                echo "<script>window.location ='" . site_url() . '/' . $general_setting['reg_url'] . "'</script>";
            }
        }
        ?>

        <span style='color:red;'><?php echo $error ?></span>
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <form name="frm" method="post" action="" onSubmit="return formValidation();">
                <tr>
                    <td><?php _e('Create Username', 'binary-mlm-pro'); ?><span style="color:red;">*</span> :</td>
                    <td><input type="text" name="username" id="username" value="<?php if (!empty($_POST['username'])) _e(htmlentities($_POST['username'])); ?>" maxlength="20" size="37" onBlur="checkUserNameAvailability(this.value);"><br /><div id="check_user"></div></td>
                </tr>
                <?php
                $mlm_general_settings = get_option('wp_mlm_general_settings');
                if (!empty($mlm_general_settings['ePin_activate']) && !empty($mlm_general_settings['sol_payment'])) {
                    ?>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td><?php _e('Enter ePin', 'binary-mlm-pro'); ?><span style="color:red;">*</span> :</td>
                        <td><input type="text" name="epin" id="epin" value="<?php if (!empty($_POST['epin'])) _e(htmlentities($_POST['epin'])); ?>" maxlength="20" size="37" onBlur="checkePinAvailability(this.value);"><br /><div id="check_epin"></div></td>
                    </tr>
                <?php } else if (!empty($mlm_general_settings['ePin_activate'])) { ?>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td><?php _e('Enter ePin', 'binary-mlm-pro'); ?> :</td>
                        <td><input type="text" name="epin" id="epin" value="<?php if (!empty($_POST['epin'])) _e(htmlentities($_POST['epin'])); ?>" maxlength="20" size="37" onBlur="checkePinAvailability(this.value);"><br /><div id="check_epin"></div></td>
                    </tr>
                    <?php
                }
                if (empty($mlm_general_settings['sol_payment'])) {
                    ?>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td><?php _e('Product', 'binary-mlm-pro'); ?> :</td>
                        <td> <?php $pro_price_settings = $wpdb->get_results("select * from {$table_prefix}mlm_product_price where p_id!='1'"); ?>

                            <select name="epin_value" id="epin_value" >
                                <option value="">Select Product</option>
                                <?php foreach ($pro_price_settings as $pricedetail) { ?>       
                                    <option value="<?php echo $pricedetail->p_id ?>" <?php echo ($epin_value == $pricedetail->p_id ? 'selected="selected"' : ''); ?>><?php echo $pricedetail->product_name ?></option>
                                <?php } ?>
                            </select></td>
                    </tr>
                <?php } ?>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td><?php _e('Create Password', 'binary-mlm-pro') ?> <span style="color:red;">*</span> :</td>
                    <td>	<input type="password" name="password" id="password" maxlength="20" size="37" >
                        <br /><span style="font-size:12px; font-style:italic; color:#006633"><?php _e('Password length atleast 6 character', 'binary-mlm-pro'); ?></span>
                    </td>
                </tr>

                <tr><td colspan="2">&nbsp;</td></tr>

                <tr>
                    <td><?php _e('Confirm Password', 'binary-mlm-pro') ?>  <span style="color:red;">*</span> :</td>
                    <td><input type="password" name="confirm_password" id="confirm_password" maxlength="20" size="37" ></td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td><?php _e('Email Address', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                    <td><input type="text" name="email" id="email" value="<?php if (!empty($_POST['email'])) _e(htmlentities($_POST['email'])); ?>"  size="37" ></td>
                </tr>

                <tr><td colspan="2">&nbsp;</td></tr><tr>

                <tr>
                    <td><?php _e('Confirm Email Address', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                    <td><input type="text" name="confirm_email" id="confirm_email" value="<?php if (!empty($_POST['confirm_email'])) _e(htmlentities($_POST['confirm_email'])); ?>" size="37" ></td>
                </tr>

                <tr><td colspan="2">&nbsp;</td></tr>
                <?php if (is_plugin_active('mlm-paypal-mass-pay/load-data.php')) { ?>
                    <tr>
                        <td><?php _e('Paypal ID', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                        <td><input type="text" name="paypal_id" id="paypal_id" value="<?php if (!empty($_POST['paypal_id'])) _e(htmlentities($_POST['paypal_id'])); ?>" size="37" ></td>
                    </tr>

                    <tr><td colspan="2">&nbsp;</td></tr>
                <?php } ?>	
                <tr>
                    <td><?php _e('First Name', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                    <td><input type="text" name="firstname" id="firstname" value="<?php if (!empty($_POST['firstname'])) _e(htmlentities($_POST['firstname'])); ?>" maxlength="20" size="37" onBlur="return checkname(this.value, 'firstname');" ></td>
                </tr>

                <tr><td colspan="2">&nbsp;</td></tr>

                <tr>
                    <td><?php _e('Last Name', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                    <td><input type="text" name="lastname" id="lastname" value="<?php if (!empty($_POST['lastname'])) _e(htmlentities($_POST['lastname'])); ?>" maxlength="20" size="37" onBlur="return checkname(this.value, 'lastname');"></td>
                </tr>

                <tr><td colspan="2">&nbsp;</td></tr>

                <tr>
                    <?php
                    if (isset($sponsor_name) && $sponsor_name != '') {
                        $spon = $sponsor_name;
                    }
                    else if (isset($sp_name))
                        $spon = $sp_name;
                    else if (isset($_POST['sponsor']))
                        $spon = htmlentities($_POST['sponsor']);
                    ?>
                    <td><?php _e('Sponsor Name', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                    <td>
                        <input type="text" name="sponsor" id="sponsor" value="<?php if (!empty($spon)) _e($spon); ?>" maxlength="20" size="37" onBlur="checkReferrerAvailability(this.value);" <?php echo $readonly_sponsor; ?>>
                        <br /><div id="check_referrer"></div>
                    </td>
                </tr>

                <tr><td colspan="2">&nbsp;</td></tr>

                <tr>
                    <td><?php _e('Placement', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                    <?php
                    if (isset($_POST['leg']) && $_POST['leg'] == '0') {
                        $checked = 'checked';
                    }
                    else if (isset($_GET['l']) && $_GET['l'] == '0') {
                        $checked = 'checked';
                        $disable_leg = 'disabled';
                    }
                    else
                        $checked = '';

                    if (isset($_POST['leg']) && $_POST['leg'] == '1') {
                        $checked1 = 'checked';
                    }
                    else if (isset($_GET['l']) && $_GET['l'] == '1') {
                        $checked1 = 'checked';
                        $disable_leg = 'disabled';
                    }
                    else
                        $checked1 = '';
                    ?>

                    <td><?php echo __('Left', 'binary-mlm-pro') ?> <input id="left" type="radio" name="leg" value="0" <?php echo $checked; ?> <?php if (!empty($disable_leg)) _e($disable_leg); ?>/>
                        <?php echo __('Right', 'binary-mlm-pro') ?><input id="right" type="radio" name="leg" value="1" <?php echo $checked1; ?> <?php if (!empty($disable_leg)) _e($disable_leg); ?>/>



                    </td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" name="submit" id="submit" value="<?php _e('Submit', 'binary-mlm-pro') ?>" /></td>
                </tr>
            </form>
        </table>
        <?php
    }
    else
        _e($msg);
}

//function end
?>