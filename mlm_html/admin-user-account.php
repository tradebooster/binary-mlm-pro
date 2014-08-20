<?php

function adminMLMUserAccountInterface() {
    //get database table prefix
    $table_prefix = mlm_core_get_table_prefix();
    global $wpdb;

    $msg = '';
    if (isset($_POST['mlm_user_account'])) {
        $search = $_POST['search_user'];
        $userId = $wpdb->get_var("SELECT ID FROM {$table_prefix}users WHERE user_login = '$search' OR user_email = '$search'");
        if ($wpdb->num_rows > 0) {
            $_SESSION['search_user'] = $search;
            $_SESSION['session_set'] = 'sets';
            $_SESSION['userID'] = $userId;
        }
        else {
            $msg = __('You have entered a wrong username or email address', 'binary-mlm-pro');
            $_SESSION['search_user'] = $search;
            $_SESSION['session_set'] = '';
            $_SESSION['userID'] = '';
        }
    }
    include 'js-validation-file.html';
    ?>

    <div class='wrap'>
        <div id="icon-users" class="icon32"></div><h1><?php _e('User Report', 'binary-mlm-pro'); ?></h1><br />

        <div class="notibar msginfo" style="margin:10px;">
            <a class="close"></a>
            <p><?php _e("Input a member's username or email address in the input box below to get complete information about the member's account. No more switching back and forth into different member accounts to check their account details.", 'binary-mlm-pro'); ?></p>
        </div>	

    </div>

    <div class="forms-ui">

        <p><span style='color:red;'><?php if (!empty($error)) _e($error) ?></span></p>

        <form name="open_user_account" method="post" action="">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
                <tr>
                    <th scope="row" class="admin-settings">
                        <a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('search-user');"><?php _e('Search By username or email address', 'binary-mlm-pro'); ?> :</a>
                    </th>
                    <td>
                        <input type="text" name="search_user" id="search_user" size="52" value="<?php if (!empty($_SESSION['search_user'])) _e(htmlentities($_SESSION['search_user'])); ?>">
                        <a href="?page=mlm-user-account" style="cursor:pointer;text-decoration:none; margin-left:90px;"><?php _e('Back to User Dashboard', 'binary-mlm-pro'); ?></a>
                        <div class="toggle-visibility" id="search-user"><?php _e('Please enter username or email address.', 'binary-mlm-pro'); ?></div>
                        <div style="color:red;"><?php echo $msg ?></div>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="mlm_user_account" id="mlm_user_account" value="<?php _e('Search', 'binary-mlm-pro'); ?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
            </p>
        </form>

        <script language="JavaScript">
            populateArrays();
        </script>
        <!-----sdfgdf -->
        <?php
        if (!empty($_GET['ac'])) {
            if ($_GET['ac'] == 'edit' && $_GET['page'] == 'mlm-user-account')
                mlm_update_profile($_SESSION['userID']);

            else if ($_GET['ac'] == 'leftleg' && $_GET['page'] == 'mlm-user-account')
                myLeftGroupDetails($_SESSION['userID']);

            else if ($_GET['ac'] == 'rightleg' && $_GET['page'] == 'mlm-user-account')
                myRightGroupDetails($_SESSION['userID']);

            else if ($_GET['ac'] == 'personal' && $_GET['page'] == 'mlm-user-account')
                myPersonalGroupDetails($_SESSION['userID']);

            else if ($_GET['ac'] == 'payout' && $_GET['page'] == 'mlm-user-account')
                mlm_my_payout_page($_SESSION['userID']);

            else if ($_GET['ac'] == 'payout-details' && $_GET['page'] == 'mlm-user-account')
                mlm_my_payout_details_page($_SESSION['userID']);

            else if ($_GET['ac'] == 'network' && $_GET['page'] == 'mlm-user-account')
                adminViewBinaryNetwork($_SESSION['userID']);
        }
        else if (!empty($_SESSION['session_set']) && $_SESSION['session_set'] == 'sets') {
            $key = $wpdb->get_var("SELECT user_key FROM {$table_prefix}mlm_users WHERE user_id = {$_SESSION['userID']}");

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



            $user_info = get_userdata($_SESSION['userID']);


            $add_page_id = get_post_id('mlm_registration_page');
            $sponsor_name = $user_info->user_login;
            $affiliateURL = site_url() . '?page_id=' . $add_page_id . '&sponsor=' . $sponsor_name;
            ?>

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
                                <td><a href="?page=mlm-user-account&ac=edit" style="text-decoration: none"> <?php  _e('Edit', 'binary-mlm-pro'); ?></a></td>
                                <td><a href="?page=mlm-user-account&ac=network" style="text-decoration: none"> <?php  _e('View Genealogy', 'binary-mlm-pro'); ?></a></td>
                            </tr>
                        </table>
                        <table width="100%" border="0" cellspacing="10" cellpadding="1">
                            <tr>
                                <td colspan="2"><strong> <?php  _e('My Payouts', 'binary-mlm-pro'); ?></strong></td>
                            </tr>
                            <tr>
                                <td scope="row"> <?php  _e('Date', 'binary-mlm-pro'); ?></td>
                                <td> <?php  _e('Amount', 'binary-mlm-pro'); ?></td>
                                <td> <?php  _e('Action', 'binary-mlm-pro'); ?></td>
                            </tr>
                            <?php
                            $detailsArr = my_payout_function($_SESSION['userID']);
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
                                        <td><a href="?page=mlm-user-account&ac=payout-details&pid=<?php echo $row->payout_id ?>" style="text-decoration:none;"> <?php  _e('View', 'binary-mlm-pro'); ?></a></td>	
                                    </tr>
                                    <tr><td colspan="2"><a href="?page=mlm-user-account&ac=payout" style="text-decoration:none;"> <?php  _e('View All', 'binary-mlm-pro'); ?></a></td></tr>
                                <?php endforeach; ?>
                                <?php
                            }else {
                                ?>
                                <div class="no-payout">  <?php  _e('You have not earned any commisssions yet.', 'binary-mlm-pro'); ?> </div>

                                <?php
                            }
                            ?>
                        </table>
                    </td>
                    <td width="40%">
                        <table width="100%" border="0" cellspacing="10" cellpadding="1">
                            <tr>
                                <td><strong> <?php  _e('Network Details', 'binary-mlm-pro'); ?></strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <table width="100%" border="0" cellspacing="10" cellpadding="1">
                                        <tr>
                                            <td colspan="2"><strong> <?php  _e('Left Leg Sales', 'binary-mlm-pro'); ?></strong></td>
                                        </tr>

                                        <tr>
                                            <td><?php _e('Total on Left Leg', 'binary-mlm-pro'); ?>: <?php echo $leftLegUsers ?></td>
                                            <td>Active: <?php echo $leftLegActiveUsers ?></td>
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
                                            <td colspan="2"><a href="?page=mlm-user-account&ac=leftleg" style="text-decoration: none"> <?php  _e('View All', 'binary-mlm-pro'); ?></a></td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <table width="100%" border="0" cellspacing="10" cellpadding="1">
                                        <tr>
                                            <td colspan="2"><strong> <?php  _e('Right Leg Sales', 'binary-mlm-pro'); ?></strong></td>
                                        </tr>

                                        <tr>
                                            <td><?php _e('Total on Right Leg', 'binary-mlm-pro'); ?>: <?php echo $rightLegUsers ?></td>
                                            <td>Active: <?php echo $rightLegActiveUsers ?></td>
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
                                            <td colspan="2"><a href="?page=mlm-user-account&ac=rightleg" style="text-decoration: none"> <?php  _e('View All', 'binary-mlm-pro'); ?></a></td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>


                            <tr>
                                <td>
                                    <table width="100%" border="0" cellspacing="10" cellpadding="1">
                                        <tr>
                                            <td colspan="2"><strong> <?php  _e('Personal Sales', 'binary-mlm-pro'); ?></strong></td>
                                        </tr>

                                        <tr>
                                            <td> <?php  _e('My Personal Sales', 'binary-mlm-pro'); ?>: <?php echo $personalSales ?></td>
                                            <td>Active: <?php echo $activePersonalSales ?></td>
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
                                            <td colspan="2"><a href="?page=mlm-user-account&ac=personal" style="text-decoration: none"> <?php  _e('View All', 'binary-mlm-pro'); ?></a></td>
                                        </tr>

                                    </table>
                                </td>
                            </tr> 

                        </table>
                    </td>
                </tr>
            </table>
        <?php }; ?>
        <!---sdfdsfsd ->
        
        
                </div>
        <?php
    }
    ?>