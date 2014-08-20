<?php

function mlmShowDashboard() {
//echo "<pre>"; print_r($_SERVER); echo "</pre>";

    $BMP_Instance = new BMP();
    $latest_bmp_ver = $BMP_Instance->Plugin_Latest_Version();
    if (!$latest_bmp_ver) {
        $latest_bmp_ver = $BMP_Instance->Version;
    }
    $reversion = preg_split('/[ \.-]/', $BMP_Instance->Version);
    $bmp_version = bmp_arrval($reversion, 0) . '.' . bmp_arrval($reversion, 1);
    $bmp_build = bmp_arrval($reversion, 2);
    $bmp_stage = bmp_arrval($reversion, 3);
    ?>

    <script>
        jQuery(document).ready(function() {
            jQuery('a:contains(Upgrade)').click(function() {
                var validity = '<?php echo is_update() ?>';
                if (validity != 1) {
                    var res = confirm('<?php _e('Your Licence Key has expired. Though you can continue to use the plugin as is, you will not be able to get updates. In order to get updates for the next 1 year you would need to renew your license.\n\n To renew your license key for another 1 year\n Click OK and you are redirected for complete renewal. ', 'binary-mlm-pro') ?>');
                    if (res == true) {
                        window.open('<?php echo WP_BINARY_MLM_ULR ?>/my-account/', '_blank');
                    }
                    else {
                        return false;
                    }
                }

            });
        });

        function download_BMP() {
            var validity = '<?php echo is_update() ?>';
            if (validity == 1) {
                window.location = "<?php
    $BMP_Instance = new BMP();
    echo $BMP_Instance->Plugin_Download_Url()
    ?>";
            }
            else {
                var res = confirm('<?php _e('You will now be redirected to your My Account page at' . WP_BINARY_MLM_ULR, 'binary-mlm-pro') ?>');
                if (res == true) {
                    window.open('<?php echo WP_BINARY_MLM_ULR ?>/my-account/', '_blank');
                }
            }
        }

        function download_PMP() {
            var validity = '<?php echo has_buy() ?>';
            if (validity == 1) {
                window.location = "<?php
    $BMP_Instance = new BMP();
    echo $BMP_Instance->Plugin_mlm_Mass_Pay_Download_Url()
    ?>";
            }
            else {
                alert('<?php _e('Please validate your email address first than you click download', 'binary-mlm-pro') ?>');
                return false;
            }
        }
        function purchase_PMP() {
            var res = confirm('<?php _e('You you are redirecting at ' . WP_BINARY_MLM_ULR, 'binary-mlm-pro') ?>');
            if (res == true) {
                window.open('<?php echo WP_BINARY_MLM_ULR ?>/product/paypal-mass-payments/', '_blank');
            }
        }

    </script>

    <div class='wrap'>
        <div id="icon-users" class="icon32"></div><h1><?php _e('Binary MLM Pro Dashboard', 'binary-mlm-pro'); ?></h1>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="metabox-holder">

                <!-- BEGIN LEFT POSTBOX CONTAINER -->
                <div class='postbox-container' style='width:49%;margin-right:1%'>			
                    <!-- BEGIN NEW POSTBOX -->
                    <div id="wl_dashboard_right_now" class="postbox">
                        <h3><?php _e('NAVIGATION MENU', 'binary-mlm-pro'); ?></h3>
                        <!-- begin inside content -->
                        <div class="inside">
                            <p>
                                <strong><?php $BMP_Instance->GetMenu('Settings', 'admin-settings', true); ?></strong> - <?php _e('Adjust the main settings for your membership', 'binary-mlm-pro'); ?><br /><br />
                                <strong><?php $BMP_Instance->GetMenu('Run Payouts', 'mlm-payout', true); ?></strong> - <?php _e('See and manage your members', 'binary-mlm-pro'); ?><br /><br />
                                <strong><?php $BMP_Instance->GetMenu('User Report', 'mlm-user-account', true); ?></strong> - <?php _e('Control the content your members see', 'binary-mlm-pro'); ?><br /><br />
                                <strong><?php $BMP_Instance->GetMenu('Withdrawals', 'admin-mlm-pending-withdrawal', true); ?></strong> - <?php _e('Manage content for Membership Levels and User Posts', 'binary-mlm-pro'); ?><br /><br />
                                <strong><?php $BMP_Instance->GetMenu('Reports', 'admin-reports', true); ?></strong> - <?php _e('Integrate with shopping carts and autoresponders', 'binary-mlm-pro'); ?><br /><br />
                            </p>
                        </div>
                    </div><!-- END THIS POSTBOX -->
                    <div class="postbox">
                        <h3><?php _e('UPGRADE Binary MLM PRO', 'binary-mlm-pro'); ?></h3>
                        <!-- begin inside content -->
                        <div class="inside">
                            <?php if ($BMP_Instance->Plugin_Is_Latest()): ?>
                                <p>
                                    <!--<a style="float:right" href="?<?php echo $_SERVER['QUERY_STRING']; ?>&checkversion=1"><?php _e('Check for Updates', 'binary-mlm-pro'); ?></a>-->
                                    <span style="color:green"><?php printf(__('You have the latest version of <strong>Binary MLM Pro</strong> (v%1$s)', 'binary-mlm-pro'), $bmp_version); ?></span>
                                </p>
                                <p style="text-align:left; ">
                                    <?php printf(__('<input type="button" id="download" class="button-primary" value="Download Binary MLM Pro" onclick="download_BMP()" />', 'binary-mlm-pro'), $BMP_Instance->Plugin_Download_Url()); ?></p>
                                <p style="text-align:left; ">
                                    <?php echo lic_till_valid() ?> .
                                </p><p></p>
                            <?php else: ?>
                                <p><?php printf(__('You are currently running on <strong>Binary MLM Pro</strong> version %1$s', 'binary-mlm-pro'), $bmp_version); ?>
                                    <br />
                                    <span style="color:red"><?php printf(__('* The most current version is version %1$s', 'binary-mlm-pro'), $latest_ump_ver); ?></span></p>
                                <p style="text-align:left; ">
                                    <?php printf(__('<a href="%2$s" class="button-primary" id="upgrade" >Upgrade</a> &nbsp;&nbsp; <input type="button" id="download" class="button-primary" value="Download Binary MLM Pro" onclick="download_BMP()" />', 'binary-mlm-pro'), $BMP_Instance->Plugin_Download_Url(), $BMP_Instance->Plugin_Update_Url()); ?></p>
                                <p style="text-align:left; ">
                                    <?php echo lic_till_valid() ?> .
                                </p><p></p>
                            <?php endif; ?>

                        </div>
                        <!-- end inside -->
                    </div>
                    <!-- END THIS POSTBOX -->
                    <?php
                    if (!empty($_POST['wpbinary_user_email'])) {
                        update_option('wpbinary_user_email', $_POST['wpbinary_user_email']);
                        has_buy();
                        echo "<script>window.location=''</script>";
                    }
                    ?>

                    <!-- BEGIN NEW POSTBOX -->
                    <div class="postbox" >
                        <h3><?php _e('MLM PAYPAL MASS PAY ADDON', 'binary-mlm-pro'); ?></h3>
                        <!-- begin inside content -->
                        <div class="inside">
                            <p style="text-align:left; ">
                                <?php if (!has_buy()) { ?>
                                    <?php _e('<input type="button" id="purchase" class="button-primary" value="Buy Paypal Mass Pay Addon" onclick="purchase_PMP()" />', 'binary-mlm-pro'); ?><br/><br/>

                                <form method="post" >
                                    <p class="submit">
                                        <span style='color:red'><?php _e('Before download Paypal Mass Pay Addon, validate your email address used during purchase.', 'binary-mlm-pro'); ?></span><br/><br/>
                                        <input type="email" name="wpbinary_user_email" value=""  placeholder="Enter Email Address" required/>
                                        <input type="submit" class="button-secondary" value="Submit" name="Submit" />
                                    </p>
                                </form><br />
                                <?php
                            }
                            else {
                                echo "<span style='color:green'>Your email address validate sucessfully.</span><br/>";
                                _e('<input type="button" id="download" class="button-primary" value="Download Paypal Mass Pay Addon" onclick="download_PMP()" />', 'binary-mlm-pro');
                            }
                            ?>
                            <br />
                            <?php ?>
                            </p>
                        </div>
                        <!-- end inside -->
                    </div>
                    <!-- END THIS POSTBOX -->

                    <?php if (!$BMP_Instance->isURLExempted(strtolower(get_bloginfo('url')))): ?>
                        <!-- BEGIN NEW POSTBOX -->
                        <div class="postbox" style="display:none">
                            <h3><?php _e('Deactivate Binary MLM Pro', 'binary-mlm-pro'); ?></h3>
                            <!-- begin inside content -->
                            <div class="inside">
                                <form method="post" onsubmit="return confirm('<?php _e('Are you sure that you want to deactivate the license of this plugin for this site?', 'binary-mlm-pro'); ?>')">
                                    <p class="submit"><?php _e("If you're migrating your site to a new server, or just need to cancel your license for this site, click the button below to deactivate the license of this plugin for this site.", 'binary-mlm-pro'); ?><br /><br />
                                        <input type="hidden" name="wordpress_wishlist_deactivate" value="<?php echo $BMP_Instance->ProductSKU; ?>" />
                                        <input type="submit" class="button-secondary" value="Deactivate License For This Site" name="Submit" />
                                    </p>
                                </form>
                            </div>
                            <!-- end inside -->
                        </div>
                        <!-- END THIS POSTBOX -->

                    <?php endif; ?>
                    <p>
                        <small><strong>Binary MLM Pro</strong> v<?php echo $bmp_version; ?> |  Build  <?php echo $bmp_build; ?> <?php echo $bmp_stage; ?> | WordPress <?php echo get_bloginfo('version'); ?> | PHP <?php echo phpversion(); ?> on <?php echo php_sapi_name(); ?></small>
                    </p>
                </div>
                <!-- END LEFT POSTBOX CONTAINER -->
                <style type="text/css">
                    #revalidate{background: #ffe9ad url(../wp-content/plugins/binary-mlm-pro/images/info1.png) no-repeat 0 31px;}
                </style>
                <!-- BEGIN RIGHT POSTBOX CONTAINER -->
                <div class="postbox-container" style="width:49%;">

                    <!-- BEGIN SUPPORT POSTBOX -->
                    <div class="postbox">
                        <h3><?php _e('LICENSE SETTINGS', 'binary-mlm-pro'); ?></h3>
                        <!-- begin inside content -->
                        <div class="inside umpsuppport-widget">
                            <?php mlm_licenese_settings_new(); ?>
                        </div>
                        <!-- end inside -->
                    </div>
                    <!-- END SUPPORT POSTBOX -->

                    <!-- BEGIN SUPPORT POSTBOX -->
                    <div class="postbox">
                        <h3><?php _e('SUPPORT', 'binary-mlm-pro'); ?></h3>
                        <!-- begin inside content -->
                        <div class="inside umpsuppport-widget">

                            <?php
                            //links, I have small screen so I want a line to be shorter
                            $faq_lnk = WP_BINARY_MLM_ULR . "/faqs/";
                            $priority_support = WP_BINARY_MLM_ULR . "/product/priority-support/";
                            $blog = WP_BINARY_MLM_ULR . "/blog/";
                            ?>

                            <table class="widefat">
                                <tr class="first">
                                    <td>
                                        <strong><a href="<?php echo $faq_lnk; ?>" target="_blank"><?php _e('FAQs', 'binary-mlm-pro'); ?></a></strong> - <?php _e('Access the FAQs on our website to get answers to your Frequently Asked Questions.', 'binary-mlm-pro'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong><a href="<?php echo $blog; ?>" target="_blank"><?php _e('Blog', 'binary-mlm-pro'); ?></a></strong> - <?php _e('Access the Blog on our website.', 'binary-mlm-pro'); ?>
                                    </td>
                                </tr>									
                                <tr>
                                    <td>
                                        <strong><u>Customer / Technical Support:</u></strong>
                                    </td>	
                                </tr>
                                <tr>
                                    <td>
                                        <strong><a href="<?php echo $priority_support; ?>" target="_blank"><?php _e('Priority Support', 'binary-mlm-pro'); ?></a></strong> - <?php _e('For issues of urgent nature or for issues outside the scope of our standard support.', 'binary-mlm-pro'); ?>
                                    </td>	
                                </tr>									

                                <tr>
                                    <td>
                                        <strong><?php _e('Regular Support', 'binary-mlm-pro'); ?></strong> - <?php _e('Send us an email at <a href= "mailto:support@wordpressmlm.com">support@wordpressmlm.com</a>', 'binary-mlm-pro'); ?>
                                    </td>	
                                </tr>	

                            </table>
                        </div>
                        <!-- end inside -->
                    </div>
                    <!-- END SUPPORT POSTBOX -->




                    <!-- BEGIN NEWS POSTBOX -->
                    <div class="postbox" id="wlrss-postbox">
                        <h3><?php _e('BINARY MLM PRO NEWS', 'my-text-domain'); ?></h3>
                        <!-- begin inside content -->
                        <div class="inside wlrss-widget">
                            <?php
                            include_once( ABSPATH . WPINC . '/feed.php' );
                            $rss = fetch_feed(WP_BINARY_MLM_ULR . '/blog/binary-mlm-pro/feed');
                            if (!is_wp_error($rss)) : // Checks that the object is created correctly
                                $maxitems = $rss->get_item_quantity(5);
                                $rss_items = $rss->get_items(0, $maxitems);
                            endif;
                            ?>
                            <ul>
                                <?php if ($maxitems == 0) : ?>
                                    <li><?php _e('No items', 'my-text-domain'); ?></li>
                                <?php else : ?>
                                    <?php // Loop through each feed item and display each item as a hyperlink. ?>
                                    <?php foreach ($rss_items as $item) : ?>
                                        <li>
                                            <a href="<?php echo esc_url($item->get_permalink()); ?>" target="_blank"
                                               title="<?php printf(__('Posted %s', 'my-text-domain'), $item->get_date('j F Y | g:i a')); ?>">
                                                   <?php echo esc_html($item->get_title()); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>

                        </div>
                        <!-- end inside -->
                    </div>
                    <!-- END NEWS POSTBOX -->

                </div>
                <!-- END RIGHT POSTBOX CONTAINER -->

            </div><!-- END dashboard-widgets-wrap -->
        </div>
    </div>
    <?php
}

function mlm_licenese_settings_new() {
    $error = '';
    $msg = '';
    if (isset($_REQUEST['mlm_license_settings'])) {
        if ($_REQUEST['license_key'] != '') {
            $msg = licUpdate($_REQUEST);
        }
        else {
            $error = _e("<span style='color:red;'>Please fill the complete information.</span>");
        }
    }
    $settings = get_option('mlm_license_settings');
    if (isMLMLic() && empty($_POST)) {
        echo '<div class="notibar msgsuccess"><a class="close"></a><p>' . __('Thank you for re-validating your License Key.', 'binary-mlm-pro') . '</p></div>';
    }
    else if (empty($_POST)) {
        echo '<div class="notibar msgalert" id="revalidate" ><a class="close"></a><p>' . __('You will need to re-validate your license key due to the new licensing policy in this version of the plugin. Just click the Update Details below. You DO NOT need to generate a new license key.Your License key has been updated.', 'binary-mlm-pro') . '</p> </div>';
    }
    ?>
    <div>
        <?php if ($msg) : ?>	
            <?php _e($msg); ?>
        <?php endif; ?>
        <?php if ($error) : ?>
            <div class="notibar msgalert">
                <a class="close"></a>
                <p><?php _e($error); ?></p>
            </div>
        <?php endif; ?>
        <div id="license-form">
            <form name="frm" method="post" action="">
                <table>
                    <tr>
                        <td><strong><?php _e('Domain Name', 'binary-mlm-pro'); ?> :</strong></td>
                        <td><?php echo siteURL() ?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr><td colspan="2"></td></tr>
                    <tr>
                        <td><strong><?php _e('License Key', 'binary-mlm-pro'); ?> :</strong></td>
                        <td><input type="text" name="license_key" size="30" value="<?php if (!empty($settings['license_key'])) _e($settings['license_key']); ?>" /></td>
                        <td><input type="submit" name="mlm_license_settings" id="mlm_license_settings" value="<?php _e('Update Details', 'binary-mlm-pro'); ?>" class='button-primary'></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <?php
}
?>