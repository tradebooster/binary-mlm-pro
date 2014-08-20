<?php
require_once("php-form-validation.php");

function mlm_change_password() {
    $error = '';
    global $current_user;
    get_currentuserinfo();
    $sponsor_name = $current_user->user_login;

    //most outer if condition
    if (isset($_POST['submit'])) {
        $password = sanitize_text_field($_POST['password']);
        $confirm_pass = sanitize_text_field($_POST['confirm_password']);

        if (checkInputField($password))
            $error .= "\n Please enter your new password.";

        if (confirmPassword($password, $confirm_pass))
            $error .= "\n Your confirm password does not match.";

        // inner if condition
        if (empty($error)) {
            $user = array
                (
                'ID' => $current_user->ID,
                'user_pass' => $password,
            );

            // return the wp_users table inserted user's ID
            $user_id = wp_update_user($user);

            $msg = "<span style='color:green;'>Congratulations! Your password has been successfully updated.</span>";
        }//end inner if condition
    }//end most outer if condition
    //if any error occoured
    if (!empty($error))
        $error = nl2br($error);

    if (!empty($msg))
        _e($msg);
    include 'js-validation-file.html';
    ?>
    <!--<script type="text/javascript" src="<?php //echo plugins_url().'/'.MLM_PLUGIN_NAME.'/js/form-validation.js'          ?>"></script>-->

    <span style='color:red;'><?php echo $error ?></span>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <form name="frm" method="post" action="" onSubmit="return updatePassword();">
            <tr>
                <td><?php _e('New Password', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                <td>	<input type="password" name="password" id="password" maxlength="20" size="37" >
                    <br /><span style="font-size:12px; font-style:italic; color:#006633"><?php _e('Password length atleast 6 character', 'binary-mlm-pro'); ?></span>
                </td>
            </tr>

            <tr><td colspan="2">&nbsp;</td></tr>

            <tr>
                <td><?php _e('Type Again', 'binary-mlm-pro'); ?><span style="color:red;">*</span> :</td>
                <td>	<input type="password" name="confirm_password" id="confirm_password" maxlength="20" size="37" >
                </td>
            </tr>

            <tr><td colspan="2">&nbsp;</td></tr>

            <tr>
                <td colspan="2"><input type="submit" name="submit" id="submit" value="<?php _e('Submit', 'binary-mlm-pro') ?>" /></td>
            </tr>
        </form>
    </table>
    <?php
}

//function end
?>