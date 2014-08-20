<?php

function mlmNetworkDetailsPage() {
    //get loged user's key
    $key = get_current_user_key();

    //Total Users on My left leg
    $leftLegUsers = totalLeftLegUsers($key);

    //Total users on my right leg
    $rightLegUsers = totalRightLegUsers($key);

    //paid users on my left leg
    $leftLegActiveUsers = activeUsersOnLeftLeg($key);

    //paid users on my right leg
    $rightLegActiveUsers = activeUsersOnRightLeg($key);

    //Total my personal sales
    $personalSales = totalMyPersonalSales($key);

    //Total my personal sales active users
    $activePersonalSales = activeUsersOnPersonalSales($key);

    //show five users on left leg
    $fiveLeftLegUsers = myFiveLeftLegUsers($key);

    //show five users on right leg
    $fiveRightLegUsers = myFiveRightLegUsers($key);

    //show five users on personal sales
    $fivePersonalUsers = myFivePersonalUsers($key);

    //get logged in user info
    global $current_user, $wpdb;

    get_currentuserinfo();
    $username = $current_user->ID;

    $user_info = get_userdata($current_user->ID);
    $_SESSION['ajax'] = 'ajax_check';
    $add_page_id = get_post_id('mlm_registration_page');
    $sponsor_name = $current_user->user_login;

    $affiliateURLold = site_url() . '?page_id=' . $add_page_id . '&sp=' . $key;
    $affiliateURLnew = site_url() . '/u/' . getusernamebykey($key);

    $permalink = get_permalink(empty($_GET['page_id']) ? '' : $_GET['page_id']);
    $postidparamalink = strstr($permalink, 'page_id');
    $affiliateURL = ($postidparamalink) ? $affiliateURLold : $affiliateURLnew;
    ?>
    <?php
    if (function_exists('Update_Paypal_Notification')) {
        Update_Paypal_Notification();
    }
    ?>
    <p class="affiliate_url"><strong>Affiliate URL :</strong> <?php echo $affiliateURL ?> </p><br />

    <table width="100%" border="0" cellspacing="10" cellpadding="1">
        <tr>
            <td width="40%" valign="top">
                <table width="100%" border="0" cellspacing="10" cellpadding="1">
                    <tr>
                        <td colspan="2"><strong> <?php  _e('Personal Information', 'binary-mlm-pro'); ?></strong></td>
                    </tr>
                    <tr>
                        <td scope="row"><?php _e('Title', 'binary-mlm-pro'); ?></td>
                        <td> <?php  _e('Details', 'binary-mlm-pro'); ?></td>
                    </tr>
                    <tr>
                        <td scope="row"> <?php  _e('Name', 'binary-mlm-pro'); ?></td>
                        <td><?php echo $user_info->first_name . ' ' . $user_info->last_name ?></td>
                    </tr>
                    <tr>
                        <td scope="row"> <?php  _e('Address', 'binary-mlm-pro'); ?></td>
                        <td style="white-space:normal;"><?php echo $user_info->user_address1 . "<br>" . $user_info->user_address2 ?></td>
                    </tr>
                    <tr>
                        <td scope="row"> <?php  _e('City', 'binary-mlm-pro'); ?></td>
                        <td><?php echo $user_info->user_city ?></td>
                    </tr>
                    <tr>
                        <td scope="row"> <?php  _e('Contact No', 'binary-mlm-pro'); ?>.</td>
                        <td><?php echo $user_info->user_telephone ?></td>
                    </tr>
                    <tr>
                        <td scope="row"> <?php  _e('DOB', 'binary-mlm-pro'); ?></td>
                        <td><?php echo $user_info->user_dob ?></td>
                    </tr>



                    <tr>
                        <td><a href="<?php echo get_post_id_or_postname('mlm_update_profile_page', 'binary-mlm-pro'); ?>" style="text-decoration: none"><?php _e('Edit', 'binary-mlm-pro'); ?></a></td>
                        <td><a href="<?php echo get_post_id_or_postname('mlm_network_genealogy_page', 'binary-mlm-pro'); ?>" style="text-decoration: none"><?php _e('View Genealogy', 'binary-mlm-pro'); ?></a></td>
                    </tr>
                </table>
                <table width="100%" border="0" cellspacing="10" cellpadding="1">
                    <tr>
                        <td colspan="2"><strong><?php _e('My Payouts', 'binary-mlm-pro'); ?></strong></td>
                    </tr>
                    <tr>
                        <td scope="row"><?php _e('Date', 'binary-mlm-pro'); ?></td>
                        <td><?php _e('Amount', 'binary-mlm-pro'); ?></td>
                        <td><?php _e('Action', 'binary-mlm-pro'); ?></td>
                    </tr>
                    <?php
                    $detailsArr = my_payout_function();
//_e("<pre>");print_r($detailsArr); exit; 
//$page_id = get_post_id('mlm_my_payout_details_page');
                    if (count($detailsArr) > 0) {
                        $mlm_settings = get_option('wp_mlm_general_settings');
                        ?>
                        <?php
                        foreach ($detailsArr as $row) :

                            $amount = $row->commission_amount + $row->bonus_amount + $row->referral_commission_amount - $row->tax - $row->service_charge;
                            ?>
                            <tr>
                                <td><?php echo $row->payoutDate ?></td>
                                <td><?php echo $mlm_settings['currency'] . ' ' . $amount ?></td>
                                <td><a href="<?php echo get_post_id_or_postname_for_payout('mlm_my_payout_details_page', $row->payout_id) ?>"><?php echo __('View', 'binary-mlm-pro'); ?></a></td>

                            </tr>

                        <?php endforeach; ?>
                        <?php
                    }else {
                        ?>
                        <div class="no-payout"><?php _e('You have not earned any commisssions yet.', 'binary-mlm-pro'); ?> </div>

                        <?php
                    }
                    ?>
                </table>
            </td>
            <td width="40%">
                <table width="100%" border="0" cellspacing="10" cellpadding="1">
                    <tr>
                        <td><strong><?php _e('Network Details', 'binary-mlm-pro'); ?></strong></td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellspacing="10" cellpadding="1">
                                <tr>
                                    <td colspan="2"><strong><?php _e('Left Leg Sales', 'binary-mlm-pro'); ?></strong></td>
                                </tr>

                                <tr>
                                    <td><?php _e('Total on Left Leg', 'binary-mlm-pro'); ?>: <?php echo $leftLegUsers ?></td>
                                    <td><?php _e('Active', 'binary-mlm-pro'); ?>: <?php echo $leftLegActiveUsers ?></td>
                                </tr>
                                <?php
                                foreach ($fiveLeftLegUsers as $key => $value) {
                                    _e("<tr>");
                                    foreach ($value as $k => $val) {
                                        _e("<td>" . $val . "</td>");
                                    }
                                    _e("</tr>");
                                }
                                ?>
                                <tr>
                                    <td colspan="2"><a href="<?php echo get_post_id_or_postname('mlm_left_group_details_page', 'binary-mlm-pro'); ?>" style="text-decoration: none"><?php _e('View All', 'binary-mlm-pro'); ?></a></td>
                                </tr>

                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table width="100%" border="0" cellspacing="10" cellpadding="1">
                                <tr>
                                    <td colspan="2"><strong><?php _e('Right Leg Sales', 'binary-mlm-pro'); ?></strong></td>
                                </tr>

                                <tr>
                                    <td><?php _e('Total on Right Leg', 'binary-mlm-pro'); ?>: <?php echo $rightLegUsers ?></td>
                                    <td><?php _e('Active', 'binary-mlm-pro'); ?>: <?php echo $rightLegActiveUsers ?></td>
                                </tr>
                                <?php
                                foreach ($fiveRightLegUsers as $key => $value) {
                                    _e("<tr>");
                                    foreach ($value as $k => $val) {
                                        _e("<td>" . $val . "</td>");
                                    }
                                    _e("</tr>");
                                }
                                ?>
                                <tr>
                                    <td colspan="2"><a href="<?php echo get_post_id_or_postname('mlm_right_group_details_page', 'binary-mlm-pro'); ?>" style="text-decoration: none"><?php _e('View All', 'binary-mlm-pro'); ?></a></td>
                                </tr>

                            </table>
                        </td>
                    </tr>


                    <tr>
                        <td>
                            <table width="100%" border="0" cellspacing="10" cellpadding="1">
                                <tr>
                                    <td colspan="2"><strong><?php _e('Personal Sales', 'binary-mlm-pro'); ?></strong></td>
                                </tr>

                                <tr>
                                    <td><?php _e('My Personal Sales', 'binary-mlm-pro'); ?>: <?php echo $personalSales ?></td>
                                    <td><?php _e('Active', 'binary-mlm-pro'); ?>: <?php echo $activePersonalSales ?></td>
                                </tr>
                                <?php
                                foreach ($fivePersonalUsers as $key => $value) {
                                    _e("<tr>");
                                    foreach ($value as $k => $val) {
                                        _e("<td>" . $val . "</td>");
                                    }
                                    _e("</tr>");
                                }
                                ?>
                                <tr>
                                    <td colspan="2"><a href="<?php echo get_post_id_or_postname('mlm_personal_group_details_page', 'binary-mlm-pro'); ?>" style="text-decoration: none"><?php _e('View All', 'binary-mlm-pro'); ?></a></td>
                                </tr>

                            </table>
                        </td>
                    </tr> 

                </table>
            </td>
        </tr>
    </table>

    <?php
}
?>