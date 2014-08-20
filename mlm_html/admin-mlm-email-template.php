<?php

function mlmEmailTemplates() {


    // echo "<pre>"; print_r($_POST);

    if (isset($_POST['MLMMemberAction'])) {

        foreach ($_POST AS $key => $value) {
            update_option($key, $value);
        }

        if (isset($_POST['payout_mail'])) {
            update_option('payout_mail', 1);
        }
        else {
            update_option('payout_mail', 0);
        }

        if (isset($_POST['network_mail'])) {
            update_option('network_mail', 1);
        }
        else {
            update_option('network_mail', 0);
        }

        if (isset($_POST['withdrawal_mail'])) {
            update_option('withdrawal_mail', 1);
        }
        else {
            update_option('withdrawal_mail', 0);
        }

        if (isset($_POST['process_withdrawal_mail'])) {
            update_option('process_withdrawal_mail', 1);
        }
        else {
            update_option('process_withdrawal_mail', 0);
        }
    }
    ?>   

    <style type="text/css">

        body {
            /*Tahoma, Verdana, Arial, Helvetica;*/
            font-size: 12px;
        }

        #nav, #nav ul { /* all lists */
            padding: 0;
            margin: 0;
            list-style: none;
            line-height: 1;
        }

        #nav a {
            width: 14em;
            text-decoration:none;

        }

        #nav li ul li a {
            text-decoration:none;
            color: #000000;
            padding-left:10px;
            padding-top:10px;

            vertical-align: middle; /* | top | bottom */
        }

        img
        {
            vertical-align: middle; /* | top | bottom */
        }

        #nav li { /* all list items */
            float: left;
            width: 14em; /* width needed or else Opera goes nuts */
            height:20px;
        }

        #nav li ul { /* second-level lists */
            position: absolute;
            /*background: #EBEAEB;*/
            padding-left:10px;
            padding-top:10px;
            width: 14em;
            left: -999em; /* using left instead of display to hide menus because display: none isn't read by screen readers */
        }

        #nav li ul li:hover {
            background: #F5F5F5;
        }

        #nav li:hover ul, #nav li.sfhover ul { /* lists nested under hovered list items */
            left: auto;
            background: #EBEAEB;
        }

        #content {
            clear: left;
            color: #ccc;
        }

    </style>

    <script type="text/javascript"><!--//--><![CDATA[//><!--

        sfHover = function() {
            var sfEls = document.getElementById("nav").getElementsByTagName("LI");
            for (var i = 0; i < sfEls.length; i++) {
                sfEls[i].onmouseover = function() {
                    this.className += " sfhover";
                }
                sfEls[i].onmouseout = function() {
                    this.className = this.className.replace(new RegExp(" sfhover\\b"), "");
                }
            }
        }
        if (window.attachEvent)
            window.attachEvent("onload", sfHover);

        function mlm_insertHTML(html, field) {
            var o = document.getElementById(field);
            try {
                if (o.selectionStart || o.selectionStart === 0) {
                    o.focus();
                    var os = o.selectionStart;
                    var oe = o.selectionEnd;
                    var np = os + html.length;
                    o.value = o.value.substring(0, os) + html + o.value.substring(oe, o.value.length);
                    o.setSelectionRange(np, np);
                } else if (document.selection) {
                    o.focus();
                    sel = document.selection.createRange();
                    sel.text = html;
                } else {
                    o.value += html;
                }
            } catch (e) {
            }
        }
        //--><!]]></script>


    <h2><?php _e('Email Templates', 'binary-mlm-pro'); ?></h2>
    <div class="notibar msginfo">
        <a class="close"></a>	
        <p><strong><?php _e('Payout Received Mail', 'unilevel-mlm-pro'); ?> - </strong> <?php _e(' When the payout routine is run this email template will be sent to all the members who have earned comissions and bonuses in this payout cycle. ', 'unilevel-mlm-pro'); ?></p>
        <p><strong><?php _e('Network Growing Mail', 'unilevel-mlm-pro'); ?> - </strong> <?php _e(' When a new member joins the network this email will be sent to all members in the upline of that user right upto the first user of the network. ', 'unilevel-mlm-pro'); ?></p>
        <p><strong><?php _e('Withdrawal Intiated Mail', 'unilevel-mlm-pro'); ?> - </strong> <?php _e(' This is the email that is sent to the site admin notifying him when a member initiates a new withdrawal from the MLM Financial Dashboard. ', 'unilevel-mlm-pro'); ?></p>	
        <p><strong><?php _e('Withdrawal Processed Mail', 'unilevel-mlm-pro'); ?> - </strong> <?php _e(' This email is sent to an individual member once his withdrawal has been successfully processed by the admin. ', 'unilevel-mlm-pro'); ?></p>	
    </div>

    <!-- Email Settings -->
    <form method="post">


        <h3 style="margin-bottom:0"><?php _e('Payout Received Mail', 'binary-mlm-pro'); ?> </h3>
        <table class="form-table">

            <tr valign="top">
                <th scope="row" style="border:none" class="WLRequired"><?php _e('Subject', 'binary-mlm-pro'); ?></th>
                <td style="border:none"><input type="text" name="runpayout_email_subject" value="<?php echo get_option('runpayout_email_subject', true); ?>" size="40" /></td>
            </tr>
            <tr valign="top">
                <th scope="row" class="WLRequired"><?php _e('Body', 'binary-mlm-pro'); ?></th>
                <td>
                    <textarea name="runpayout_email_message" id="<?php echo 'runpayout_email_message'; ?>" cols="40" rows="10" style="float:left;margin-right:10px"><?php echo get_option($x = 'runpayout_email_message'); ?></textarea>
                    <ul id="nav">
                        <li><a href="javascript:return false;"> &nbsp; Merge codes </a>
                            <ul>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[firstname]', '<?php echo $x; ?>')"><?php _e('Insert First Name', 'binary-mlm-pro'); ?></a> </li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[lastname]', '<?php echo $x; ?>')"><?php _e('Insert Last Name', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[email]', '<?php echo $x; ?>')"><?php _e('Insert Email', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[username]', '<?php echo $x; ?>')"><?php _e('Insert Username', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[amount]', '<?php echo $x; ?>')"><?php _e('Insert Amount', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[sitename]', '<?php echo $x; ?>')"><?php _e('Insert Sitename', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[payoutid]', '<?php echo $x; ?>')"><?php _e('Insert Payout Id', 'binary-mlm-pro'); ?></a></li>
                            </ul>
                        </li>
                    </ul>
                    <br clear="all" />
                    <?php _e('If you would like to insert info for the individual member<br />please click the merge field codes on the right.', 'binary-mlm-pro'); ?>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row" style="border:none" class="WLRequired">&nbsp;</th>
                <td style="border:none"><input type="checkbox" name="payout_mail" value="1" <?php if ($x = get_option('payout_mail', true) == 1) echo "checked"; ?>/>Enabled this Mail functionality</td>
            </tr>

        </table>   




        <h3 style="margin-bottom:0"><?php _e('Network Growing Mail', 'binary-mlm-pro'); ?> </h3>
        <table class="form-table">

            <tr valign="top">
                <th scope="row" style="border:none" class="WLRequired"><?php _e('Subject', 'binary-mlm-pro'); ?></th>
                <td style="border:none"><input type="text" name="networkgrowing_email_subject" value="<?php echo get_option('networkgrowing_email_subject', true); ?>" size="40" /></td>
            </tr>
            <tr valign="top">
                <th scope="row" class="WLRequired"><?php _e('Body', 'binary-mlm-pro'); ?></th>
                <td>
                    <textarea name="networkgrowing_email_message" id="<?php echo 'networkgrowing_email_message'; ?>" cols="40" rows="10" style="float:left;margin-right:10px"><?php echo get_option($x = 'networkgrowing_email_message'); ?></textarea>
                    <ul id="nav">
                        <li><a href="javascript:return false;"> &nbsp; Merge codes </a>
                            <ul>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[firstname]', '<?php echo $x; ?>')"><?php _e('Insert First Name', 'binary-mlm-pro'); ?></a> </li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[lastname]', '<?php echo $x; ?>')"><?php _e('Insert Last Name', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[email]', '<?php echo $x; ?>')"><?php _e('Insert Email', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[username]', '<?php echo $x; ?>')"><?php _e('Insert Username', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[sponsor]', '<?php echo $x; ?>')"><?php _e('Insert Sponsor', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[parent]', '<?php echo $x; ?>')"><?php _e('Insert Parent', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[sitename]', '<?php echo $x; ?>')"><?php _e('Insert Sitename', 'binary-mlm-pro'); ?></a></li>

                            </ul>
                        </li>
                    </ul>
                    <br clear="all" />
                    <?php _e('If you would like to insert info for the individual member<br />please click the merge field codes on the right.', 'binary-mlm-pro'); ?>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row" style="border:none" class="WLRequired">&nbsp;</th>
                <td style="border:none"><input type="checkbox" name="network_mail" value="1" <?php if ($w = get_option('network_mail', true) == 1) echo "checked"; ?> />Enabled this Mail functionality</td>
            </tr>		


        </table>     




        <h3 style="margin-bottom:0"><?php _e('Withdrawal Intiated Mail', 'binary-mlm-pro'); ?> </h3>
        <table class="form-table">

            <tr valign="top">
                <th scope="row" style="border:none" class="WLRequired"><?php _e('Subject', 'binary-mlm-pro'); ?></th>
                <td style="border:none"><input type="text" name="withdrawalintiate_email_subject" value="<?php echo get_option('withdrawalintiate_email_subject', true); ?>" size="40" /></td>
            </tr>
            <tr valign="top">
                <th scope="row" class="WLRequired"><?php _e('Body', 'binary-mlm-pro'); ?></th>
                <td>
                    <textarea name="withdrawalintiate_email_message" id="<?php echo 'withdrawalintiate_email_message'; ?>" cols="40" rows="10" style="float:left;margin-right:10px"><?php echo get_option($x = 'withdrawalintiate_email_message'); ?></textarea>
                    <ul id="nav">
                        <li><a href="javascript:return false;"> &nbsp; Merge codes </a>
                            <ul>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[firstname]', '<?php echo $x; ?>')"><?php _e('Insert First Name', 'binary-mlm-pro'); ?></a> </li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[lastname]', '<?php echo $x; ?>')"><?php _e('Insert Last Name', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[email]', '<?php echo $x; ?>')"><?php _e('Insert Email', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[username]', '<?php echo $x; ?>')"><?php _e('Insert Username', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[password]', '<?php echo $x; ?>')"><?php _e('Insert Password', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[loginurl]', '<?php echo $x; ?>')"><?php _e('Insert Login URL', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[memberlevel]', '<?php echo $x; ?>')"><?php _e('Insert Membership Level', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[amount]', '<?php echo $x; ?>')"><?php _e('Insert Amount', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[comment]', '<?php echo $x; ?>')"><?php _e('Insert Comment', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[payoutid]', '<?php echo $x; ?>')"><?php _e('Insert Payout Id', 'binary-mlm-pro'); ?></a></li>
                            </ul>
                        </li>
                    </ul>
                    <br clear="all" />
                    <?php _e('If you would like to insert info for the individual member<br />please click the merge field codes on the right.', 'binary-mlm-pro'); ?>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row" style="border:none" class="WLRequired">&nbsp;</th>
                <td style="border:none"><input type="checkbox" name="withdrawal_mail" value="1" <?php if ($v = get_option('withdrawal_mail', true) == 1) echo "checked"; ?> />Enabled this Mail functionality</td>
            </tr>		

        </table>   



        <h3 style="margin-bottom:0"><?php _e('Withdrawal Processed Mail', 'binary-mlm-pro'); ?> </h3>
        <table class="form-table">

            <tr valign="top">
                <th scope="row" style="border:none" class="WLRequired"><?php _e('Subject', 'binary-mlm-pro'); ?></th>
                <td style="border:none"><input type="text" name="withdrawalProcess_email_subject" value="<?php echo get_option('withdrawalProcess_email_subject', true); ?>" size="40" /></td>
            </tr>
            <tr valign="top">
                <th scope="row" class="WLRequired"><?php _e('Body', 'binary-mlm-pro'); ?></th>
                <td>
                    <textarea name="withdrawalProcess_email_message" id="<?php echo 'withdrawalProcess_email_message'; ?>" cols="40" rows="10" style="float:left;margin-right:10px"><?php echo get_option($x = 'withdrawalProcess_email_message'); ?></textarea>
                    <ul id="nav">
                        <li><a href="javascript:return false;"> &nbsp; Merge codes </a>
                            <ul>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[firstname]', '<?php echo $x; ?>')"><?php _e('Insert First Name', 'binary-mlm-pro'); ?></a> </li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[lastname]', '<?php echo $x; ?>')"><?php _e('Insert Last Name', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[email]', '<?php echo $x; ?>')"><?php _e('Insert Email', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[username]', '<?php echo $x; ?>')"><?php _e('Insert Username', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[amount]', '<?php echo $x; ?>')"><?php _e('Insert Amount', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[withdrawalmode]', '<?php echo $x; ?>')"><?php _e('Insert Withdrawal Mode', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[payoutid]', '<?php echo $x; ?>')"><?php _e('Insert Payout Id', 'binary-mlm-pro'); ?></a></li>
                                <li><a href="javascript:;" onclick="mlm_insertHTML('[sitename]', '<?php echo $x; ?>')"><?php _e('Insert Sitename', 'binary-mlm-pro'); ?></a></li>
                            </ul>
                        </li>
                    </ul>
                    <br clear="all" />
                    <?php _e('If you would like to insert info for the individual member<br />please click the merge field codes on the right.', 'binary-mlm-pro'); ?>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row" style="border:none" class="WLRequired">&nbsp;</th>
                <td style="border:none"><input type="checkbox" name="process_withdrawal_mail" value="1"  <?php if ($u = get_option('process_withdrawal_mail', true) == 1) echo "checked"; ?>/>Enabled this Mail functionality</td>
            </tr>		

        </table>               




        <p class="submit">

            <input type="submit" name="MLMMemberAction" class="button-primary" value="<?php _e('Save Settings', 'binary-mlm-pro'); ?>" />
        </p>
    </form>












    <?php
}
?>