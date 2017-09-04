<?php

/**
 * Pressidium (Ninukis) Plugin
 *
 * @package   Ninukis_Plugin_Admin
 * @author    Filip Slavik <filip@pressidium.com>
 * @license   GPL-2.0+
 * @link      https://pressidium.com
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
    die( 'Forbidden' );

class Ninukis_Plugin_Admin extends NinukisPluginCommon {

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * Slug of the plugin screen.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $plugin_screen_hook_suffix = null;
    
    /**
     * Function to be called just before anything else
     * when a user accesses the admin area.
     *
     * @since     1.0.0
     */
    public function adminInitHook() {
        // remove the 'update_nag' function from the
        // 'admin_notices' so we don't display the update-nag
        // more info @ EWPDEV-568
        remove_action('admin_notices', 'update_nag', 3);
        /* based on work found @ https://lud.icro.us/disable-wordpress-core-update/ */
        remove_action('wp_version_check', 'wp_version_check');
        remove_action('admin_init', '_maybe_update_core');
        add_filter('pre_transient_update_core', function() {
            return null;
        });
        add_filter('pre_site_transient_update_core', function() {
            return null;
        });
        // end of EWPDEV-568
    }

    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     *
     * @since     1.0.0
     */
    private function __construct() {

        if (!is_multisite()) {

            /* this is not multisite, so only the admin can
             * access the plugin */
            if (!current_user_can('manage_options'))
                return;
        } else {

            if (!is_super_admin()) {
                /* the current user is not a super_admin so, allow the 
                 * user only if he is at least admin and the allowMultisiteAdminInteraction function 
                 * return true
                 */
                if (!( current_user_can('manage_options') && $this->allowMultisiteAdminInteraction()))
                    return;
            }
        }

        $plugin = Ninukis_Plugin::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();

        # hook on the admin_init as well
        add_action('admin_init', array($this, 'adminInitHook'));

        // Load admin style sheet and JavaScript.
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // PWNP-141: Possibly add our special menu on the dashboard
        if ( ! $this->is_pressidium_menu_hidden() ) {

            // Add the options page and menu item using the appropriate hook
            if ( is_multisite() ) {
                add_action('network_admin_menu', array($this, 'add_plugin_admin_menu'));
            }

            /* always add the 'admin_menu' hook */
            add_action('admin_menu', array($this, 'add_plugin_admin_menu'));

        }

        // Add an action link pointing to the options page.
        $plugin_basename = plugin_basename(plugin_dir_path(realpath(dirname(__FILE__))) . $this->plugin_slug . '.php');
        add_filter('plugin_action_links_' . $plugin_basename, array($this, 'add_action_links'));

        add_action('admin_action_manual_smack', array($this, 'manual_smack'));
        add_filter('manage_media_columns', array($this, 'add_media_column'));
        add_action('manage_media_custom_column', array($this, 'add_my_custom_column'), 11, 2);

        add_action('add_meta_boxes_' . $this->plugin_screen_hook_suffix, array($this, 'create_metaboxes'));
        add_action('add_meta_boxes', array($this, 'create_metaboxes'));
        //
        // //setup meta-box
        // do_action('add_meta_boxes_'.$this->plugin_screen_hook_suffix, null);
        // //do_action('add_meta_boxes', null);
    }

    /**
     * Prints the jQuery script to initialize the metaboxes
     * Called on admin_footer-*
     */
    public function admin_footer_scripts() {
        ?>
        <script> postboxes.add_postbox_toggles(pagenow);</script>
        <?php

    }

    public function add_media_column($defaults) {
        $defaults['smacker'] = 'Image Smack';
        return $defaults;
    }

    public function add_my_custom_column($column_name, $id) {
        if ('smacker' == $column_name) {
            $data = wp_get_attachment_metadata($id);
            if (isset($data['smacker']) && !empty($data['smacker'])) {
                echo $data['smacker'];
            } else {
                if (wp_attachment_is_image($id)) {
                    print __('Not smacked yet', $this->plugin_slug);
                    printf("<br><a href=\"admin.php?action=manual_smack&amp;id=%d\">%s</a>", $id, __('Smack it now!', $this->plugin_slug));
                }
            }
        }
    }

    public function manual_smack() {
        if (!current_user_can('upload_files')) {
            wp_die(__("You don't have permission to work with uploaded files.", $this->plugin_slug));
        }

        if (!isset($_GET['id'])) {
            wp_die(__('No attachment ID was provided.', $this->plugin_slug));
        }

        $id = intval($_GET['id']);

        if (wp_attachment_is_image($id)) {
            $smacker = Ninukis_Plugin_Admin_Smack::get_instance();
            $old_meta = wp_get_attachment_metadata($id);
            $meta = $smacker->resize_from_meta($old_meta, $id, true);
            wp_update_attachment_metadata($id, $meta);
            wp_redirect(preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', wp_get_referer()));
            exit();
        }
        wp_die(__('Only images are supported for smacking!', $this->plugin_slug));
    }

    public function create_metaboxes() {

        /* general tab meta boxes */
        add_meta_box(
                'generaltab-dnsinfo', __('DNS Info', $this->plugin_slug), array($this, 'display_general_tab_dnsinfo'), $this->plugin_screen_hook_suffix . 'generaltab-dnsinfo', 'general'
        );

        add_meta_box(
                'generaltab-sftpinfo', __('SFTP Info', $this->plugin_slug), array($this, 'display_general_tab_sftpinfo'), $this->plugin_screen_hook_suffix . 'generaltab-sftpinfo', 'general'
        );

        add_meta_box(
                'generaltab-help', __('Help & KB', $this->plugin_slug), array($this, 'display_general_tab_help'), $this->plugin_screen_hook_suffix . 'generaltab-help', 'general'
        );

        /* caching tab meta boxes */
        add_meta_box(
                'cachingtab-overview', __('Caching Overview', $this->plugin_slug), array($this, 'display_caching_tab_overview'), $this->plugin_screen_hook_suffix . 'cachingtab-overview', 'caching'
        );

        add_meta_box(
                'cachingtab-control', __('Cache Control', $this->plugin_slug), array($this, 'display_caching_tab_control'), $this->plugin_screen_hook_suffix . 'cachingtab-control', 'caching'
        );

        add_meta_box(
                'cachingtab-controlcdn', __('CDN Cache Control', $this->plugin_slug), array($this, 'display_caching_tab_cdncontrol'), $this->plugin_screen_hook_suffix . 'cachingtab-cdncontrol', 'caching'
        );

        add_meta_box(
                'cachingtab-help', __('Help & KB', $this->plugin_slug), array($this, 'display_caching_tab_help'), $this->plugin_screen_hook_suffix . 'cachingtab-help', 'caching'
        );

        /* network tab meta boxes */
        if (is_multisite()) {

            add_meta_box(
                    'network-tab-help', __('Help & KB', $this->plugin_slug), array($this, 'display_network_tab_help'), $this->plugin_screen_hook_suffix . 'network-tab-help', 'network'
            );

            add_meta_box(
                    'network-tab-overview', __('Network Operations Overview', $this->plugin_slug), array($this, 'display_network_tab_overview'), $this->plugin_screen_hook_suffix . 'network-tab-overview', 'network'
            );

            add_meta_box(
                    'network-caching-tab-control', __('Network Cache Control', $this->plugin_slug), array($this, 'display_network_caching_tab_control'), $this->plugin_screen_hook_suffix . 'network-caching-tab-control', 'network'
            );

            add_meta_box(
                    'network-caching-cdn-tab-control', __('Network CDN Cache Control', $this->plugin_slug), array($this, 'display_network_caching_cdn_tab_control'), $this->plugin_screen_hook_suffix . 'network-caching-cdn-tab-control', 'network'
            );
        }

        /* backup tab meta boxes */
        add_meta_box(
                'backuptab-help', __('Help & KB', $this->plugin_slug), array($this, 'display_backup_tab_help'), $this->plugin_screen_hook_suffix . 'backuptab-help', 'backup'
        );

        add_meta_box(
                'backuptab-overview', __('Instant Backup Overview', $this->plugin_slug), array($this, 'display_backup_tab_overview'), $this->plugin_screen_hook_suffix . 'backuptab-overview', 'backup'
        );

        add_meta_box(
                'backuptab-control', __('Instant Backup', $this->plugin_slug), array($this, 'display_backup_tab_control'), $this->plugin_screen_hook_suffix . 'backuptab-control', 'backup'
        );

        /* utility tab meta boxes */
        add_meta_box(
                'utilitytab-help', __('Help & KB', $this->plugin_slug), array($this, 'display_utility_tab_help'), $this->plugin_screen_hook_suffix . 'utilitytab-help', 'utility'
        );

        add_meta_box(
                'utility-fixfilepermissions', __('Fix File Permissions', $this->plugin_slug), array($this, 'display_utility_tab_fixfileperm'), $this->plugin_screen_hook_suffix . 'utilitytab-fixfileperm', 'utility'
        );

        add_meta_box(
                'utility-updatewpcore', __('Update WP Core', $this->plugin_slug), array($this, 'display_utility_tab_updatewpcore'), $this->plugin_screen_hook_suffix . 'utilitytab-updatewpcore', 'utility'
        );

        /* media tab meta boxes */
        add_meta_box(
                'mediatab-help', __('Help & KB', $this->plugin_slug), array($this, 'display_media_tab_help'), $this->plugin_screen_hook_suffix . 'mediatab-help', 'media'
        );

        add_meta_box(
                'mediatab-smackimages', __('Image Smacking', $this->plugin_slug), array($this, 'display_media_tab_smackimages'), $this->plugin_screen_hook_suffix . 'mediatab-smackimages', 'media'
        );
    }

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        /*
         * @TODO :
         *
         * - Uncomment following lines if the admin class should only be available for super admins
         */
        /* if( ! is_super_admin() ) {
          return;
          } */

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Register and enqueue admin-specific style sheet.
     *
     * @TODO:
     *
     * - Rename "Plugin_Name" to the name your plugin
     *
     * @since     1.0.0
     *
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_admin_styles() {

        if (!isset($this->plugin_screen_hook_suffix)) {
            return;
        }

        $screen = get_current_screen();

        if ($this->plugin_screen_hook_suffix == $screen->id) {
            wp_enqueue_style($this->plugin_slug . '-admin-styles', plugins_url('assets/css/admin.css', __FILE__), array(), Ninukis_Plugin::VERSION);
        }
    }

    /**
     * Register and enqueue admin-specific JavaScript.
     *
     * @TODO:
     *
     * - Rename "Plugin_Name" to the name your plugin
     *
     * @since     1.0.0
     *
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_admin_scripts() {

        if (!isset($this->plugin_screen_hook_suffix)) {
            return;
        }

        $screen = get_current_screen();
        if ($this->plugin_screen_hook_suffix == $screen->id) {
            wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/admin.js', __FILE__), array('jquery'), Ninukis_Plugin::VERSION);

            /* Enqueue WordPress' script for handling the metaboxes */
            wp_enqueue_script('postbox');
        }
    }

    /**
     * Gathers important info about the current WP install (like cluster, IPs, LBs, etc) and
     * stores them in object for easy access. This info is used, mainly, by the admin section of our plugin
     * to show our customers where to point their DNS, SFTP, etc ..
     *
     * @since    1.0.0
     */
    public function getWPInstallInfo() {

        static $wp_install_info = null;
        if (!$wp_install_info) {
            /* construct the info */
            $_t = new stdClass;
            $_t->installName = WP_NINUKIS_WP_NAME;
            $_t->accountName = WP_NINUKIS_ACCOUNT;
            $_t->clusterName = WP_NINUKIS_CLUSTER;
            $_t->privateCNAME = WP_NINUKIS_IP; /* not actually private */
            $_t->publicCNAME = $_t->installName . '.onpressidium.com';
            $_t->publicIP = gethostbynamel($_t->privateCNAME);
            /* PWNP-26 START */
            if (defined('WP_NINUKIS_SFTP_HOST')) {
                $_t->sftpHost = WP_NINUKIS_SFTP_HOST;
            } else {
                /* WP_NINUKIS_SFTP_HOST not defined, so use the default */
                $_t->sftpHost = 'sftp.' . $_t->installName . '.onpressidium.com';
            }
            if (defined('WP_NINUKIS_SFTP_PORT')) {
                $_t->sftpPort = WP_NINUKIS_SFTP_PORT;
            } else {
                /* WP_NINUKIS_SFTP_PORT not defined, so use the default */
                $_t->sftpPort = 11422;
            }
            /* PWNP-26 END */
            $_t->sftpAccountName = WP_NINUKIS_ACCOUNT;
            /* if CND is enabled for this WP install update accordinally */
            if ($this->isCDNEnabled()) {
                $_t->cdnEnabled = true;
                $_t->cdnDomainName = $this->getCDNDomain();
            } else {
                $_t->cdnEnabled = false;
            }
            /* if this is a multisite add additional info */
            if (is_multisite()) {
                $_t->networkCDNEnabled = $this->isCDNNetworkEnabled();
            }

            $wp_install_info = $_t;
        }

        return $wp_install_info;
    }

    /**
     * Request an instant backup
     *
     * More info @ EWPDEV-385
     * @since    1.0.0
     */
    public function requestInstantBackup($backupComment = null) {

        if ($this->isStaging())
            return false; // instant backups are not support in staging

        return NinukisApi::get_instance()->performInstantBackup($backupComment);
    }

    /**
     * Request a file permissions repair request for either the production
     * or dev site.
     *
     * More info @ EWPDEV-384
     * @since    1.0.0
     */
    public function requestFilePermissionFix($staging = false) {
        return NinukisApi::get_instance()->performFixFilePermissions($staging);
    }

    /**
     * Request a WP core update
     *
     * More info @ EWPDEV-778
     * @since    1.0.5
     */
    public function requestWPCoreUpdate($staging = false) {
        return NinukisApi::get_instance()->performWPCoreUpdate($staging);
    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {
        /*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *
         */

        $capability = 'manage_options';

        # create the sidemenu
        $this->plugin_screen_hook_suffix = add_menu_page(
                'ninukis-plugin', 'Pressidium', $capability, $this->plugin_slug, array($this, 'display_plugin_main_admin_page'), plugins_url('assets/images/p-white.png', __FILE__), 0
        );

        if (is_super_admin()) {

            # add menu items (nothing really special, just shortcuts to our site & support)
            add_submenu_page(
                    'ninukis-plugin', 'My Portal', 'My Portal', $capability, $this->plugin_slug . '-user-portal', array($this, 'redirect_to_user_portal')
            );
        }


        /* Add callbacks for this screen only */
        add_action('load-' . $this->plugin_screen_hook_suffix, array($this, 'admin_page_actions'), 9);
        add_action('admin_footer-' . $this->plugin_screen_hook_suffix, array($this, 'admin_footer_scripts'));
    }

    /*
     * Actions to be taken prior to page loading. This is after headers have been set.
     * call on load-$hook
     * This calls the add_meta_boxes hooks, adds screen options and enqueues the postbox.js script.
     */

    function admin_page_actions() {
        do_action('add_meta_boxes_' . $this->plugin_screen_hook_suffix, null);
        do_action('add_meta_boxes', $this->plugin_screen_hook_suffix, null);

        /* Enqueue WordPress' script for handling the metaboxes */
        wp_enqueue_script('postbox');
    }

    /*
     * Redirect the user to the customer portal
     */

    public function redirect_to_user_portal() {
        wp_redirect('https://cp.pressidium.com');
        exit;
    }

    // /**
    //  * Render the settings page for this plugin.
    //  *
    //  * @since    1.0.0
    //  */
    public function display_plugin_main_admin_page() {
        include_once( 'views/admin.php' );
    }

    public function display_general_tab_help() {
        include_once('views/general-help.php');
    }

    public function display_general_tab_dnsinfo() {
        include_once('views/general-dnsinfo.php');
    }

    public function display_general_tab_sftpinfo() {
        include_once('views/general-sftpinfo.php');
    }

    public function display_caching_tab_overview() {
        include_once('views/caching-overview.php');
    }

    public function display_caching_tab_help() {
        include_once('views/caching-help.php');
    }

    public function display_caching_tab_control() {
        include_once('views/caching-control.php');
    }

    public function display_network_tab_help() {
        include_once('views/network-help.php');
    }

    public function display_network_tab_overview() {
        include_once('views/network-overview.php');
    }

    public function display_network_caching_tab_control() {
        include_once('views/network-caching-control.php');
    }

    public function display_network_caching_cdn_tab_control() {
        include_once('views/network-caching-cdn-control.php');
    }

    public function display_caching_tab_cdncontrol() {
        include_once('views/caching-cdncontrol.php');
    }

    public function display_backup_tab_control() {
        include_once('views/backup-control.php');
    }

    public function display_backup_tab_help() {
        include_once('views/backup-help.php');
    }

    public function display_backup_tab_overview() {
        include_once('views/backup-overview.php');
    }

    public function display_utility_tab_help() {
        include_once('views/utility-help.php');
    }

    public function display_utility_tab_fixfileperm() {
        include_once('views/utility-fixfileperm.php');
    }

    public function display_utility_tab_updatewpcore() {
        include_once('views/utility-updatewpcore.php');
    }

    public function display_media_tab_help() {
        include_once('views/media-help.php');
    }

    public function display_media_tab_smackimages() {
        include_once('views/media-smackimages.php');
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function add_action_links($links) {

        return array_merge(
                array(
            'settings' => '<a href="' . admin_url('options-general.php?page=' . $this->plugin_slug) . '">' . __('Settings', $this->plugin_slug) . '</a>'
                ), $links
        );
    }

    private function _byte_format($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Returns true if we should allow admin users, in a multisite
     * enviroment to interact with the plugin.
     * 
     * The function respect the WP_NINUKIS_DISABLE_MULTISITE_ADMIN constant.
     * 
     * 
     * @return boolean
     */
    public function allowMultisiteAdminInteraction() {
        if (defined('WP_NINUKIS_DISABLE_MULTISITE_ADMIN') && WP_NINUKIS_DISABLE_MULTISITE_ADMIN)
            return false;
        else
            return true;
    }

    /**
     * Returns true if our special menu should be hidden
     * @return boolean
     */
    private function is_pressidium_menu_hidden() {

        $current_user = wp_get_current_user();

        if ( ! ( $current_user instanceof WP_User ) )
            return false;

        /* our special user should always see our special menu */
        if ( $current_user->user_login == 'ninukisadm' ) {
            return false; // show the menu
        }

        if ( defined('PRESSIDIUM_DISABLE_MENU') && PRESSIDIUM_DISABLE_MENU ) {
            /*  ok, the generic flag to hide the menu has been set, hide the menu *unless*
                the user has the 'view_pressidium_menu` capability
            */
            if ( current_user_can( 'view_pressidium_menu' ) ) {
                return false; // show the menu
            }

            return true; // ok, hide the menu
        }

        return false; // for all cases, show the menu
    }

}
