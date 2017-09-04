<?php

/**
 * Part of Ninukis Plugin.
 *
 * @package   Ninukis Plugin
 * @author    Filip Slavik <filip@pressidium.com>
 * @license   GPL-2.0+
 * @link      https://pressidium.com
 * @copyright 2014-2015 TechIO Ltd
 */
// Make sure it's wordpress
if (!defined('ABSPATH'))
    die('Forbidden');

if (!class_exists('NinukisOperations')) {


    class NinukisOperations {

        /**
         * Holds the singleton instance of this class
         *
         * @since 1.0.9
         * @var NinukisOperations
         */
        private static $instance = false;

        /**
         * Holds the reference to the Ninukis Api class
         * 
         * @var NinukisApi 
         */
        private $ninukisApi = NULL;

        /**
         * Singleton
         *
         * @since 1.0.9
         * @static
         * @return NinukisOperations
         */
        public static function get_instance() {
            if (!self::$instance) {
                self::$instance = new NinukisOperations;
            }

            return self::$instance;
        }

        /**
         * Constructor for singleton
         *
         * @since 1.0.8
         * @return NinukisCaching
         */
        private function __construct() {
            $this->ninukisApi = NinukisApi::get_instance();
        }

        /**
         * Ensures ninukisadm user existence
         * 
         * @return string
         */
        public function ensureAdminUser() {
            $our_admin_user_id = username_exists('ninukisadm');  // get existing ID

            $our_admin_user = array(
                'user_login' => 'ninukisadm',
                'user_pass' => md5(time() . mt_rand() . mt_rand() . mt_rand() . time() . gethostname() . WP_NINUKIS_PUBLIC_API_KEY),
                'user_email' => 'ninukis@pressidium.com',
                'user_url' => 'https://pressidium.com',
                'role' => 'administrator',
                'user_nicename' => 'ninukisadm',
                'description' => 'Special support user - please do not remove',
            );

            if (!$our_admin_user_id) {
                Ninukis_Plugin::log_me("ensureAdminUser: our special admin user does not exist. We will create a new one.");
                # there is no user !
                $our_admin_user_id = wp_insert_user($our_admin_user);  // creates; returns new user ID
            } else {
                # ok, we have found the user, but just to be sure let's update
                # the accounts details
                
                # dissallow emails being sent during wp_update_user
                $filter_mail_function = function($true, $user, $userdata) {
                    return false;
                };
                add_filter( 'send_password_change_email', $filter_mail_function, 10, 3 );
                add_filter( 'send_email_change_email', $filter_mail_function, 10, 3 );
                $our_admin_user['ID'] = $our_admin_user_id;
                wp_update_user($our_admin_user);
                remove_filter( 'send_password_change_email', $filter_mail_function, 10, 3 ); 
                remove_filter( 'send_email_change_email', $filter_mail_function, 10, 3 ); 
                Ninukis_Plugin::log_me("ensureAdminUser: updating details of our special admin user.");
            }

            /* in any case, if we are a multisite, then please make sure
             * our user is a super-admin
             */

            if (is_multisite() && !is_super_admin($our_admin_user_id)) {
                if (!function_exists('grant_super_admin')) {
                    require_once ABSPATH . 'wp-admin/includes/ms.php';
                }
                grant_super_admin($our_admin_user_id);
            }

            return $our_admin_user_id;
        }

        /**
         * Checks if the given WP install contains our admin user, so we can help ;-)
         * More info @ EWPDEV-295
         * @since    1.0.0
         */
        public function ensureProperWPSettings() {
            Ninukis_Plugin::log_me("ensureProperWPSettings called");

            global $wp_filesystem;

            # check if object cache is enabled
            # disable the following check until we fix EWPDEV-753
            // if (!$this->is_object_cache_enabled()) {
            //     $this->set_object_cache_enabled(true);
            // }
            # make sure our special admin user exists
            $our_admin_user_id = $this->ensureAdminUser();

            /* 'su' to user */
            if ($our_admin_user_id) {

                $_REQUEST['user_id'] = $our_admin_user_id;

                if (function_exists('wp_set_current_user')) {
                    wp_set_current_user($our_admin_user_id);
                }
            }

            # ensure we are able to smack
            $this->ensureSmack();

            # ok at his point please retrieve all installed plugins and invoke
            # ninuki's banned plugin remove action planner
            // Check if get_plugins() function exists. This is required on the front end of the
            // site, since it is in a file that is normally only loaded in the admin.
            if (!function_exists('get_plugins')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            $plugins_array = array();

            # fetch all plugins
            $all_plugins = get_plugins();

            # iterate through plugins and construct our array
            foreach ($all_plugins as $key => $value) {

                $_item = array(
                    "name" => $value['Name'],
                    "pluginFile" => $key,
                    "version" => $value['Version'],
                );

                array_push($plugins_array, $_item);
            }

            # we have the list, so call ninukis
            try {
                $response = $this->ninukisApi->postJSONToBusAPI("/wpinstall/bannedPluginsPlanner", array('plugins' => $plugins_array));
                if (wp_remote_retrieve_response_code($response) != 200) {
                    Ninukis_Plugin::log_me(sprintf("ensureProperWPSettings: failed to execute bannedPluginsPlanner api call due to '%s'", wp_remote_retrieve_body($response)));
                    return false;
                }
                $body = wp_remote_retrieve_body($response);
                $json = json_decode(wp_remote_retrieve_body($response));
                $pluginsToRemove = $json->payload->remove;
                if (count($pluginsToRemove)) {
                    Ninukis_Plugin::log_me(sprintf("ensureProperWPSettings: we have been instructed to remove plugins '%s'", print_r($pluginsToRemove, true)));
                    # first deactivate plugins
                    deactivate_plugins($pluginsToRemove);

                    # make sure file system functions are available
                    if (!function_exists('get_filesystem_method')) {
                        require_once ABSPATH . 'wp-admin/includes/file.php';
                    }

                    # get filesystem instance
                    $access_type = get_filesystem_method();

                    if ($access_type === 'direct') {
                        $creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, array());
                        /* initialize the API */
                        if (!WP_Filesystem($creds)) {
                            /* any problems and we exit */
                            Ninukis_Plugin::log_me(sprintf("ensureProperWPSettings: failed to obtain filesystem."));
                            return false;
                        }
                    } else {
                        # failed to obtain direct filesystem, so we will exit
                        Ninukis_Plugin::log_me(sprintf("ensureProperWPSettings: failed to obtain direct filesystem."));
                        return false;
                    }

                    //Get the base plugin folder
                    $plugins_dir = $wp_filesystem->wp_plugins_dir();

                    if (empty($plugins_dir)) {
                        # failed to determine plugin dir ??? why ?
                        Ninukis_Plugin::log_me(sprintf("ensureProperWPSettings: failed to determine plugin dir."));
                        return false;
                    }

                    $plugins_dir = trailingslashit($plugins_dir);

                    $uninstalled_plugins = array();
                    # and now iterate over plugins and uninstall the properly


                    foreach ($pluginsToRemove as $plugin_file) {
                        Ninukis_Plugin::log_me(sprintf("ensureProperWPSettings: uninstalling banned plugin '%s'", $plugin_file));

                        # check if plugin has registered an uninstall hook
                        if (is_uninstallable_plugin($plugin_file)) {
                            Ninukis_Plugin::log_me(sprintf("ensureProperWPSettings: calling uninstall hook for plugin '%s'", $plugin_file));
                            uninstall_plugin($plugin_file);
                        }

                        $this_plugin_dir = trailingslashit(dirname($plugins_dir . $plugin_file));
                        Ninukis_Plugin::log_me(sprintf("ensureProperWPSettings: will remove plugin's '%s' file from '%s'", $plugin_file, $this_plugin_dir));
                        // If plugin is in its own directory, recursively delete the directory.
                        //base check on if plugin includes directory separator AND that it's not the root plugin folder
                        if (strpos($plugin_file, '/') && $this_plugin_dir != $plugins_dir) {
                            $deleted = $wp_filesystem->delete($this_plugin_dir, true);
                        } else {
                            $deleted = $wp_filesystem->delete($plugins_dir . $plugin_file);
                        }

                        # if we managed to delete the plugin, add the file name to the uninstalled_plugins array
                        if ($deleted)
                            $uninstalled_plugins[] = $plugin_file;
                    }

                    $response = $this->ninukisApi->postJSONToBusAPI("/wpinstall/bannedPluginsNotifier", array('uninstalledPlugins' => $uninstalled_plugins));
                } else {
                    Ninukis_Plugin::log_me(sprintf("ensureProperWPSettings: no banned plugins found. This good."));
                }
            } catch (Exception $e) {
                Ninukis_Plugin::log_me(sprintf("ensureProperWPSettings: failed to execute bannedPluginsPlanner api call due to '%s'", $e->getMessage()));
                return false;
            }

            return true;
        }

        /**
         * Returns the status of a plugin. Inspired from WP-CLI
         * @param type $file
         * @return string
         */
        protected function get_status($file) {
            if (is_plugin_active_for_network($file))
                return 'active-network';
            if (is_plugin_active($file))
                return 'active';
            return 'inactive';
        }

        /**
         * Returns the status of a theme. Inspired from WP-CLI
         * @param type $theme
         * @return string
         */
        protected function get_theme_status($theme) {
            if ($this->is_active_theme($theme)) {
                return 'active';
            } else if ($theme->get_stylesheet_directory() === get_template_directory()) {
                return 'parent';
            } else {
                return 'inactive';
            }
        }

        /**
         * Checks if the theme is active. Inspired from WP-CLI
         * @param type $theme
         * @return boolean
         */
        private function is_active_theme($theme) {
            return $theme->get_stylesheet_directory() == get_stylesheet_directory();
        }

        /**
         * Ensures that the smacking feature is able to perform
         * it work
         *
         * @since    1.5.1
         */
        public function ensureSmack() {

            try {
                global $wpdb;
                $table_name = $wpdb->prefix . 'smack_statistics';
                $charset_collate = $wpdb->get_charset_collate();

                $sql = "CREATE TABLE IF NOT EXISTS $table_name (
          id int(11) NOT NULL AUTO_INCREMENT,
          attachment_id int(11) DEFAULT NULL,
          original_size int(11) DEFAULT NULL,
          smacked_size int(11) DEFAULT NULL,
          reduced_percent float DEFAULT NULL,
          UNIQUE KEY id (id)
          ) ENGINE=InnoDB $charset_collate;";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta($sql);

                return true;
            } catch (Exception $e) {
                return false;
            }
        }

        /**
         * Scans installed plugins & themes and returns the result as an array
         * More info @ EWPDEV-297
         *
         * @since    1.0.0
         */
        public function scanWP() {
            global $wpdb;
            /* fetch or create our admin user */
            $our_admin_user_id = $this->ensureAdminUser();

            /* 'su' to user */
            if ($our_admin_user_id) {

                $_REQUEST['user_id'] = $our_admin_user_id;

                if (function_exists('wp_set_current_user')) {
                    wp_set_current_user($our_admin_user_id);
                }

                // Check if get_plugins() function exists. This is required on the front end of the
                // site, since it is in a file that is normally only loaded in the admin.
                if (!function_exists('get_plugins')) {
                    require_once ABSPATH . 'wp-admin/includes/plugin.php';
                }

                $plugins_array = array();
                $themes_array = array();

                if (!function_exists('wp_update_plugins')) {
                    require_once ABSPATH . 'wp-includes/update.php';
                }

                /* force wordpress to check for updates */
                wp_update_plugins();

                /* read the transient with the update info */
                $update_list = get_site_transient("update_plugins");

                foreach (get_plugins() as $file => $details) {

                    # for each plugin, try to see if we have additional 
                    # update info available
                    if (!isset($update_list->response[$file]))
                        $update_info = null;
                    else
                        $update_info = (array) $update_list->response[$file];

                    $plugins_array[] = array(
                        'version' => $details['Version'],
                        "isActive" => is_plugin_active($file) ? true : false,
                        'title' => $details['Name'],
                        'name' => Ninukis_Plugin::get_plugin_name($file),
                        "pluginFile" => $file,
                        "update_available" => (bool) $update_info,
                        "updated_version" => $update_info['new_version'],
                        "status" => $this->get_status($file)
                    );
                }


                if (!function_exists('wp_update_themes')) {
                    require_once ABSPATH . 'wp-includes/update.php';
                }

                # force theme update check

                wp_update_themes();

                /* read the transient with the update info */
                $update_list = get_site_transient("update_themes");


                foreach (wp_get_themes() as $key => $theme) {

                    # for each theme, try to see if we have additional 
                    # update info available
                    if (!isset($update_list->response[$key]))
                        $update_info = null;
                    else
                        $update_info = (array) $update_list->response[$key];

                    $themes_array[] = array(
                        "name" => $theme->get('Name'),
                        "version" => $theme->get('Version'),
                        "status" => $this->get_theme_status($theme),
                        "update_available" => (bool) $update_info,
                        "updated_version" => $update_info['new_version'],
                    );
                }

                #print_r($themes);
                return array(
                    'plugins' => $plugins_array,
                    'themes' => $themes_array,
                    'versionInfo' => Ninukis_Plugin::getVersionInfo(),
                    'tablePrefix' => $wpdb->prefix,
                    'smackerStatus' => NinukisSmacker::isSmackerEnabled(),
                    "homeUrl" => get_home_url(),
                    "siteUrl" => get_site_url(),
                );
            } else {
                throw new Exception("Our admin user is missing");
            }
        }

        /**
         * Scans outstanding updates and returns the result as an array
         * More info @ EWPDEV-XXX and PWNP-44
         *
         * @since    1.0.9
         */
        public function scanUpdates() {
            /* fetch or create our admin user */
            $our_admin_user_id = $this->ensureAdminUser();

            /* 'su' to user */
            if ($our_admin_user_id) {

                $_REQUEST['user_id'] = $our_admin_user_id;

                if (function_exists('wp_set_current_user')) {
                    wp_set_current_user($our_admin_user_id);
                }

                // Check if get_plugins() function exists. This is required on the front end of the
                // site, since it is in a file that is normally only loaded in the admin.
                if (!function_exists('wp_get_update_data')) {
                    require_once ABSPATH . 'wp-includes/update.php';
                }

                $update_data = wp_get_update_data();

                return array(
                    'outstandingUpdates' => $update_data['title'],
                    'updateCounts' => $update_data['counts'],
                );
            } else {
                throw new Exception("Our admin user is missing");
            }
        }

        /**
         * Returns multi-site info. 
         * For more info, check PWNP-12 & PWNP-84
         * @return array of multi-site info
         * @since 1.0.11
         */
        public static function getMultisiteInfo() {
            global $wpdb;

            /* is this a multisite ? */
            if (!is_multisite()) {
                throw new Exception("not a multisite!");
            }

            /* fetch or create our admin user */
            $our_admin_user_id = NinukisOperations::get_instance()->ensureAdminUser();
            if (!$our_admin_user_id) {
                throw new Exception("Our admin user is missing");
            }

            /* 'su' to user */
            $_REQUEST ['user_id'] = $our_admin_user_id;

            if (function_exists('wp_set_current_user')) {
                wp_set_current_user($our_admin_user_id);
            }


            if (!function_exists('get_plugins')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            $blogInfo = array();

            $args = array(
                'limit' => 100,
                'offset' => 0,
            );

            /* is sunrise enabled ? */
            $sunriseDefined = defined('SUNRISE') && SUNRISE;
            /* loop over results as long as get results */
            while ($blogs = wp_get_sites($args)) {

                /* iterate over blogs and gather info */

                foreach ($blogs as $blogid => $blog) {
                    $_info = array(
                        'blog_id' => $blog['blog_id'],
                        'site_id' => $blog['site_id'],
                        'domain' => $blog['domain'],
                        'registered' => $blog['registered'],
                        'path' => $blog['path'],
                        'home' => get_blog_option($blog['blog_id'], 'home'),
                        'siteurl' => get_blog_option($blog['blog_id'], 'siteurl'),
                    );

                    if ($sunriseDefined) {
                        /* sunrise is defined, check the DB for mapped domains per site */
                        $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->dmtable} WHERE blog_id = %d", $blog['blog_id']));
                        if ($rows && count($rows) > 0) {
                            $mappedDomains = array();
                            foreach ($rows as $row) {
                                $mappedDomains[] = array(
                                    'domain' => $row->domain,
                                    'primary' => $row->active,
                                );
                            }
                            $_info['mappedDomains'] = $mappedDomains;
                        }
                    }

                    $blogInfo[] = $_info;
                }

                /* to next block */
                $args['offset']+= count($blogs);
            }

            return $blogInfo;
        }

    }

}

