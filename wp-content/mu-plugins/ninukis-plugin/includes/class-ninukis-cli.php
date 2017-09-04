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
/**
 * Ninukis CLI interface
 */
// Make sure it's wordpress
if (!defined('ABSPATH'))
    die('Forbidden');

WP_CLI::add_command('ninukis', 'Ninukis_CLI');

class Ninukis_CLI extends WP_CLI_Command {

    /**
     * Returns the Ninukis Plugin instance
     * 
     * @return Ninukis_Plugin
     */
    private function getNinukisPlugin() {
        return Ninukis_Plugin::get_instance();
    }

    /**
     * Get Ninukis Version
     *
     * ## OPTIONS
     *
     * [--format=<format>]
     * : The serialization format for the value.
     *
     * ## EXAMPLES
     *
     *  wp ninukis version
     *
     */
    public function version($args = array(), $assoc_args = array()) {
        $versionInfo = Ninukis_Plugin::getVersionInfo();
        if ($versionInfo) {
            $fields = array('wpVersion', 'wpDbVersion', 'wpPluginVersion');
            $formatter = new \WP_CLI\Formatter(
                    $assoc_args, $fields);

            $formatter->display_item($versionInfo);
        } else {
            WP_CLI::error("failed to retrieve version");
        }
    }

    /**
     * Ensures ninukisadm user existence
     * 
     * ## OPTIONS
     *
     * None. 
     *
     * ## EXAMPLES
     *
     *  wp ninukis ensureUser
     *
     */
    public function ensureUser($args = array(), $assoc_args = array()) {
        $ops = NinukisOperations::get_instance();
        $id = $ops->ensureAdminUser();
        if ($id) {
            WP_CLI::success("Created or updated ninukisadm user $id");
        } else {
            WP_CLI::error("failed to ensure ninukisadm user");
        }
    }

    /**
     * Ensures proper WP settings
     * 
     * ## OPTIONS
     *
     * None. 
     *
     * ## EXAMPLES
     *
     * wp ninukis ensureWP
     *
     */
    public function ensureWP($args = array(), $assoc_args = array()) {
        $ops = NinukisOperations::get_instance();
        if ($ops->ensureProperWPSettings()) {
            WP_CLI::success("Ensured proper WP settings");
        } else {
            WP_CLI::error("failed to ensure proper WP settings");
        }
    }

    /**
     * Scans install and returns valuable info
     *
     * ## OPTIONS
     *
     * None. 
     * 
     * ## EXAMPLES
     *
     * wp ninukis scan
     *
     */
    public function scan($args = array(), $assoc_args = array()) {
        $scanResult = NinukisOperations::get_instance()->scanWP();
        if ($scanResult) {
            WP_CLI::print_value(json_encode($scanResult));
        } else {
            WP_CLI::error("failed to perfom scan operation");
        }
    }

    /**
     * Returns pending updates info & stats
     * 
     * ## EXAMPLES
     * 
     * wp ninukis updates
     * 
     * @param type $args
     * @param type $assoc_args
     */
    public function updates($args = array(), $assoc_args = array()) {
        $scanResult = NinukisOperations::get_instance()->scanUpdates();
        if ($scanResult) {
            WP_CLI::print_value(json_encode($scanResult));
        } else {
            WP_CLI::error("failed to perfom scan operation");
        }
    }

    /**
     * Manages CDN
     * 
     * ## OPTIONS
     * 
     * info [blogId]                    : returns CDN configuration info
     * status [blogId]                  : returns CDN status 
     * enable [blogId]                  : enables CDN usage
     * disable [blogId]                 : disables CDN usage
     * configure status domain [blogId] : configures CDN with status and domain 
     * 
     * ## EXAMPLES
     * 
     * wp ninukis cdn info
     * wp ninukis cdn status
     * wp ninukis cdn enable
     * wp ninukis cdn disable
     * wp ninukis cdn configure enabled cdn-slavik.pressidium.com
     * 
     * @param type $args
     * @param type $assoc_args
     * 
     */
    public function cdn($args = array(), $assoc_args = array()) {

        $action = isset($args[0]) ? $args[0] : 'prompt';
        
        // check actions
        if (!in_array($action, array('info', 'status', 'enable', 'disable', 'configure', 'prompt'))) {
            WP_CLI::error(sprintf(__('%s is not a valid cnd command.', 'ninukis'), $action));
        }
        // check action parameters
        if ( in_array( $action, array( 'configure' ) ) ) {
            if ( isset( $args[1] ) &&  isset( $args[2])) {
                $cdnStatus = $args[1];
                $cdnDomain = $args[2];
                $blogId = isset($args[3]) ? $args[3] : NULL;
            } else {
                WP_CLI::error(sprintf(__('You need to specify the CDN status and CDN domain', 'ninukis')));
            }
            
            /* check if the cdnStatus is accepted */
            if(! in_array($cdnStatus, array( 'enabled', 'disabled'))) {
                WP_CLI::error(sprintf(__("CDN status '$cdnStatus' is not valid. Please use 'enabled' or 'disabled'.", 'ninukis')));	
            }
        } else if ( in_array( $action, array( 'info', 'enable', 'status', 'disable' ) ) ) {
            /* info, enable, status & disable accepts blogId parameter but only
               if this is a multisite */
            $blogId = isset($args[1]) ? $args[1] : NULL;
            if( $blogId && ! is_multisite()) {
                // ops! this is not allowed
                WP_CLI::error(sprintf(__("blogId '$blogId' has been specified but this site *IS NOT* multisite.", 'ninukis')));
            }
        }

        switch ($action) {
            case 'info':
                $fields = array('enabled', 'domain');
                $formatter = new \WP_CLI\Formatter(
                        $assoc_args, $fields);
                
                $cdn_info = array(
                    'enabled' => NinukisCDN::isCDNEnabled( $blogId ),
                    'domain' => NinukisCDN::getCDNDomain( $blogId ),
                );

                $formatter->display_item($cdn_info, $fields);
                break;
            case 'status':
                if (NinukisCDN::isCDNEnabled( $blogId )) {
                    WP_CLI::line(__('CDN is *enabled*!', 'ninukis'));
                } else {
                    WP_CLI::line(__('CDN is disabled', 'ninukis'));
                }
                break;
            case 'enable':
                if(NinukisCDN::isCDNEnabled( $blogId )) {
                    WP_CLI::error(__('CDN is already enabled on this site', 'ninukis'));
                } else {
                    NinukisCDN::updateCDNStatus(true, $blogId);
                    WP_CLI::success(__("CDN has been enabled on this site.", 'ninukis'));
                }
                break;
            case 'disable':
                if(NinukisCDN::isCDNEnabled( $blogId )) {
                    NinukisCDN::updateCDNStatus(false, $blogId);
                    WP_CLI::success(__("CDN has been *disabled* on this site.", 'ninukis'));
                } else {
                    WP_CLI::error(__('CDN is *NOT* enabled on this site', 'ninukis'));
                }
                break;
            case 'configure':
                if( NinukisCDN::configureCDN($cdnStatus, $cdnDomain, $blogId) ) {
                    WP_CLI::success(__("Configured CDN with status '$cdnStatus' and domain '$cdnDomain' (blogID '$blogId')", 'ninukis'));
                } else {
                    WP_CLI::error(__("Failed to configure CDN with status '$cdnStatus' and domain '$cdnDomain' (blogID '$blogId')", 'ninukis'));
                }
                break;
            case 'prompt':
                WP_CLI::error(__('Please specify a valid action command', 'ninukis'));
                break;
        }
    }

    public function configureCDN($args = array(), $assoc_args = array()) {
        $isCDNCapable = $args[0];
        $cdnDomain = $args[1];

        WP_CLI::success("Configured CDN '$isCDNCapable' option.");
    }
    
    /**
     * Clears expired transients
     * 
     * ## OPTIONS
     * 
     * [--batch]
     * : The size of the batch. Default 100.
     * 
     * [--expiration]
     * : delete transient that have expired before X hours. Default 24.
     * 
     * [--timeout]
     * : set the maximum execution time in seconds. Default 30.
     * 
     * ## EXAMPLES
     *
     *  wp ninukis clearTransients
     *  
     * @param type $args
     * @param type $assoc_args
     */
    public function clearTransients($args = array(), $assoc_args = array()) {
        
        $fields = array('removed');

        $batch_size = array_key_exists( 'batch', $assoc_args ) ? intval($assoc_args['batch']) : 100;
        $expiration = array_key_exists( 'expiration', $assoc_args ) ? intval($assoc_args['expiration']) : 24;
        $timeout = array_key_exists( 'timeout', $assoc_args ) ? intval($assoc_args['timeout']) : 30;

        $formatter = new \WP_CLI\Formatter(
                $assoc_args, $fields);

        $result = NinukisCaching::clear_transients($expiration, $timeout, $batch_size);

        $result_info = array(
            'removed' => $result,
        );

        $formatter->display_item($result_info, $fields);
    }
    
    /**
     * Fix file permissions
     * @param type $args
     * @param type $assoc_args
     */
    public function fixPermissions($args = array(), $assoc_args = array()) {
        $result = NinukisApi::get_instance()->performFixFilePermissions(Ninukis_Plugin::isStagingEnv());
        if ( $result ) {
            WP_CLI::success("Successfully requested fix file permissions");
        } else {
            WP_CLI::error("failed to request fix file permissions !");
        }
    }
    
    /**
     * Purge site caches (varnish & object-cache)
     * 
     * @param type $args
     * @param type $assoc_args
     */
    public function purgeSite($args = array(), $assoc_args = array()) {
        if ( ! Ninukis_Plugin::isCachingEnabled() ) {
          WP_CLI::warning("Caching is not enabled for this site !");
          return;
        }
        $result = NinukisCaching::get_instance()->purgeAllCaches();
        if ( $result ) {
            WP_CLI::success("Successfully requested site cache purge");
        } else {
            WP_CLI::error("failed to request site cache purge !");
        }
    }
    
    /*
     * Purge the entire network's caches (varnish & object-cache)
     */
    public function purgeNetwork($args = array(), $assoc_args = array()) {
        if ( ! Ninukis_Plugin::isCachingEnabled() ) {
          WP_CLI::warning("Caching is not enabled for this site !");
          return;
        }
        if ( is_multisite() ) {
            $result = NinukisCaching::get_instance()->purge_network_varnish_cache();
            if ( $result ) {
                WP_CLI::success("Successfully requested network-wide cache purge");
            } else {
                WP_CLI::error("failed to request network-wide cache purge !");
            }
        } else {
            WP_CLI::warning("This is not an multisite !");
        }
    }
    
    /**
     * Manages multisite
     * 
     * ## OPTIONS
     * 
     * info             : displays multisite info
     * 
     * ## EXAMPLES
     * 
     * wp ninukis multisite info
     * @param type $args
     * @param type $assoc_args
     */
    public function multisite($args = array(), $assoc_args = array()) {
        $action = isset($args[0]) ? $args[0] : 'prompt';
        
        // check actions
        if (!in_array($action, array('info', 'prompt'))) {
            WP_CLI::error(sprintf(__('%s is not a valid cnd command.', 'ninukis'), $action));
        }
        
        switch ($action) {
            case 'info':
                $multisite_info = NinukisOperations::getMultisiteInfo();
                WP_CLI::print_value(json_encode($multisite_info));
                break;
            
            case 'prompt':
                WP_CLI::error(__('Please specify a valid action command', 'ninukis'));
                break;
        }
        
    }
    
    /**
     * Manages Smacking Service
     * 
     * ## OPTIONS
     * 
     * status                  : returns smacker status 
     * enable                  : enables smacker
     * disable                 : disables smacker
     * smack-all               : smacks all images
     * smack                   : smacks certain image
     * 
     * ## EXAMPLES
     * 
     * wp ninukis smacker profile [normal, aggressive]
     * wp ninukis smacker status
     * wp ninukis smacker enable
     * wp ninukis smacker disable
     * wp ninukis smack-all
     * wp ninukis smack wp-content/uploads/2016/12/logo.jpg
     * 
     * @param type $args
     * @param type $assoc_args
     * 
     */
    public function smacker($args = array(), $assoc_args = array()) {

        $action = isset($args[0]) ? $args[0] : 'prompt';
        
        // check actions
        if ( ! in_array( $action, array( 'status', 'enable', 'disable', 'configure', 'completed', 'smack-all', 'smack', 'prompt' ) ) ) {
          WP_CLI::error( sprintf( __( '%s is not a valid cnd command.', 'ninukis' ), $action ) );
        }
        // check action parameters
        if ( in_array( $action, array( 'smack' ) ) ) {
          if ( isset( $args[1] ) ) {
            $imagePath = $args[1];
          } else {
            WP_CLI::error(sprintf(__('You need to specify the path of the image to be smacked', 'ninukis')));
          }
        }
        
        if ( in_array( $action, array( 'configure' ) ) ) {
          if ( isset( $args[1] ) ) {
            $profile = $args[1];
          } else {
            WP_CLI::error(sprintf(__('You need to select one of the following profiles: normal,aggressive', 'ninukis')));
          }
        }

        if ( in_array( $action, array( 'completed' ) ) ) {
          if ( isset( $args[1] ) && isset( $args[2] )  && isset( $args[3] ) ) {
            $imagePath = $args[1];
            $percentage_saved = $args[2];
            $bytes_saved = $args[3];
          } else {
            WP_CLI::error(sprintf(__('You must provide the image path, size reduced (percentage) and size reduced (in bytes)', 'ninukis')));
          }
        }

        switch ($action) {
          case 'completed':
            NinukisSmacker::writeCompleted( $imagePath, $percentage_saved, $bytes_saved );
            WP_CLI::success(__("Smacking info written for image [" . $imagePath . "]"));
            break;
          case 'configure':
            NinukisSmacker::configureSmackerProfile( $profile );
            WP_CLI::success(__("Smacker profile set to [" . $profile . "]"));
            break;
          case 'status':
            if ( NinukisSmacker::isSmackerEnabled() ) {
              $current_profile = NinukisSmacker::getSmackerProfile();
              WP_CLI::line( __( sprintf( 'Smacking service is *enabled* with [%s] profile!', $current_profile ), 'ninukis' ) );
            } else {
              WP_CLI::line( __( 'Smacking service is disabled', 'ninukis' ) );
            }
            break;
          case 'enable':
            if( NinukisSmacker::isSmackerEnabled() ) {
              WP_CLI::error( __( 'Smacking service is already enabled on this site', 'ninukis'));
            } else {
              NinukisSmacker::updateSmackerStatus( true );
              WP_CLI::success(__("Smacking service has been enabled on this site.", 'ninukis'));
            }
            break;
          case 'disable':
            if( NinukisSmacker::isSmackerEnabled() ) {
              NinukisSmacker::updateSmackerStatus( false );
              WP_CLI::success(__("Smacking service has been *disabled* on this site.", 'ninukis'));
            } else {
              WP_CLI::error(__('Smacking service is *NOT* enabled on this site', 'ninukis'));
            }
            break;
          case 'smack-all':
            if( NinukisSmacker::isSmackerEnabled() ) {
              // here we need to call a method in NinukisSmacker
            } else {
              WP_CLI::error(__('Smacking service is *NOT* enabled on this site', 'ninukis'));
            }
            break;
          case 'smack':
            if( NinukisSmacker::isSmackerEnabled() ) {
              // here we need to call a method in NinukisSmacker
              //WP_CLI::line( __( "Trying to find attachement for $imagePath", 'ninukis' ) );
              
              // for now just γο blindly
              
              $result = NinukisApi::get_instance()->smackRequest( array( $imagePath ) );
              //$attachment = NinukisSmacker::get_attachment_id_by_path($imagePath);
              //WP_CLI::line( __( "Yes ! attachement is $attachment", 'ninukis' ) );
            } else {
              WP_CLI::error(__('Smacking service is *NOT* enabled on this site', 'ninukis'));
            }
            break;

          case 'prompt':
              WP_CLI::error(__('Please specify a valid action command', 'ninukis'));
              break;
        }
    }

}
