<?php
require_once("php-form-validation.php");

function join_network() {
    global $wpdb, $current_user;
    $user_id = $current_user->ID;
    $table_prefix = mlm_core_get_table_prefix();
    $error = '';
    $chk = 'error';


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
        $sp_name = $wpdb->get_var("select username from {$table_prefix}mlm_users where user_key='" . $_REQUEST['sp'] . "'");
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

    /*     * ****date format ***** */

    $date_format = get_option('date_format');
    $time_format = get_option('time_format');



    /*     * ****** end******* */

    global $current_user;
    get_currentuserinfo();
    $mlm_general_settings = get_option('wp_mlm_general_settings');
    if (isset($_REQUEST['sp']) && $_REQUEST['sp'] != '') {
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


    //most outer if condition
    if (isset($_POST['submit'])) {

        $firstname = sanitize_text_field($_POST['firstname']);
        $lastname = sanitize_text_field($_POST['lastname']);
        $email = sanitize_text_field($_POST['email']);
        $sponsor = sanitize_text_field($_POST['sponsor']);
        if (empty($sponsor)) {
            $sponsor = $wpdb->get_var("select `username` FROM {$table_prefix}mlm_users order by id asc limit 1");
        }

        if (checkInputField($firstname))
            $error .= "\n Please enter your first name.";

        if (checkInputField($lastname))
            $error .= "\n Please enter your last name.";

        if (!is_email($email))
            $error .= "\n E-mail address is invalid.";


        //Add usernames we don't want used
        $invalid_usernames = array('admin');
        //Do username validation
        $sql = "SELECT COUNT(*) num, `user_key` 
				FROM {$table_prefix}mlm_users 
				WHERE `username` = '" . $sponsor . "'";
        $intro = $wpdb->get_row($sql);


        if (isset($_GET['l']) && $_GET['l'] != '') {
            $leg = $_GET['l'];
        }
        else {
            $leg = $_POST['leg'];
        }

        if (isset($leg) && $leg != '0') {
            if ($leg != '1') {
                $error .= "\n You have enter a wrong placement.";
            }
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
        //generate random numeric key for new user registration
        $user_key = generateKey();
        //if generated key is already exist in the DB then again re-generate key
        do {
            $check = $wpdb->get_var("SELECT COUNT(*) ck FROM {$table_prefix}mlm_users 
                                     WHERE `user_key` = '" . $user_key . "'");
            $flag = 1;
            if ($check == 1) {
                $user_key = generateKey();
                $flag = 0;
            }
        } while ($flag == 0);

        //check parent key exist or not

        if (isset($_GET['k']) && $_GET['k'] != '') {
            if (!checkKey($_GET['k'])) {
                $error .= "\n Parent key does't exist.";
            }
            // check if the user can be added at the current position
            $checkallow = checkallowed($_GET['k'], $leg);
            if ($checkallow >= 1) {
                $error .= "\n You have enter a wrong placement.";
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
                        $sql = "SELECT `user_key` FROM {$table_prefix}mlm_users 
                                WHERE parent_key = '" . $sponsor1 . "' AND 
                                leg = '" . $leg . "' AND banned = '0'";
                        $spon = $wpdb->get_var($sql);
                        $num = $wpdb->num_rows;
                        if ($num) {
                            $sponsor1 = $spon;
                        }
                    } while ($num == 1);
                    $parent_key = $sponsor1;
                }

                // return the wp_users table inserted user's ID
                $user = array
                    (
                    'ID' => $user_id,
                    'first_name' => $firstname,
                    'last_name' => $lastname,
                    'user_email' => $email,
                    'role' => 'mlm_user'
                );

                // return the wp_users table inserted user's ID
                $user_id = wp_update_user($user);
                $username = $current_user->user_login;


                //get the selected country name from the country table

                /* Send e-mail to admin and new user - 
                  You could create your own e-mail instead of using this function */

                /**                 * ****** product Price set *************** */
                if (!empty($mlm_general_settings['product_price'])) {
                    $pc = $mlm_general_settings['product_price'];
                }
                else {
                    $pc = '0';
                }

                //insert the data into fa_user table

                if (!empty($epin)) {
                    $pointStatus = $wpdb->get_row("select point_status from {$table_prefix}mlm_epins where epin_no = '{$epin}'", ARRAY_N);
                    // to epin point status 1 
                    if ($pointStatus[0] == '1') {
                        $paymentStatus = '1';
                        $product_price = $pc;
                    }
                    // to epin point status 1 
                    else if ($pointStatus[0] == '0') {
                        $paymentStatus = '2';
                        $product_price = '0';
                    }
                }
                else { // to non epin 
                    $paymentStatus = '0';
                    $product_price = '0';
                }

                $insert = "INSERT INTO {$table_prefix}mlm_users(
			user_id, username, user_key, parent_key, sponsor_key, leg,payment_status,product_price) 
			VALUES(
			'" . $user_id . "','" . $username . "', '" . $user_key . "', '" . $parent_key . "', '" . $sponsor . "', '" . $leg . "','" . $paymentStatus . "','" . $product_price . "')";

                // if all data successfully inserted
                if ($wpdb->query($insert)) { //begin most inner if condition
                    //entry on Left and Right Leg tables
                    if ($leg == 0) {
                        $insert = "INSERT INTO {$table_prefix}mlm_leftleg  (pkey, ukey) 
				VALUES ('" . $parent_key . "','" . $user_key . "')";
                        $insert = $wpdb->query($insert);
                    }
                    else if ($leg == 1) {
                        $insert = "INSERT INTO {$table_prefix}mlm_rightleg(pkey, ukey) 
				VALUES('" . $parent_key . "','" . $user_key . "')";
                        $insert = $wpdb->query($insert);
                    }
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
                                    $tbright = "INSERT INTO {$table_prefix}mlm_rightleg (pkey,ukey) 
						VALUES('" . $result->parent_key . "','" . $user_key . "')";
                                    $tbright = $wpdb->query($tbright);
                                }
                                else {
                                    $tbleft = "INSERT INTO {$table_prefix}mlm_leftleg (pkey, ukey) 
						VALUES('" . $result->parent_key . "','" . $user_key . "')";
                                    $tbleft = $wpdb->query($tbleft);
                                }
                            }
                            $parent_key = $result->parent_key;
                        }
                        else {
                            $parent_key = '0';
                        }
                    }//end while loop
                    if (isset($epin) && !empty($epin)) {
                        $sql = "update {$table_prefix}mlm_epins set user_key='{$user_key}', date_used=now(), status=1 where epin_no ='{$epin}' "; // Update epin according user_key (19-07-2013)

                        $wpdb->query($sql);
                        if ($paymentStatus == 1) {
                            insert_refferal_commision($user_id, $sponsor, $user_key);
                        }
                    }
                    $chk = '';
                    $msg = "<span style='color:green;'>Congratulations! You have successfully Join MLM</span>";
                }//end most inner if condition
            } //end inner if condition
            else {
                $error = "\n Sponsor does not exist in the system.";
            }
        }//end outer if condition
    }//end most outer if condition
    //if any error occoured
    if (!empty($error)) {
        $error = nl2br($error);
    }

    if ($chk != '') {
        include 'js-validation-file.html';
        ?>
        <?php
        if ($current_user->roles[0] == 'mlm_user') {
            echo "Your are MLM user";
        }
        else {
            $_POST['firstname'] = get_user_meta($user_id, 'first_name', true);
            $_POST['lastname'] = get_user_meta($user_id, 'last_name', true);
            $_POST['email'] = $current_user->user_email;
            ?>
            <script>
                function checkspname() {
                    var spname = document.getElementById('sponsor').value;
                    if (spname == '') {
                        if (!confirm('Are you sure you do not know your Sponsor\'s username? Proceed without a Sponsor?')) {
                            return false;
                        }
                    }
                }
            </script>
            <span style='color:red;'><?php echo $error ?></span>
            <?php if (isset($msg) && $msg != "") echo $msg; ?>
            <form name="frm" method="post" action="" onSubmit="checkspname()">			
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr><td colspan="2">&nbsp;</td></tr>

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
                        <td><?php _e('Email Address', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                        <td><input type="text" name="email" id="email" value="<?php if (!empty($_POST['email'])) _e(htmlentities($_POST['email'])); ?>"  size="37" ></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td><?php _e('Sponsor Name', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                        <td>
                            <input type="text" name="sponsor" id="sponsor" value="<?php if (!empty($_POST['sponsor'])) _e(htmlentities($_POST['sponsor'])); ?>" maxlength="20" size="37" onkeyup="checkReferrerAvailability12(this.value);">
                            <br /><div id="check_referrer"></div>
                        </td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td><?php _e('Placement', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                        <td><?php echo __('Left', 'binary-mlm-pro') ?> <input id="left" type="radio" name="leg" value="0" <?php echo (isset($led) && $leg == '0') ? 'checked="checked"' : ''; ?> />
                            <?php echo __('Right', 'binary-mlm-pro') ?><input id="right" type="radio" name="leg" value="1" <?php echo (isset($led) && $leg == '0') ? 'checked="checked"' : ''; ?>/>
                        </td>

                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td colspan="2"><input type="submit" name="submit" id="submit" value="<?php _e('Submit', 'binary-mlm-pro') ?>" /></td>
                    </tr>
                </table>
            </form>
            <?php
        }
        ?>	
        <?php
    }
    else {
        _e($msg);
    }
}

//function end
