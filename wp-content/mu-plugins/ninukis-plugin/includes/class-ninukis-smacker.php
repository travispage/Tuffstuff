<?php

/**
 * Part of Ninukis Plugin.
 *
 * @package   Ninukis Plugin
 * @author    Filip Slavik <filip@pressidium.com>
 * @license   GPL-2.0+
 * @link      https://pressidium.com
 * @copyright 2014-2017 TechIO Ltd
 */

// Make sure it's wordpress
if (!defined('ABSPATH'))
    die('Forbidden');

if (!class_exists('NinukisSmacker')) {

    class NinukisSmacker
    {

        /**
         * Holds the singleton instance of this class
         *
         * @var NinukisSmacker
         */
        private static $instance = false;

        /**
         * Smacker options
         * @var array
         */
        static $SMACKER_OPTIONS = array(
            'autosmack' => WP_NINUKIS_WP_NAME . '-autosmack',
            'profile' => WP_NINUKIS_WP_NAME . '-smacker-profile',
            'invocations' => WP_NINUKIS_WP_NAME . '-smacker-invocations',
            'savedbytes' => WP_NINUKIS_WP_NAME . '-smacker-savedbytes',
            'savedpercentages' => WP_NINUKIS_WP_NAME . '-smacker-savedpercentages'
        );

        /**
         * Singleton
         *
         * @static
         * @return NinukisSmacker
         */
        public static function get_instance()
        {
            if (!self::$instance) {
                self::$instance = new NinukisSmacker();
            }

            return self::$instance;
        }

        /**
         * Constructor for singleton
         *
         * @return NinukisSmacker
         */
        private function __construct()
        {

        }

        /**
         * Returns true if the Smacker service is enabled for this site
         */
        public static function isSmackerEnabled()
        {

            if (defined("WP_NINUKIS_WP_NAME")) {
                $option = get_site_option(NinukisSmacker::$SMACKER_OPTIONS['autosmack'], 0);
                return $option == 1 ? TRUE : FALSE;
            }

        }

        /**
         * Ensure operation for smacking options
         *
         * @param bool $init_enabled Optional, use true/false to set service state
         * @param string $init_profile Optional, initialize profile
         * @param int $init_invocations Optional, initialize invocations
         * @param int $init_savedbytes Optional, initialize saved bytes
         * @param float $init_savedpercentages Optional, initialize saved percentages
         *
         * @return bool     True if already exist or created successfully and
         *                  False if options don't exist and creation failed
         */
        public static function ensureSmackOptions($init_enabled = true, $init_profile = 'normal', $init_invocations = 0, $init_savedbytes = 0, $init_savedpercentages = 0)
        {
            $return = true;

            if (!get_site_option(NinukisSmacker::$SMACKER_OPTIONS['autosmack'])) {
                $return &= add_site_option(NinukisSmacker::$SMACKER_OPTIONS['autosmack'], $init_enabled, '', 'no');
            }

            if (!get_site_option(NinukisSmacker::$SMACKER_OPTIONS['profile'])) {
                $return &= add_site_option(NinukisSmacker::$SMACKER_OPTIONS['profile'], $init_profile, '', 'no');
            }

            if (!get_site_option(NinukisSmacker::$SMACKER_OPTIONS['invocations'])) {
                $return &= add_site_option(NinukisSmacker::$SMACKER_OPTIONS['invocations'], $init_invocations, '', 'no');
            }

            if (!get_site_option(NinukisSmacker::$SMACKER_OPTIONS['savedbytes'])) {
                $return &= add_site_option(NinukisSmacker::$SMACKER_OPTIONS['savedbytes'], $init_savedbytes, '', 'no');
            }

            if (!get_site_option(NinukisSmacker::$SMACKER_OPTIONS['savedpercentages'])) {
                $return &= add_site_option(NinukisSmacker::$SMACKER_OPTIONS['savedpercentages'], $init_savedpercentages, '', 'no');
            }

            return $return;
        }

        /**
         * Writes image metadata and smacking statistics
         * Should normally be called only by ninukis
         *
         * @param string $imagePath - The image path, relative to the install's root directory
         *                            (e.g. wp-content/uploads/test.jpg)
         * @param float $percentage_saved - Size percentage saved by the smacking operation
         * @param int $bytes_saved - Saved size (in bytes)
         *
         * @return bool - returns the result of the metadata operation (file is already saved on disk, so if
         *                metadata update also succeeds we return true)
         */
        public static function writeCompleted($imagePath, $percentage_saved, $bytes_saved)
        {
            global $wpdb;

            if (!is_multisite()) {
                /* update smacker saved bytes - we don't use get/set option as we need atomic updates */
                $wpdb->query($wpdb->prepare(
                    "UPDATE $wpdb->options SET option_value = option_value + %d WHERE option_name = '%s';"
                    , $bytes_saved, NinukisSmacker::$SMACKER_OPTIONS['savedbytes']));

                /* update smacker invocation counter - we don't use get/set option as we need atomic updates */
                $wpdb->query($wpdb->prepare(
                    "UPDATE $wpdb->options SET option_value = option_value + 1 WHERE option_name = '%s';"
                    , NinukisSmacker::$SMACKER_OPTIONS['invocations']));

                /* update smacker percentage total - we don't use get/set option as we need atomic updates */
                $wpdb->query($wpdb->prepare(
                    "UPDATE $wpdb->options SET option_value = option_value + %f WHERE option_name = '%s';"
                    , $percentage_saved, NinukisSmacker::$SMACKER_OPTIONS['savedpercentages']));
            } else {
                /* update smacker saved bytes - we don't use get/set option as we need atomic updates */
                $wpdb->query($wpdb->prepare(
                    "UPDATE $wpdb->sitemeta SET meta_value = meta_value + %d WHERE meta_key = '%s' AND site_id = 1;"
                    , $bytes_saved, NinukisSmacker::$SMACKER_OPTIONS['savedbytes']));

                /* update smacker invocation counter - we don't use get/set option as we need atomic updates */
                $wpdb->query($wpdb->prepare(
                    "UPDATE $wpdb->sitemeta SET meta_value = meta_value + 1 WHERE meta_key = '%s' AND site_id = 1;"
                    , NinukisSmacker::$SMACKER_OPTIONS['invocations']));

                /* update smacker percentage total - we don't use get/set option as we need atomic updates */
                $wpdb->query($wpdb->prepare(
                    "UPDATE $wpdb->sitemeta SET meta_value = meta_value + %f WHERE meta_key = '%s' AND site_id = 1;"
                    , $percentage_saved, NinukisSmacker::$SMACKER_OPTIONS['savedpercentages']));
            }

            /* update primary image metadata */
            $image_attachment_id = self::get_attachment_id_from_filepath($imagePath);
            $image_meta = wp_get_attachment_metadata($image_attachment_id);
            $image_meta['smacker'] = __(sprintf('Smacked! Saved: %.2f%% (%s)', $percentage_saved, Ninukis_Plugin::byte_format($bytes_saved)));

            return wp_update_attachment_metadata($image_attachment_id, $image_meta);
        }

        /**
         * Update the Smacker service status
         * @param bool $enabled
         * @return bool False if value was not updated and true if value was updated.
         */
        public static function updateSmackerStatus($enabled = FALSE)
        {
            $status = $enabled ? 1 : 0;
            return update_site_option(WP_NINUKIS_WP_NAME . '-autosmack', $status);
        }

        /**
         * Configure smacking profile
         * @param string $profile
         * @return bool False if value was not updated and true if value was updated
         */
        public static function configureSmackerProfile($profile)
        {
            return update_site_option(NinukisSmacker::$SMACKER_OPTIONS['profile'], $profile);
        }

        public static function getSmackerProfile()
        {
            return get_site_option(NinukisSmacker::$SMACKER_OPTIONS['profile'], 'undefined');
        }

        /**
         * Given an attachment file path it will return the attachment ID
         * @param string $image_path
         * @return int The attachment ID
         */
        public static function get_attachment_id_from_filepath($image_path)
        {
            $wp_upload_dir_data = wp_upload_dir();
            $wp_upload_dir_base = $wp_upload_dir_data['basedir'];
            $wp_home_dir = get_home_path();

            $full_image_path = realpath($wp_home_dir . DIRECTORY_SEPARATOR . $image_path);
            if (substr($full_image_path, 0, strlen($wp_upload_dir_base)) == $wp_upload_dir_base) {
                // +1 to also remove the slash
                $image_path_for_query = substr($full_image_path, strlen($wp_upload_dir_base) + 1);
            }

            global $wpdb;
            $attachment_id = $wpdb->get_col($wpdb->prepare(
                "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file' AND meta_value='%s';"
                , $image_path_for_query));
            return $attachment_id[0];
        }


        /* TODO: Check for usage and remove if nobody uses it */
        public static function get_attachment_id_by_path($url)
        {
            // https://gist.github.com/asadowski10/496068291ec5ca5f3016

            // Split the $url into two parts with the wp-content directory as the separator.
            $parsed_url = explode(parse_url(WP_CONTENT_URL, PHP_URL_PATH), $url);
            // Get the host of the current site and the host of the $url, ignoring www.
            $this_host = str_ireplace('www.', '', parse_url(home_url(), PHP_URL_HOST));
            $file_host = str_ireplace('www.', '', parse_url($url, PHP_URL_HOST));
            echo $this_host . '\n';
            echo $file_host . '\n';
            print_r($parsed_url[1]);
            // Return nothing if there aren't any $url parts or if the current host and $url host do not match.
            if (!isset($parsed_url[1]) || empty($parsed_url[1]) || ($this_host != $file_host)) {
                return;
            }

            // Now we're going to quickly search the DB for any attachment GUID with a partial path match.
            // Example: /uploads/2013/05/test-image.jpg
            global $wpdb;

            $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND guid LIKE %s", $parsed_url[1]));
            if (is_array($attachment) && !empty($attachment)) {
                return array_shift($attachment);
            }

            return null;
        }

    }

}

