<?php

/**
 * Unregister some the default WordPress widgets on startup and register our
 * slightly modified versions
 *
 * @since 3.1.0
 */
function mlm_widgets_init() {
    if (!is_blog_installed()) {
        return;
    }

    unregister_widget('WP_Nav_Menu_Widget');

    register_widget('MLM_Nav_Menu_Widget');
}

add_action('widgets_init', 'mlm_widgets_init', 1);

/**
 * Navigation Menu widget class
 *
 * @since 3.1.0
 */
class MLM_Nav_Menu_Widget extends WP_Nav_Menu_Widget {

    function MLM_Nav_Menu_Widget() {
        $widget_ops = array('description' => __('Use this widget to add one of your custom menus as a widget.'));
        parent::WP_Widget('nav_menu', __('Custom Menu'), $widget_ops);
    }

    function widget($args, $instance) {
        if (isset($instance['mlm_visible_by_members']) && $instance['mlm_visible_by_members'] && !is_user_logged_in()) {
            return;
        }

        if (isset($instance['mlm_visible_by_nonmembers']) && $instance['mlm_visible_by_nonmembers'] && is_user_logged_in()) {
            return;
        }

        // Get menu
        $nav_menu = wp_get_nav_menu_object($instance['nav_menu']);

        if (!$nav_menu)
            return;

        _e($args['before_widget']);

        if (!empty($instance['title']))
            _e($args['before_title'] . $instance['title'] . $args['after_title']);

        wp_nav_menu(array('fallback_cb' => '', 'menu' => $nav_menu));

        _e($args['after_widget']);
    }

    function update($new_instance, $old_instance) {
        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['nav_menu'] = (int) $new_instance['nav_menu'];
        $instance['mlm_visible_by_members'] = 0;
        $instance['mlm_visible_by_nonmembers'] = 0;

        if (isset($new_instance['mlm_visible_by_members'])) {
            $instance['mlm_visible_by_members'] = 1;
        }

        if (isset($new_instance['mlm_visible_by_nonmembers'])) {
            $instance['mlm_visible_by_nonmembers'] = 1;
        }

        return $instance;
    }

}

class affiliate_url extends WP_Widget {

    function __construct() {
        parent::__construct('wpb_widget', __('MLM Sponsor Widget', 'wpb_widget_domain'), array('description' => __('Use this to show sponsor name, picture and Join Now button', 'wpb_widget_domain'),)
        );
    }

    function form($instance) {
        if ($instance) {
            $title = esc_attr($instance['title']);
            $textarea = esc_textarea($instance['textarea']);
            $ckemail = esc_attr($instance['ckemail']);
            add_option('ckemail', $ckemail);
            update_option('ckemail', $ckemail);
        }
        else {
            $title = '';
            $textarea = '';
            $ckemail = '';
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp_widget_plugin'); ?></label><br/>
            <input id="<?php echo $this->get_field_id('title'); ?>" class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('textarea'); ?>"><?php _e('Description:', 'wp_widget_plugin'); ?></label><br/>
            <textarea id="<?php echo $this->get_field_id('textarea'); ?>" class="widefat" name="<?php echo $this->get_field_name('textarea'); ?>"><?php echo $textarea; ?></textarea>
        </p>
        <p>
            <input type="checkbox" value="1" id="<?php echo $this->get_field_id('ckemail'); ?>" class="widefat" name="<?php echo $this->get_field_name('ckemail'); ?>" <?php echo ($ckemail == '1') ? 'checked="checked"' : ''; ?>>
            <label for="<?php echo $this->get_field_id('ckemail'); ?>"><?php _e('Do you want to show email', 'wp_widget_plugin'); ?></label>
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['textarea'] = strip_tags($new_instance['textarea']);
        $instance['ckemail'] = strip_tags($new_instance['ckemail']);
        return $instance;
    }

    function widget($args, $instance) {
        extract($args);
        $table_prefix = mlm_core_get_table_prefix();
        global $wpdb;
        $title = apply_filters('widget_title', $instance['title']);
        $textarea = $instance['textarea'];
        $ckemail = get_option('ckemail');
        echo $before_widget;
        if ($title) {
            echo $before_title . $title . $after_title;
        }
        if ($textarea) {
            echo '<p>' . $textarea . '</p>';
        }

        if (!empty($_GET['sp_name'])) {
            $sp_name = $_GET['sp_name'];
        }
        else if (!empty($_GET['sp'])) {
            $sp_name = getusernamebykey($_GET['sp']);
        }
        else {
            if (!empty($_COOKIE["sp_name"])) {
                $sp_name = $_COOKIE["sp_name"];
            }
        }
        //$sp_name = empty($_COOKIE["sp_name"])?'':$_COOKIE["sp_name"]; 
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $cuserid = $current_user->ID;
            $cusername = $current_user->user_firstname . ' ' . $current_user->user_lastname;
            $cemail = $current_user->user_email;
            $all_meta_for_user = get_user_meta($cuserid);
            ?>  
            <div class="join_box">
                <div class="join_box_left" style="width:90px;float:left;">
                    <?php echo get_avatar($cuserid, 80); ?>
                </div>
                <div class="join_box_right">
                    <?php
                    echo '<span class="swname">' . ucwords(empty($cusername) ? '' : $cusername) . '</span><br/>';
                    if (!empty($ckemail)) {
                        ?>
                        <span class='swemail'><?php echo empty($cemail) ? '' : $cemail ?></span><br/><br/>
                        <?php
                    }
                    else {
                        echo '<br/>';
                    }
                    ?>
                    <input type="button" value="Join Now" class="primary button" onclick="window.location = '<?php echo get_post_id_or_postname('mlm_registration_page', 'binary-mlm-pro'); ?>'" />
                </div>
            </div>
            <?php
        }
        else {
            if (!empty($sp_name)) {
                $userid = $wpdb->get_var("SELECT user_id FROM {$table_prefix}mlm_users WHERE username = '" . $sp_name . "'");
                if (!empty($userid)) {
                    $all_meta_for_user = get_user_meta($userid);
                    $fname = $all_meta_for_user['first_name'][0];
                    $lname = $all_meta_for_user['last_name'][0];
                    $fullname = $fname . ' ' . $lname;
                    $user_info = get_userdata($userid);
                    $email = $user_info->user_email;
                    $permalink = get_permalink(empty($_GET['page_id']) ? '' : $_GET['page_id']);
                    $postidparamalink = strstr($permalink, 'page_id');
                    $concat = ($postidparamalink) ? '&' : '/?';
                }
                ?>
                <div class="join_box">
                    <div class="join_box_left" style="width:90px;float:left;">
                        <?php echo get_avatar($userid, 80); ?>
                    </div>
                    <div class="join_box_right">
                        <?php
                        echo '<span class="swname">' . ucwords(empty($fullname) ? '' : $fullname) . '</span><br/>';
                        if (!empty($ckemail)) {
                            ?>
                            <span class='swemail'><?php echo empty($email) ? '' : $email ?></span><br/><br/>
                            <?php
                        }
                        else {
                            echo '<br/>';
                        }
                        ?>
                        <input type="button" value="Join Now" class="primary button" onclick="window.location = '<?php echo get_post_id_or_postname('mlm_registration_page', 'binary-mlm-pro'); ?><?php echo empty($concat) ? '/?' : $concat ?>sp_name=<?php echo $sp_name; ?>'" />
                    </div>
                </div>
                <?php
            }
        }
        echo $after_widget;
    }

}

function register_affiliate_url() {
    register_widget('affiliate_url');
}

add_action('widgets_init', 'register_affiliate_url');
?>