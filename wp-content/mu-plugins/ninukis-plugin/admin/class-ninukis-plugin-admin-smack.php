<?php
/**
 * Part of Ninukis Plugin.
 *
 * @package   Ninukis_Plugin_Admin
 * @author    John Andriopoulos <john@pressidium.com>
 * @author    Filip Slavik <filip@pressidium.com>
 * @license   GPL-2.0+
 * @link      https://www.pressidium.com
 */


class Ninukis_Plugin_Admin_Smack extends NinukisPluginCommon {

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
     *
     * @var string The default smacker endpoint URL
     */
    private static $smacker_base = 'http://c1-smacker.pressidium.com/index.php?file=';


    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     *
     * @since     1.0.0
     */
    private function __construct() {

        /*
         * Call $plugin_slug from public plugin class.
         *
         */
        $plugin = Ninukis_Plugin::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();

        add_filter('http_request_args', function($args) {
                $args['reject_unsafe_urls'] = false;
                return $args;
        });
        add_filter('wp_generate_attachment_metadata', array($this, 'resize_from_meta'), 11, 2);
    }
    
    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
                self::$instance = new self;
        }

        return self::$instance;
    }
    
    /**
     * @return string The base URL of the smacker endpoint
     */
    private function get_smacker_base() {
        if ( defined ( 'WP_NINUKIS_SMACKER_BASE') ) {
            return WP_NINUKIS_SMACKER_BASE;
        }
        return self::$smacker_base;
    }


    public function smack_image($path, $url, $id) {
        if( !file_exists($path) ) {
            $msg = __(sprintf("Smacking failed: file '%s' does not exists !", $path), $this->plugin_slug);
            Ninukis_Plugin::log_me($msg);
            return $msg;
        }
        
        if( !is_writeable($path) ) {
            $msg = __(sprintf("Smacking failed: file '%s' is not writable !", $path), $this->plugin_slug);
            Ninukis_Plugin::log_me($msg);
            return $msg;
        }
        
        $response = wp_safe_remote_get( $this->get_smacker_base() . $url, array('timeout' => 100, 'httpversion' => '1.1'));
        if(is_wp_error($response)) {
            // failed to smack ?
            $msg = __(sprintf("Smacking failed: '%s:%s'", $response->get_error_code(), $response->get_error_message()), $this->plugin_slug);
            Ninukis_Plugin::log_me($msg);
            return $msg;
        }
        
        $smacked_response = json_decode(wp_remote_retrieve_body($response));
        
        if(isset($smacked_response->error)) {
            //could be because of throttling, large image file, or any other reason, fail silently
            $msg = __(sprintf("Smacking failed: '%s'", $smacked_response->error), $this->plugin_slug);
            Ninukis_Plugin::log_me($msg);
            return $msg;
        }

        if (!function_exists('download_url')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        $smacked_file = download_url( $smacked_response->link, 100 );

        if(is_wp_error($smacked_file)) {
            $msg = __(sprintf("Smacking failed: '%s:%s'", $smacked_file->get_error_code(), $smacked_file->get_error_message()), $this->plugin_slug);
            Ninukis_Plugin::log_me($msg);
            return $msg;
        }

        clearstatcache();
        $original_size = filesize($path);
        clearstatcache();
        $reduced_size = filesize($smacked_file);
        $reduced_percent = 100 - (($reduced_size / $original_size) * 100);

        global $wpdb;
        $inserted = $wpdb->insert($wpdb->prefix . 'smack_statistics',
                array(
                        'attachment_id' => $id,
                        'original_size' => $original_size,
                        'smacked_size' => $reduced_size,
                        'reduced_percent' => $reduced_percent
                ),
                array('%d', '%d', '%d', '%f')
        );

        return (rename($smacked_file, $path))? __(sprintf('Smacked! - Size reduced by %.2f%% (%s)', $reduced_percent, $this->_byte_format($original_size - $reduced_size)), $this->plugin_slug) : '';
    }

    public function resize_from_meta($meta, $id = NULL, $force = false) {
	$this->autosmack = get_option(WP_NINUKIS_WP_NAME . '-autosmack', 0);

        if($this->autosmack == 0 && $force !== true) return $meta;

        if(($id == NULL) || wp_attachment_is_image($id) === false ) {
            return $meta;
        }
        $image_path = get_attached_file($id);
        $image_url = wp_get_attachment_url($id);

        $meta['smacker'] = $this->smack_image($image_path, $image_url, $id);

        if(!isset($meta['sizes'])) return $meta;

        foreach($meta['sizes'] as $size_key => $size_data) {
            $extra_size_path = trailingslashit(dirname($image_path)) . $size_data['file'];
            $extra_size_url = trailingslashit(dirname($image_url)) . $size_data['file'];

            $meta['sizes'][$size_key]['smacker'] = $this->smack_image($extra_size_path, $extra_size_url, $id);
        }

        return $meta;
    }

    private function _byte_format($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }


}