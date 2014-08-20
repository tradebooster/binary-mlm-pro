<?php

function my_show_extra_profile_fields($user) {
    global $wpdb;
    $table_prefix = mlm_core_get_table_prefix();


    include 'js-validation-file.html';
    ?>

    <script type="text/javascript">
        var popup1, popup2, splofferpopup1;
        var bas_cal, dp_cal1, dp_cal2, ms_cal; // declare the calendars as global variables 
        window.onload = function() {
            dp_cal1 = new Epoch('dp_cal1', 'popup', document.getElementById('user_dob'));
        };
    </script>
    <h3>Extra profile information</h3>

    <table class="form-table">
        <tr>
            <th><label for="user_address1">Address1 <span class="description">(required)</span></label></th>
            <td>
                <input type="text" name="user_address1" id="user_address1" value="<?php _e(esc_attr(get_the_author_meta('user_address1', $user->ID))); ?>" class="regular-text" required = true /><br />
                <!-- <span class="description">You edit here your address.</span>  -->
            </td>
        </tr>

        <tr>
            <th><label for="user_address2">Address2</label></th>
            <td>
                <input type="text" name="user_address2" id="user_address2" value="<?php _e(esc_attr(get_the_author_meta('user_address2', $user->ID))); ?>" class="regular-text" /><br />
                <!-- <span class="description">Please edit here your address.</span> -->
            </td>
        </tr>

        <tr>
            <th><label for="user_city">City <span class="description">(required)</span></label></th>
            <td>
                <input type="text" name="user_city" id="user_city" value="<?php _e(esc_attr(get_the_author_meta('user_city', $user->ID))); ?>" class="regular-text" required = true /><br />
                <!-- <span class="description">Please edit here your address.</span> -->
            </td>
        </tr>

        <tr>
            <th><label for="user_state">State <span class="description">(required)</span></label></th>
            <td>
                <input type="text" name="user_state" id="user_state" value="<?php _e(esc_attr(get_the_author_meta('user_state', $user->ID))); ?>" class="regular-text" required = true /><br />
                <!-- <span class="description">Please edit here your address.</span> -->
            </td>
        </tr>

        <tr>
            <th><label for="user_postalcode">Postal Code <span class="description">(required)</span></label></th>
            <td>
                <input type="text" name="user_postalcode" id="user_postalcode" value="<?php _e(esc_attr(get_the_author_meta('user_postalcode', $user->ID))); ?>" class="regular-text" required = true /><br />
                <!-- <span class="description">Please edit here your address.</span> -->
            </td>
        </tr>

        <tr>
            <th><label for="user_country">Country <span class="description">(required)</span></label></th>
            <td>
                <?php
                $sql = "SELECT name
							FROM {$table_prefix}mlm_country
							ORDER BY name";
                $results = $wpdb->get_results($sql);
                ?>
                <select name="user_country" id="user_country" required = true >
                    <option value="">Select Country</option>
                    <?php
                    foreach ($results as $row) {
                        if (esc_attr(get_the_author_meta('user_country', $user->ID)) == $row->name)
                            $selected = 'selected';
                        else
                            $selected = '';
                        ?>
                        <option value="<?php echo $row->name; ?>" <?php echo $selected ?>><?php echo $row->name; ?></option>
                        <?php
                    }
                    ?>
                </select>
                <!-- <span class="description">Please edit here your address.</span> -->
            </td>
        </tr>

        <tr>
            <th><label for="user_telephone">Contact No. <span class="description">(required)</span></label></th>
            <td>
                <input type="text" name="user_telephone" id="user_telephone" value="<?php _e(esc_attr(get_the_author_meta('user_telephone', $user->ID))); ?>" class="regular-text" required = true /><br />
                <!-- <span class="description">Please edit here your address.</span> -->
            </td>
        </tr>

        <tr>
            <th><label for="user_dob">Date of Birth <span class="description">(required)</span></label></th>
            <td>
                <input type="text" name="user_dob" id="user_dob" value="<?php _e(esc_attr(get_the_author_meta('user_dob', $user->ID))); ?>" class="regular-text" required = true /><br />
                <!-- <span class="description">Please edit here your address.</span> -->
            </td>
        </tr>

    </table>
    <?php
}

function my_save_extra_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id))
        return false;

    /* Copy and paste this line for additional fields. Make sure to change 'user_address1' to the field ID. */
    update_usermeta($user_id, 'user_address1', $_POST['user_address1']);
    update_usermeta($user_id, 'user_address2', $_POST['user_address2']);
    update_usermeta($user_id, 'user_city', $_POST['user_city']);
    update_usermeta($user_id, 'user_state', $_POST['user_state']);
    update_usermeta($user_id, 'user_postalcode', $_POST['user_postalcode']);
    update_usermeta($user_id, 'user_country', $_POST['user_country']);
    update_usermeta($user_id, 'user_telephone', $_POST['user_telephone']);
    update_usermeta($user_id, 'user_dob', $_POST['user_dob']);
}
?>