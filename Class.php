<?php

/**
 * Core Class for Binary MLM Pro
 * @author Mike Lopez <mjglopez@gmail.com>
 * @package wishlistmember
 *
 * @version $Rev: 1944 $
 * $LastChangedBy: mike $
 * $LastChangedDate: 2014-01-09 16:11:18 -0500 (Thu, 09 Jan 2014) $
 */
if (!defined('ABSPATH'))
    die();
if (!class_exists('BMP')) {

    class BMP {

        const ActivationURLs = 'wishlistactivation.com';
        const ActivationMaxRetries = 5;

        // -----------------------------------------
        // Constructor
        function BMP() { // constructor
            global $wpdb;
            require_once(ABSPATH . '/wp-admin/includes/plugin.php');

            $this->AllCategories = get_terms('category', array('fields' => 'ids', 'get' => 'all'));


            $this->PluginOptionName = 'BMP_Options';
            $this->TablePrefix = $wpdb->prefix . 'bmp_';
            $this->OptionsTable = $this->TablePrefix . 'options';

            // character encoding
            $this->BlogCharset = get_option('blog_charset');
            $pluginfile = WP_PLUGIN_DIR . '/' . MLM_PLUGIN_NAME . '/mlm-loader .php';
            $this->PluginInfo = (object) get_plugin_data($pluginfile);
            $this->Version = $this->PluginInfo->Version;
            $this->WPVersion = $GLOBALS['wp_version'] + 0;

            $this->pluginPath = $pluginfile;
            $this->pluginDir = dirname($this->pluginPath);
            $this->PluginFile = basename(dirname($pluginfile)) . '/' . basename($pluginfile);
            $this->PluginSlug = sanitize_title_with_dashes($this->PluginInfo->Name);
            $this->pluginBasename = plugin_basename($this->pluginPath);
//			$this->pluginURL = get_bloginfo('wpurl') . '/' . PLUGINDIR . '/' . dirname(plugin_basename($this->pluginPath));
//			$this->pluginURL = plugins_url('', $this->pluginPath);
            // this method works even if the WLM folder is just a symlink
            $this->pluginURL = plugins_url('', '/') . basename($this->pluginDir);
        }

        var $extensions;
        var $wp_upload_path = '';
        var $wp_upload_path_relative = '';
        var $access_control = null;

        /**
         * TempEmailSanitize
         * is a filter that hooks to sanitize_email
         * and makes sure that our temp email address
         * which we use for shopping cart integrations
         * go through.
         *
         * @param string $email
         * @return string
         */
        function Plugin_Update_Notice($transient) {
            static $our_transient_response;

            if ($this->Plugin_Is_Latest()) {
                return $transient;
            }

            if (!$our_transient_response) {
                $package = $this->Plugin_Download_Url();
                if ($package === false)
                    return $transient;

                $file = $this->PluginFile;

                $our_transient_response = array(
                    $file => (object) array(
                        'id' => 'binary-mlm-pro_' . time(),
                        'slug' => $this->PluginSlug,
                        'new_version' => $this->Plugin_Latest_Version(),
                        'url' => 'http://wordpress.org/extend/plugins/binary-mlm-pro/',
                        'package' => $package
                    )
                );
            }

            $transient->response = array_merge((array) $transient->response, (array) $our_transient_response);
            return $transient; // set_plugin_update_notice($transient);
        }

        function Plugin_Info_Hook($res, $action, $args) {
            if ($res === false && $action == 'plugin_information' && $args->slug == 'binary-mlm-pro') {
                $res = new stdClass();
                $res->name = $this->PluginInfo->Name;
                $res->slug = $this->PluginSlug;
                $res->version = $this->Plugin_Latest_Version();
                $res->author = $this->PluginInfo->Author;
                $res->author_profile = $this->PluginInfo->AuthorURI;
                $res->homepage = $this->PluginInfo->PluginURI;
                $res->requires = "3.0";
                $res->sections = array(
                    'description' => '<p>Binary MLM Pro is a powerful solution for creating an online membership site â€“ all built using WordPress as the core content management system.</p>
										<p>Now itâ€™s easy to control access to your content, accept payments, manage your members and so much more! Read below for full feature descriptions, tutorial videos and examples of sites using Binary MLM Pro.</p>',
                    'support' => '<p>Need help?  Click one of the links below.</p>
									<ul>
									<li><a href="http://wpbinarymlm.com/support-options" target="_blank">Customer Support</a></li>
									<li><a href="http://wpbinarymlm.com/videos" target="_blank">Video Tutorials</a></li>
									<li><a href="http://wpbinarymlm.com/guides" target="_blank">Help Guide</a></li>
									<li><a href="http://wpbinarymlm.com/faq" target="_blank">FAQ\'s</a></li>
									<li><a href="http://wpbinarymlm.com/api" target="_blank">API Documents</a></li>
									<li><a href="http://wpbinarymlm.com/release-notes" target="_blank">Release Notes</a></li>
									</ul>'
                );
                $res->download_link = 'http://google.com/download';
            }
            return $res;
        }

        function Pre_Upgrade($return, $plugin) {
            $plugin = (isset($plugin['plugin'])) ? $plugin['plugin'] : '';
            if ($plugin == $this->PluginFile) {
                $dir = sys_get_temp_dir() . '/' . 'BMP-Upgrade';

                $this->Recursive_Delete($dir);

                $this->Recursive_Copy($this->pluginDir . '/extensions', $dir . '/extensions');
                $this->Recursive_Copy($this->pluginDir . '/lang', $dir . '/lang');
            }
            return $return;
        }

        function Post_Upgrade($return, $plugin) {
            $plugin = (isset($plugin['plugin'])) ? $plugin['plugin'] : '';
            if ($plugin == $this->PluginFile) {
                $dir = sys_get_temp_dir() . '/' . 'BMP-Upgrade';

                $this->Recursive_Copy($this->pluginDir . '/extensions', $dir . '/extensions');
                $this->Recursive_Copy($this->pluginDir . '/lang', $dir . '/lang');

                $this->Recursive_Copy($dir . '/extensions', $this->pluginDir . '/extensions');
                $this->Recursive_Copy($dir . '/lang', $this->pluginDir . '/lang');

                $this->Recursive_Delete($dir);
            }
            return $return;
        }

        function UpdateNag() {
            if (!$this->Plugin_Is_Latest()) {
                $latest_bmp_ver = $this->Plugin_Latest_Version();
                if (!$latest_bmp_ver) {
                    $latest_bmp_ver = $this->Version;
                }

                global $current_user;
                $user_id = $current_user->ID;
                $dismiss_meta = 'dismiss_mlm_update_notice_' . $latest_bmp_ver;
                if (!get_user_meta($user_id, $dismiss_meta) && current_user_can('update_plugins')) {
                    echo "<div class='update-nag'>";
                    printf(__("The most current version of Binary MLM Pro is v%s.", 'wishlist-member'), $latest_bmp_ver);
                    echo " ";
                    echo "<a href='" . $this->Plugin_Update_Url() . "'>";
                    _e("Please update now. ", 'wishlist-member');
                    echo "</a> | ";
                    echo '<a href="' . add_query_arg('dismiss_notice', '0') . '"> Dismiss </a>';
                    echo "</div>";
                }
            }
        }

        function dismiss_mlm_update_notice() {

            global $current_user;
            $user_id = $current_user->ID;

            /* If user clicks to ignore the notice, add that to their user meta */
            if (!$this->Plugin_Is_Latest()) {
                $latest_bmp_ver = $this->Plugin_Latest_Version();
                if (!$latest_bmp_ver) {
                    $latest_bmp_ver = $this->Version;
                }

                $dismiss_meta = 'dismiss_mlm_update_notice_' . $latest_bmp_ver;
                if (isset($_GET['dismiss_notice']) && '0' == $_GET['dismiss_notice']) {
                    add_user_meta($user_id, $dismiss_meta, 'true', true);
                }
            }
        }

        /**
         * Pre-upgrade checking
         */
        function Upgrade_Check() {
            if (basename($_SERVER['SCRIPT_NAME']) == 'update.php' && $_GET['action'] == 'upgrade-plugin' && $_GET['plugin'] == $this->PluginFile) {
                $check_result = trim($this->ReadURL(add_query_arg('check', '1', $this->Plugin_Download_Url()), 10, true, true));
                if ($check_result != 'allowed') {
                    ///header('Location: ' . $check_result);
                    //exit;
                }
            }
        }

        function Plugin_Download_Url() {
            static $url;
            if (!$url) {
                $url = Get_Download_url($this->Plugin_Latest_Version(), $this->Plugin_Latest_Version_key());
            }
            return $url;
        }

        function Plugin_mlm_Mass_Pay_Download_Url() {
            static $url;
            if (!$url) {
                $url = Get_mlm_Mass_Pay_Download_url(plugin_Latest_Version('mlm-paypal-mass-pay'), plugin_Latest_Version_key('mlm-paypal-mass-pay'));
            }
            return $url;
        }

        function Plugin_Update_Url() {
            return Get_Upgrade_url($this->PluginFile);
        }

        function Plugin_Latest_Version() {
            static $latest_bmp_ver;
            $varname = 'myplugin_version';
            if (empty($latest_bmp_ver) OR isset($_GET['checkversion'])) {
                //   $latest_bmp_ver = get_transient($varname);
                if (empty($latest_bmp_ver) OR isset($_GET['checkversion'])) {
                    $latest_bmp_ver = plugin_Latest_Version();
                    if (empty($latest_bmp_ver)) {
                        //we failed, set the latest version to this one so that we won't keep checking again for today
                        $latest_bmp_ver = $this->Version;
                    }
                    //even if we fail never try again for this day
                    set_transient($varname, $latest_bmp_ver, 60 * 60 * 24);
                }
            }
            return $latest_bmp_ver;
        }

        function Plugin_Latest_Version_key() {
            static $latest_bmp_ver;
            $varname = 'myplugin_version';
            if (empty($latest_bmp_ver) OR isset($_GET['checkversion'])) {
                //   $latest_bmp_ver = get_transient($varname);
                if (empty($latest_bmp_ver) OR isset($_GET['checkversion'])) {
                    $latest_bmp_ver = plugin_Latest_Version_key();
                    if (empty($latest_bmp_ver)) {
                        //we failed, set the latest version to this one so that we won't keep checking again for today
                        $latest_bmp_ver = $this->Version;
                    }
                    //even if we fail never try again for this day
                    set_transient($varname, $latest_bmp_ver, 60 * 60 * 24);
                }
            }
            return $latest_bmp_ver;
        }

        function Plugin_Is_Latest() {
            $latest_ver = $this->Plugin_Latest_Version();
            $ver = $this->Version;
            if (preg_match('/^(\d+\.\d+)\.{' . 'GLOBALREV}/', $this->Version, $match)) {
                $ver = $match[1];
                preg_match('/^(\d+\.\d+)\.[^\.]*/', $latest_ver, $match);
                $latest_ver = $match[1];
            }
            return version_compare($latest_ver, $ver, '<=');
        }

        /**
         * Retrieves an option's value
         * @param string $option The name of the option
         * @param boolean $dec (optional) True to decrypt the return value
         * @param boolean $no_cache (optional) True to skip cache data
         * @return string The option value
         */
        function GetOption($option, $dec = null, $no_cache = null) {
            global $wpdb;
            $cache_key = $option;
            $cache_group = $this->OptionsTable;

            if (is_null($dec))
                $dec = false;
            if (is_null($no_cache))
                $no_cache = false;

            $value = ($no_cache === true) ? false : wp_cache_get($cache_key, $cache_group);
            if ($value === false) {
                $row = $wpdb->get_row($wpdb->prepare("SELECT `option_value` FROM `{$this->OptionsTable}` WHERE `option_name`='%s'", $option));
                if (!is_object($row))
                    return false;
                $value = $row->option_value;

                $value = maybe_unserialize($value);

                wp_cache_set($cache_key, $value, $cache_group);
            }
            if ($dec) {
                $value = $this->WLMDecrypt($value);
            }
            return $value;
        }

        /**
         * Checks whether a url is exempted from licensing
         * @param string $url the url to test
         * @return boolean
         */
        function isURLExempted($url) {
            $patterns = array(
                '/^[^\.]+$/',
                '/^.+\.loc$/',
                '/^.+\.dev$/',
                '/^.+\.staging\.wpengine\.com$/'
            );
            $res = trim(parse_url($url, PHP_URL_HOST));
            foreach ($patterns AS $pattern) {
                if (preg_match($pattern, $res)) {
                    return true;
                }
            }
            return false;
        }

        /**
         * Reads the content of a URL using WordPress WP_Http class if possible
         * @param string|array $url The URL to read. If array, then each entry is checked if the previous entry fails
         * @param int $timeout (optional) Optional timeout. defaults to 5
         * @param bool $file_get_contents_fallback (optional) true to fallback to using file_get_contents if WP_Http fails. defaults to false
         * @return mixed FALSE on Error or the Content of the URL that was read
         */
        function ReadURL($url, $timeout = null, $file_get_contents_fallback = null, $wget_fallback = null) {
            $urls = (array) $url;
            if (is_null($timeout))
                $timeout = 30;
            if (is_null($file_get_contents_fallback))
                $file_get_contents_fallback = false;
            if (is_null($wget_fallback))
                $wget_fallback = false;

            $x = false;
            foreach ($urls AS $url) {
                if (class_exists('WP_Http')) {
                    $http = new WP_Http;
                    $req = $http->request($url, array('timeout' => $timeout));
                    $x = (is_wp_error($req) OR is_null($req) OR $req === false) ? false : ($req['response']['code'] == '200' ? $req['body'] . '' : false);
                }
                else {
                    $file_get_contents_fallback = true;
                }

                //Andy - fix for can not load WishList member page error.
                //$old_settings = ini_get('allow_url_fopen');
                //ini_set('allow_url_fopen',1);
                if ($x === false && ini_get('allow_url_fopen') && $file_get_contents_fallback) {
                    $x = file_get_contents($url);
                }
                //ini_set('allow_url_fopen',$old_settings);

                if ($x === false && $wget_fallback) {
                    exec('wget -T ' . $timeout . ' -q -O - "' . $url . '"', $output, $error);
                    if ($error) {
                        $x = false;
                    }
                    else {
                        $x = trim(implode("\n", $output));
                    }
                }

                if ($x !== false) {
                    return $x;
                }
            }
            return $x;
        }

        /**
         * Retrieves a menu object.  Also displays an HTML version of the menu if the $html parameter is set to true
         * @param string $key The index/key of the menu to retrieve
         * @param boolean $html If true, it echoes the url in as an HTML link
         * @return object|false Returns the menu object if successful or false on failure
         */
        function GetMenu($name, $slug, $html = false) {
            $objHTML = '';
            if ($slug) {
                $objURL = '?page=' . $slug;
                $objName = $name;
                $objHTML = '<a href="' . $objURL . '">' . $objName . '</a>';
                if ($html) {
                    echo $objHTML;
                }
                return $objHTML;
            }
            else {
                return false;
            }
        }

        /**
         * Deletes an entire directory tree
         * @param string $dir Folder Name
         */
        function Recursive_Delete($dir) {
            if (substr($dir, -1) != '/')
                $dir.='/';
            $files = glob($dir . '*', GLOB_MARK);
            foreach ($files AS $file) {
                if (is_dir($file)) {
                    $this->Recursive_Delete($file);
                    rmdir($file);
                }
                else {
                    unlink($file);
                }
            }
            rmdir($dir);
        }

        function Recursive_Copy($source, $dest) {
            if (substr($source, -1) != '/')
                $source.='/';
            $files = glob($source . '*', GLOB_MARK);
            if (!file_exists($dest) || !is_dir($dest)) {
                mkdir($dest, 0777, true);
            }
            foreach ($files AS $file) {
                if (is_dir($file)) {
                    $this->Recursive_Copy($file, $dest . '/' . basename($file));
                }
                else {
                    copy($file, $dest . '/' . basename($file));
                }
            }
        }

        /**
         * Returns the Query String. Pass a GET variable and that gets removed.
         */
        function QueryString() {
            $args = func_get_args();
            $args[] = 'msg';
            $args[] = 'err';
            $get = array();
            parse_str($_SERVER['QUERY_STRING'], $querystring);
            foreach ((array) $querystring AS $key => $value)
                $get[$key] = "{$key}={$value}";
            foreach ((array) array_keys((array) $get) AS $key) {
                if (in_array($key, $args))
                    unset($get[$key]);
            }
            return implode('&', $get);
        }

    }

}
?>