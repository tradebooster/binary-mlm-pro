<?php

function mlmGeneral() {
    global $wpdb;
    //get database table prefix
    $table_prefix = mlm_core_get_table_prefix();

    $error = '';
    $chk = 'error';

    //most outer if condition
    if (isset($_POST['mlm_general_settings'])) {
        $currency = sanitize_text_field($_POST['currency']);

        if (checkInputField($currency))
            $error .= "\n Please Select your currency type.";

        $reg_url = sanitize_text_field($_POST['reg_url']);

        if (isset($_POST['wp_reg']))
            $error .= "\n Please Fill The URL.";
        //if any error occoured
        if (!empty($error))
            $error = nl2br($error);
        else {
            $chk = '';

            update_option('wp_mlm_general_settings', $_POST);

            /*             * ********code to save the product price that have payment status 1 and product price 0 ********* */
            $mlm_general_settings = get_option('wp_mlm_general_settings');
            if (!empty($mlm_general_settings['product_price'])) {
                $product_price = $mlm_general_settings['product_price'];
                $sql = "SELECT id 
						FROM {$table_prefix}mlm_users
						WHERE payment_status='1' AND product_price='0'";
                $ids = $wpdb->get_results($sql);
                foreach ($ids as $id) {
                    $sql = "update {$table_prefix}mlm_users set product_price='{$product_price}' where id ='{$id->id}' ";

                    $wpdb->query($sql);
                }
            }

            /*             * ********code to save the product price that have payment status 1 and product price 0 ********* */
            $url = get_bloginfo('url') . "/wp-admin/admin.php?page=admin-settings&tab=eligibility";
            _e("<script>window.location='$url'</script>");
            $msg = "<span style='color:green;'>Your general settings has been successfully updated.</span>";
        }
    }// end outer if condition
    
    ?>
    <script>
        jQuery(document).ready(function() {

            jQuery("input[name='ePin_activate']").change(function() {
                var value = jQuery(this).val();
                if (value == '1')
                    jQuery("#sole_id").show();
                else if (value == '0')
                    jQuery("#sole_id").hide();
            });
            jQuery("#rp1").click(function() {
                var value = jQuery('#rp1').val();
                if (value == 'yes')
                    jQuery("#frequency").removeAttr("disabled");
            });
            jQuery("#rp2").click(function() {
                var value = jQuery('#rp2').val();
                if (value == 'no')
                    jQuery("#frequency").attr("disabled", "disabled");
            });
        });



        function isNumberKey(evt)
        {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 31 && (charCode < 46 || charCode > 57 || charCode == 47))
                return false;

            return true;
        }
    </script>	

    <script language="javascript">
        jQuery(document).ready(function() {
            jQuery("#reg_url").click(function() {
                jQuery("#reg_url").removeAttr("readonly");
            });

        });
        function CheckBoxChanged(checkbox)
        {
            if (checkbox.checked == true) {
                //document.getElementById('reg_url').disabled = false;
                jQuery("#reg_url").removeAttr("readonly");
            }
            else
            {
                jQuery("#reg_url").attr("readonly", "readonly");
                //document.getElementById('reg_url').focus();
            }
        }
        function show1()
        {
            if (document.getElementById('reg_url').value == '')
            {
                alert('Please Fill The URL');
                document.getElementById('reg_url').focus();
                return false;
            }
        }


    </script>
    <?php
    if ($chk != '') {
        $mlm_settings = get_option('wp_mlm_general_settings');
        $URL = empty($mlm_settings['affiliate_url']) ? '' : $mlm_settings['affiliate_url'] . '/';
        include 'js-validation-file.html';
        ?>

        <div class='wrap1'>
            <h2><?php _e('Currency Setting', 'binary-mlm-pro'); ?> </h2>
            <div class="updated fade">
                <p><?php _e("In order to enable SEO Friendly Affiliate URLs please add the following line of code in your .htaccess file at the top of the file BEFORE the #Begin Wordpress line of code<br/><br/> <strong> RedirectMatch 301 u/(.*)  " . site_url() . "/" . $URL . "?sp_name=$1 </strong> <br/><br/>Please note that your Permalink setting in WordPress should be anything other than Default setting.", 'binary-mlm-pro'); ?> </p> </div> <br/>
            <div class="notibar msginfo">
                <a class="close"></a>

                <p><?php _e('Please select the base currency of your MLM Network. This option is very important as all calculations will be performed in this base currency. Once this currency is chosen and saved, it CANNOT be changed later. The entire network will need to be reset if you decide to change the currency at a later date.', 'binary-mlm-pro'); ?> </p>

                <p><strong><?php _e('Use WP registration page', 'binary-mlm-pro'); ?> - </strong><?php _e('In case you would like to Check this functionality on your website, you need to put redirect link of registration page.', 'binary-mlm-pro'); ?></p>
                <p><strong><?php _e('URL of registration page', 'binary-mlm-pro'); ?> - </strong><?php _e('Registration page url for redirect your website to specefic location .', 'binary-mlm-pro'); ?></p>

                <p><strong><?php _e('Redirect Affiliate URL', 'unilevel-mlm-pro'); ?> - </strong> <?php _e(' By default the affiliate URL for your members is setup to redirect to the registration page included in our plugin. In case you would like the affiliate URL to redirect to a different page, please specify the desired URL in this field. ', 'unilevel-mlm-pro'); ?></p> 
                <p><strong><?php _e('Activate ePin', 'binary-mlm-pro'); ?> - </strong><?php _e('In case you would like to Activate ePin functionality on your website, set this value to Yes.', 'binary-mlm-pro'); ?></p>  
                <p><strong><?php _e('Sole Payment Method', 'binary-mlm-pro'); ?> - </strong><?php _e('In case members can only register on your site via ePin, set this to Yes. This would make the ePin field mandatory on the user registration form and a visitor would need a valid unused ePin to complete his registration. If this value is set to No, a visitor will be able to register on the site even without specifying a valid ePin. In this case you would need to manually mark the member as Paid / Unpaid under Users -> All Users.', 'binary-mlm-pro'); ?></p>             
                <p><strong><?php _e('ePin Length', 'binary-mlm-pro'); ?> - </strong><?php _e('The length of the generated ePins.', 'binary-mlm-pro'); ?></p>
                <p><strong><?php _e('Process Withdrawals', 'binary-mlm-pro'); ?> - </strong><?php _e('In case you would like to Check this functionality on your website than  withdrawal amount by specific process. defaul process is manually', 'binary-mlm-pro'); ?></p>


            </div>
            <?php if ($error) : ?>
                <div class="notibar msgerror">
                    <a class="close"></a>
                    <p> <strong><?php _e('Please Correct the following Error', 'binary-mlm-pro'); ?> :</strong> <?php _e($error); ?></p>

                </div>
            <?php endif; ?>

            <?php
            $currency = (isset($_POST['currency']) ? $_POST['currency'] : (isset($mlm_settings['currency']) ? $mlm_settings['currency'] : ''));
            $wp_reg = (isset($_POST['wp_reg']) ? $_POST['wp_reg'] : (isset($mlm_settings['wp_reg']) ? $mlm_settings['wp_reg'] : ''));
            $reg_url = (isset($_POST['reg_url']) ? $_POST['reg_url'] : (isset($mlm_settings['reg_url']) ? $mlm_settings['reg_url'] : ''));
            $affiliate_url = (isset($_POST['affiliate_url']) ? $_POST['affiliate_url'] : (isset($mlm_settings['affiliate_url']) ? $mlm_settings['affiliate_url'] : ''));
            $product_price = (isset($_POST['product_price']) ? $_POST['product_price'] : (isset($mlm_settings['product_price']) ? $mlm_settings['product_price'] : ''));
            $process_withdrawal = (isset($_POST['process_withdrawal']) ? $_POST['process_withdrawal'] : (isset($mlm_settings['process_withdrawal']) ? $mlm_settings['process_withdrawal'] : ''));
            if (empty($process_withdrawal))
        $_POST['process_withdrawal'] = 'Manually';
            ?>
            <form name="admin_general_settings" method="post" action="" id="admin_general_settings">
                <table border="0" cellpadding="0" cellspacing="0" width="60%" class="form-table">
                    <tr>
                        <th scope="row" class="admin-settings">
                            <a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('admin-mlm-currency');"><?php _e('Currency', 'binary-mlm-pro'); ?> <span style="color:red;">*</span>: </a>
                        </th>
                        <td>
                            <?php
                            $sql = "SELECT iso3, currency FROM {$table_prefix}mlm_currency ORDER BY iso3";
                            $results = $wpdb->get_results($sql);
                            ?>
                            <select name="currency" id="currency" >
                                <option value=""><?php _e('Select Currency', 'binary-mlm-pro'); ?></option>
                                <?php
                                foreach ($results as $row) {
                                    if ($currency == $row->iso3)
                                        $selected = 'selected';
                                    else
                                        $selected = '';
                                    ?>
                                    <option value="<?php echo $row->iso3; ?>" <?php echo $selected ?>><?php echo $row->iso3 . " - " . $row->currency; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <div class="toggle-visibility" id="admin-mlm-currency"><?php _e('Select your currency which will you use.', 'binary-mlm-pro'); ?></div>
                        </td>

                    </tr>
                    <tr>

                        <th scope="row" class="admin-setting" >
                            <strong><?php _e('Use WP registration page'); ?></strong>
                        </th>
                        <td>
                            <input type="checkbox" name="wp_reg" id="wp_reg" value="1" <?php echo ($wp_reg == 1) ? ' checked="checked"' : ''; ?> onclick="CheckBoxChanged(this);" onblur="show1();" />
                        </td> 
                    </tr>
                    <tr>

                        <th scope="row" class="admin-setting" >
                            <strong><?php _e('URL of registration page', 'binary-mlm-pro'); ?><span style="color:red;"></span>:</strong>
                        </th>
                        <td>
                            <?php echo site_url() . '/' ?><input type="text" name="reg_url" id="reg_url" value="<?php echo $reg_url ?>" readonly="true"/>
                        </td>

                    </tr>
                    <tr>

                        <th scope="row" class="admin-setting" >
                            <strong><?php _e('Redirect Affiliate URL', 'binary-mlm-pro'); ?>:</strong>
                        </th>
                        <td>
                            <?php echo site_url() . '/' ?><input type="text" name="affiliate_url" id="affiliate_url" value="<?php echo $affiliate_url ?>" />
                        </td>

                    </tr>
                    <?php general_settings_epin(); ?>


                    <tr>

                        <th scope="row" class="admin-settings">
                            <strong><?php _e('Process Withdrawals', 'binary-mlm-pro'); ?> <span style="color:red;">*</span>:</strong> 
                        </th>

                        <td>
                            <input type="radio" name="process_withdrawal" id="process_withdrawal" value="Automatically" <?php echo ($process_withdrawal == 'Automatically') ? ' checked="checked"' : '' ?> />Automatically
                            <input type="radio" name="process_withdrawal" id="process_withdrawal" value="Manually" <?php echo ( $process_withdrawal == 'Manually') ? ' checked="checked"' : '' ?> />Manually

                        </td>	

                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="mlm_general_settings" id="mlm_general_settings" value="<?php _e('Update Options', 'binary-mlm-pro'); ?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
                </p>
            </form>
        </div>

        <script language="JavaScript">
            populateArrays();
        </script>
        <?php
    } // end if statement
    else
        _e($msg);
}

//end mlmGeneral function
?>