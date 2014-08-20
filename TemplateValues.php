<?php

/*
 * Initial data saved to database when UniLevel Plugin is first activated
 * Makes it easier to manage this
 */

$MLMMemberInitialData = array(
    /* Withdrawal Process email */
    'withdrawalProcess_email_subject' => __('Withdrawal Processed sucessfully', 'binary-mlm-pro'),
    'withdrawalProcess_email_message' => __('Dear [firstname],

Your withdrawal request for the amount [amount] has been successfully processed by Admin.

The withdrawal details are as follows:
Name:	[username]
Amount:	[amount]
Mode:	[withdrawalmode]

Thanks
[sitename] Admin', 'binary-mlm-pro'),
    /* Payout Generated email */
    'runpayout_email_subject' => __('New Payout Generated', 'binary-mlm-pro'),
    'runpayout_email_message' => __('Hello [firstname]

A New Payout run by Admin. You have get the [amount] amount in this Payout.

The payout details are as follows:
Name:	[username]
Amount:	[amount]
Payout Id: [payoutid]

Thanks
[sitename] Admin', 'binary-mlm-pro'),
    /* Network is growing email */
    'networkgrowing_email_subject' => __('Your Network has just grown bigger', 'binary-mlm-pro'),
    'networkgrowing_email_message' => __('Hi [pname]

A new member has just been added in your downline. The member details are as follows:

Username:	[username]
First Name:	[firstname]
Last Name:	[lastname]
Parent:	        [parent]
Sponsor:	[sponsor]

Thanks
[sitename] Admin', 'binary-mlm-pro'),
    /* Withdrawal Intiated email */
    'withdrawalintiate_email_subject' => __('New Withdrawal initiated', 'binary-mlm-pro'),
    'withdrawalintiate_email_message' => __('Hello Admin

A new withdrawal has been initiated by [username].

The withdrawal details are as follows:
Name:	[username]
Amount:	[amount]
Mode:	[mode]
Comment: [comment]

You can login to the admin section of you site and go to Unilevel MLM -> User Withdrawals to Process / Delete this withdrawal.

Thanks
[sitename] Admin', 'binary-mlm-pro'),
);
?>