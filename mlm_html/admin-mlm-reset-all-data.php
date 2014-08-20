<?php

function adminMlmReserAllData() {
    $url = plugins_url();
    ?>	
    <script>
        jQuery(function() {
            jQuery("#reset_data").click(function() {
                var name = jQuery("input#reset_data").prop("name");
                var dataString = 'name=' + name;
                if (confirm("<?php _e('Are you sure you wish to erase ALL MLM data. There is no undo for this action.', 'binary-mlm-pro') ?>")) {
                    jQuery.ajax({
                        type: "POST",
                        url: "<?php _e($url); ?>/binary-mlm-pro/ajax-reset-all-mlm-data.php",
                        data: dataString,
                        success: function(data) {
                            jQuery('#message').html(data);
                        }
                    });
                    return false;
                }
            });
        });
    </script>

    <div class='wrap1'>
        <h2><?php _e('Reset All MLM Data ', 'binary-mlm-pro'); ?>  </h2>
        <div class="notibar msginfo">
            <a class="close"></a>
            <p><?php _e('If you wish to erase all MLM Data click the Reset All MLM Data button below. This will erase all users (except your WP Admin), all MLM Settings, Commissions, Bonuses, Payouts, etc..', 'binary-mlm-pro'); ?></p>
            <p><?php _e('<strong>CAUTION:</strong> Be very sure that you would like to erase all MLM data and start afresh. There is no way to get your data back once erased (unless of course you have a backup).', 'binary-mlm-pro'); ?></p>
        </div>



        <div id="message"></div>


        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
            <tr>

                <td>
                    <input type="button" name="reset_data" id="reset_data" value="Reset All MLM Data"  class='button-primary' />
                    <div class="toggle-visibility" id="admin-mlm-bonus"><?php _e('Please select bonus type.', 'binary-mlm-pro'); ?></div>
                </td>
            </tr>
        </table>




        <?php
    }

//end mlmBonus function
    ?>