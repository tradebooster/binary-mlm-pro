<?php
require_once("php-form-validation.php");

function mlm_update_profile($id = '') {
echo "<script>window.location='".site_url()."'</script>";

// Echo count of items in menu

    global $wpdb;
    $table_prefix = mlm_core_get_table_prefix();
    $error = '';
    global $current_user;
    get_currentuserinfo();
    if ($id == '') {
        //$sponsor_name = $current_user->user_login;
        $userId = $current_user->ID;
        $class = '';
    }
    else {
        $userId = $id;
        $class = "class='button-primary'";
    }

    //most outer if condition
    if (isset($_POST['submit'])) {
        $firstname = sanitize_text_field($_POST['firstname']);
        $lastname = sanitize_text_field($_POST['lastname']);
        $address1 = sanitize_text_field($_POST['address1']);
        $address2 = sanitize_text_field($_POST['address2']);
        $city = sanitize_text_field($_POST['city']);
        $state = sanitize_text_field($_POST['state']);
        $postalcode = sanitize_text_field($_POST['postalcode']);
        $telephone = sanitize_text_field($_POST['telephone']);
        $dob = sanitize_text_field($_POST['dob']);

        if (checkInputField($firstname))
            $error .= "\n Please enter your first name.";

        if (checkInputField($lastname))
            $error .= "\n Please enter your last name.";

        if (checkInputField($address1))
            $error .= "\n Please enter your address.";

        if (checkInputField($city))
            $error .= "\n Please enter your city.";

        if (checkInputField($state))
            $error .= "\n Please enter your state.";

        if (checkInputField($postalcode))
            $error .= "\n Please enter your postal code.";

        if (checkInputField($telephone))
            $error .= "\n Please enter your contact number.";

        if (checkInputField($dob))
            $error .= "\n Please enter your date of birth.";

        // inner if condition
        if (empty($error)) {
            $user = array
                (
                'ID' => $userId,
                'first_name' => $firstname,
                'last_name' => $lastname,
            );

            // return the wp_users table inserted user's ID
            $user_id = wp_update_user($user);

            //get the selected country name from the country table
            $country = $_POST['country'];
            $sql = "SELECT name 
						FROM {$table_prefix}mlm_country
						WHERE id = '" . $country . "'";
            $country1 = $wpdb->get_var($sql);

            //insert the registration form data into user_meta table
            update_user_meta($user_id, 'user_address1', $address1, FALSE); // I have replace FALSE to $unique
            update_user_meta($user_id, 'user_address2', $address2, FALSE);
            update_user_meta($user_id, 'user_city', $city, FALSE);
            update_user_meta($user_id, 'user_state', $state, FALSE);
            update_user_meta($user_id, 'user_country', $country1, FALSE);
            update_user_meta($user_id, 'user_postalcode', $postalcode, FALSE);
            update_user_meta($user_id, 'user_telephone', $telephone, FALSE);
            update_user_meta($user_id, 'user_dob', $dob, FALSE);
            $msg = "<span style='color:green;'>Congratulations! Profile has been successfully updated.</span>";
        }//end inner if condition
    }//end most outer if condition
    //if any error occoured
    if (!empty($error))
        $error = nl2br($error);
    $user_info = get_userdata($userId);
    if (!empty($msg))
        _e($msg);

    include 'js-validation-file.html';
    ?>

    <script type="text/javascript">
        var popup1, popup2, splofferpopup1;
        var bas_cal, dp_cal1, dp_cal2, ms_cal; // declare the calendars as global variables 
        window.onload = function() {
            dp_cal1 = new Epoch('dp_cal1', 'popup', document.getElementById('dob'));
        };
    </script>
    <span style='color:red;'><?php echo $error ?></span>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <form name="frm" method="post" action="" onSubmit="return updateFormValidation();">
            <tr>
                <td><?php _e('First Name', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                <td><input type="text" name="firstname" id="firstname" value="<?php echo $user_info->first_name; ?>" maxlength="20" size="37" onBlur="return checkname(this.value, 'firstname');" ></td>
            </tr>

            <tr><td colspan="2">&nbsp;</td></tr>

            <tr>
                <td><?php _e('Last Name', 'binary-mlm-pro'); ?><span style="color:red;">*</span> :</td>
                <td><input type="text" name="lastname" id="lastname" value="<?php echo $user_info->last_name; ?>" maxlength="20" size="37" onBlur="return checkname(this.value, 'lastname');"></td>
            </tr>

            <tr><td colspan="2">&nbsp;</td></tr>

            <tr>
                <td><?php _e('Address Line 1', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                <td><input type="text" name="address1" id="address1" value="<?php echo $user_info->user_address1; ?>"  size="37" onBlur="return allowspace(this.value, 'address1');"></td>
            </tr>

            <tr><td colspan="2">&nbsp;</td></tr>

            <tr>
                <td><?php _e('Address Line 2', 'binary-mlm-pro'); ?> :</td>
                <td><input type="text" name="address2" id="address2" value="<?php echo $user_info->user_address2; ?>"  size="37" onBlur="return allowspace(this.value, 'address2');"></td>
            </tr>

            <tr><td colspan="2">&nbsp;</td></tr>

            <tr>
                <td><?php _e('City', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                <td><input type="text" name="city" id="city" value="<?php echo $user_info->user_city; ?>" maxlength="30" size="37" onBlur="return allowspace(this.value, 'city');"></td>
            </tr>

            <tr><td colspan="2">&nbsp;</td></tr>

            <tr>
                <td><?php _e('State', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                <td><input type="text" name="state" id="state" value="<?php echo $user_info->user_state; ?>" maxlength="30" size="37" onBlur="return allowspace(this.value, 'state');"></td>
            </tr>

            <tr><td colspan="2">&nbsp;</td></tr>

            <tr>
                <td><?php _e('Postal Code', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                <td><input type="text" name="postalcode" id="postalcode" value="<?php echo $user_info->user_postalcode; ?>" maxlength="20" size="37" onBlur="return numeric(this.value, 'postalcode');"></td>
            </tr>

            <tr><td colspan="2">&nbsp;</td></tr>

            <tr>
                <td><?php _e('Country', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                <td>
                    <?php
                    $sql = "SELECT id, name
							FROM {$table_prefix}mlm_country
							ORDER BY name";
                    $rows = $wpdb->get_results($sql);
                    ?>
                    <select name="country" id="country" >
                        <option value="">Select Country</option>
                        <?php
                        foreach ($rows as $row) {
                            if ($user_info->user_country == $row->name)
                                $selected = 'selected';
                            else
                                $selected = '';
                            ?>
                            <option value="<?php echo $row->id; ?>" <?php echo $selected ?>><?php echo $row->name; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr><td colspan="2">&nbsp;</td></tr><tr>

            <tr>
                <td><?php _e('Contact No', 'binary-mlm-pro'); ?>. <span style="color:red;">*</span> :</td>
                <td><input type="text" name="telephone" id="telephone" value="<?php echo $user_info->user_telephone; ?>" maxlength="20" size="37" onBlur="return numeric(this.value, 'telephone');" ></td>
            </tr>

            <tr><td colspan="2">&nbsp;</td></tr>

            <tr>
                <td><?php _e('Date of Birth', 'binary-mlm-pro'); ?> <span style="color:red;">*</span> :</td>
                <td><input type="text" name="dob" id="dob" value="<?php echo $user_info->user_dob; ?>" maxlength="20" size="22" ></td>
            </tr>

            <tr><td colspan="2">&nbsp;</td></tr>

            <tr>
                <td colspan="2"><input type="submit" name="submit" id="submit" value="Update" <?php echo $class ?> /></td>
            </tr>
        </form>
    </table>
    <?php
}

//function end
?>