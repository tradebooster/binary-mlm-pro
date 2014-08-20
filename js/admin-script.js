///// NOTIFICATION CLOSE BUTTON /////
jQuery(document).ready(function() {
    jQuery('.notibar .close').click(function() {
        jQuery(this).parent().fadeOut(function() {
            jQuery(this).remove();
        });
    });
});

jQuery(document).ready(function() {
    jQuery("[href='admin.php?page=admin-mlm-withdrawal-process']").hide();
    jQuery("div.update-message:contains(There is a new version of Binary MLM Pro available)").remove();
});