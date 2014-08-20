<?php
require_once("php-form-validation.php");

function register_first_user() {
    global $wpdb;
    //get database table prefix
    $table_prefix = mlm_core_get_table_prefix();

    $error = '';
    $chk = 'error';

    //most outer if condition
    if (isset($_POST['submit'])) {

        $username = sanitize_text_field($_POST['username']);
        $password = sanitize_text_field($_POST['password']);
        $confirm_pass = sanitize_text_field($_POST['confirm_password']);
        $email = sanitize_text_field($_POST['email']);
        $confirm_email = sanitize_text_field($_POST['confirm_email']);
        $firstname = sanitize_text_field($_POST['first_name']);
        $lastname = sanitize_text_field($_POST['last_name']);

        //Add usernames we don't want used
        $invalid_usernames = array('admin');

        //Do username validation
        $username = sanitize_user($username);

        if (!validate_username($username) || in_array($username, $invalid_usernames))
            $error .= "\n Username is invalid.";

        if (username_exists($username))
            $error .= "\n Username already exists.";

        if (checkInputField($username))
            $error .= "\n Please enter your username.";

        if (checkInputField($password))
            $error .= "\n Please enter your password.";

        if (confirmPassword($password, $confirm_pass))
            $error .= "\n Please confirm your password.";

        //Do e-mail address validation
        if (!is_email($email))
            $error .= "\n E-mail address is invalid.";

        if (email_exists($email))
            $error .= "\n E-mail address is already in use.";

        if (confirmEmail($email, $confirm_email))
            $error .= "\n Please confirm your email address.";

        //generate random numeric key for new user registration
        $user_key = generateKey();

        // outer if condition
        if (empty($error)) {
            $user = array
                (
                'user_login' => $username,
                'user_pass' => $password,
                'user_email' => $email,
                'first_name' => $firstname,
                'last_name' => $lastname,
                'role' => 'mlm_user'
            );

            // return the wp_users table inserted user's ID
            $user_id = wp_insert_user($user);

            /* Send e-mail to admin and new user - 
              You could create your own e-mail instead of using this function */
            wp_new_user_notification($user_id, $password);

            //insert the data into fa_user table
            $insert = "INSERT INTO {$table_prefix}mlm_users
						   					(
																user_id, username, user_key, parent_key, sponsor_key, leg, payment_status
													) 
													VALUES
													(
															'" . $user_id . "','" . $username . "', '" . $user_key . "', '0', '0', '0','1'
													)";

            // if all data successfully inserted
            if ($wpdb->query($insert)) {
                $chk = '';
                //$msg = "<span style='color:green;'>Congratulations! You have successfully registered in the system.</span>";
            }
        }//end outer if condition
    }//end most outer if condition
    //if any error occoured
    if (!empty($error))
        $error = nl2br($error);

    if ($chk != '') {
        include 'js-validation-file.html';
        ?>
        <div class='wrap'>
            <h2><?php _e('Create First User in Network', 'binary-mlm-pro'); ?></h2>
            <div class="notibar msginfo">
                <a class="close"></a>
                <p><?php _e('In order to begin building your network you would need to register the First User of the network. All other users would be registered under this First User.', 'binary-mlm-pro'); ?></p>
            </div>
            <?php if ($error) : ?>
                <div class="notibar msgerror">
                    <a class="close"></a>
                    <p> <strong><?php _e('Please Correct the following Error(s)', 'binary-mlm-pro'); ?>:</strong> <?php _e($error); ?></p>
                </div>
            <?php endif; ?>

            <p>&nbsp;</p>
            <form name="frm" method="post" action="" onSubmit="return adminFormValidation();">
                <table border="0" cellpadding="0" cellspacing="0" width="100%"  class="form-table">

                    <tr>
                        <th scope="row" class="admin-settings">
                            <a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('create-first-user');">
                                <?php _e('Create Username', 'binary-mlm-pro'); ?> <span style="color:red;">*</span>: </a>
                        </th>
                        <td>
                            <input type="text" name="username" id="username" value="<?php if (!empty($_POST['username'])) _e(htmlentities($_POST['username'])) ?>" maxlength="20" size="37">
                            <div class="toggle-visibility" id="create-first-user"><?php _e('Please create the first user of the your network.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" class="admin-settings">
                            <a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('create-password');"></a>
                            <?php _e('Create Password', 'binary-mlm-pro'); ?> <span style="color:red;">*</span>: </a>
                        </th>
                        <td><input type="password" name="password" id="password" maxlength="20" size="37" >
                            <div class="toggle-visibility" id="create-password"><?php _e('Password length is atleast 6 char.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" class="admin-settings">
                            <a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('confirm-password');">
                                <?php _e('Confirm Password', 'binary-mlm-pro'); ?> <span style="color:red;">*</span>: </a>
                        </th>
                        <td>
                            <input type="password" name="confirm_password" id="confirm_password" maxlength="20" size="37" >
                            <div class="toggle-visibility" id="confirm-password"><?php _e('Please confirm your password.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" class="admin-settings">
                            <a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('email-address');">
                                <?php _e('Email Address', 'binary-mlm-pro'); ?> <span style="color:red;">*</span>: </a>
                        </th>
                        <td>
                            <input type="text" name="email" id="email" value="<?php if (!empty($_POST['email'])) _e(htmlentities($_POST['email'])); ?>"  size="37" >
                            <div class="toggle-visibility" id="email-address"><?php _e('Please specify your email address.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            <a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('confirm-address');">
                                <?php _e('Confirm Email Address', 'binary-mlm-pro'); ?> <span style="color:red;">*</span>: </a>
                        </th>
                        <td>
                            <input type="text" name="confirm_email" id="confirm_email" value="<?php if (!empty($_POST['confirm_email'])) _e(htmlentities($_POST['confirm_email'])); ?>" size="37" >
                            <div class="toggle-visibility" id="confirm-address"><?php _e('Please confirm your email address.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('first-name');">
                                <?php _e('First Name', 'binary-mlm-pro'); ?> 
                        </th>
                        <td>
                            <input type="text" name="first_name" id="first_name" value="<?php if (!empty($_POST['first_name'])) _e(htmlentities($_POST['first_name'])); ?>" size="37" >
                            <div class="toggle-visibility" id="first-name"><?php _e('Please enter your first name.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('last-name');">
                                <?php _e('Last Name', 'binary-mlm-pro'); ?>  </a>
                        </th>
                        <td>
                            <input type="text" name="last_name" id="last_name" value="<?php if (!empty($_POST['last_name'])) _e(htmlentities($_POST['last_name'])); ?>" size="37" >
                            <div class="toggle-visibility" id="last-name"><?php _e('Please confirm your last name.', 'binary-mlm-pro'); ?></div>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="submit" id="submit" value="<?php _e('Submit', 'binary-mlm-pro') ?>" class='button-primary' onclick="needToConfirm = false;"/>
                </p>
            </form>
        </div>	
        <script language="JavaScript">
            populateArrays();
        </script>
        <?php
    }
    else
        _e("<script>window.location='admin.php?page=admin-settings&tab=general&msg=s'</script>");
}

//function end
?>