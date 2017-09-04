<?php

if (!defined('NINUKIS_PLUGIN_DIR')) {
    define( 'NINUKIS_PLUGIN_DIR', realpath(dirname(__FILE__) ) . '/..' );
}

if ( ! function_exists( 'ninukis_empty' ) ) {

  function ninukis_empty() {
    ; // empty
  }

  // Return a key if it appears in an array, else a default value
  function ninukis_el( $array, $key, $default = FALSE ) {
    if ( ! array_key_exists( $key, $array ) ) return $default;
    return $array[$key];
  }

  function ninukis_param( $key, $default = FALSE ) {
    return ninukis_el( $_REQUEST, $key, $default );
  }


}


class NinukisPluginCommon {

  /**
   * Returns the WP version
   *
   * This implemention is intentionally using PHP's include and not include_once
   * @deprecated since version 1.0.9
   * @see Ninukis_Plugin::getWPCoreVersion()
   */
  function getWPVersion() {
      return Ninukis_Plugin::getWPCoreVersion();
  }

  /**
   * Returns the WP version
   *
   * This implemention is intentionally using PHP's include and not include_once
   * @deprecated since version 1.0.9
   * @see
   */
  function getWPDBVersion() {
      return Ninukis_Plugin::getWPCoreDBVersion();
  }

  
  /**
  * Returns true if and only if we are running
  * in the staging enviroment (EWPDEV-311)
  * 
  * @deprecated since version 1.0.10
  * @see Ninukis_Plugin::isStagingEnv()  
  */
  public function isStaging() {
      return Ninukis_Plugin::isStagingEnv();
  }

  /**
   * Returns true if the CDN is enabled for this site
   * @deprecated since version 1.0.10
   */
  public function isCDNEnabled() {
    return NinukisCDN::isCDNEnabled();
  }
  
  /**
   * Returns true, if the global network CDN status is enabled. The global
   * @deprecated since version 1.0.10
   * @return boolean
   */
  public function isCDNNetworkEnabled() {
    return NinukisCDN::isCDNNetworkEnabled();
  }

  /**
  * Determine the CDN domain we should use for this site !
  * @deprecated since version 1.0.10
  */
  public function getCDNDomain() {
      return NinukisCDN::getCDNDomain();
  }

  /**
  * Checks if a given IP is known and allowed
  */
  public function is_allowed_ip($ip) {
    if($ip == '127.0.0.1' ||
      $ip == '188.226.162.8' || # ops1 @ DO
      $ip == '192.168.194.9' || # ops1 @ LINODE
      substr( $ip, 0, 8 ) == '192.168.' || # private IP address space is OK
      substr( $ip, 0, 3 ) == '10.' || # private IP address space is OK
      $ip == $_SERVER['SERVER_ADDR'] ) {
      return true;
    } else {
      return false;
    }
  }

  public static function get_path_trailing_slash( $path ) {
    if ( substr( $path, -1 ) != '/' )
        return $path . '/';
    return $path;
  }

  // Is the object cache enabled?
  public function is_object_cache_enabled() {
    #global $memcached_servers;
    if ( ! defined('WP_CACHE') ) return false;
    if ( ! WP_CACHE ) return false;
    // if ( 0 == count($memcached_servers) ) return false;
    $path = WP_CONTENT_DIR . "/object-cache.php";
    return file_exists($path);
  }

  // Sets object cache to be enabled or disabled.
  public function set_object_cache_enabled( $state ) {
    $path = WP_CONTENT_DIR . "/object-cache.php";
    Ninukis_Plugin::log_me("path is $path");
    Ninukis_Plugin::log_me(NINUKIS_PLUGIN_DIR);
    if ( $state ) {
      copy(NINUKIS_PLUGIN_DIR."/object-cache.php",$path);    // copy our version into place
    } else {
      unlink($path);      // remove the object cache file
    }
  }

  /**
   * @deprecated since version 1.0.8
   * @param type $url
   * @param type $parameters
   * @return type
   */
  public function asyncInvokeBusAPI($url, $parameters = null) {
      return NinukisApi::get_instance()->invokeBusAPI($url, $parameters, 15, false);
  }

  /**
   * @deprecated since version 1.0.8
   * @param type $url
   * @param type $parameters
   * @param type $timeout
   * @param type $blocking
   * @return type
   */
  public function invokeBusAPI($url, $parameters = null, $timeout = 15, $blocking = true) {
      return NinukisApi::get_instance()->invokeBusAPI($url, $parameters, $timeout, $blocking);
  }

  /**
   * @deprecated since version 1.0.8
   * @param type $url
   * @param type $parameters
   * @param type $timeout
   * @param type $blocking
   * @return type
   */
  public function postJSONToBusAPI($url, $parameters = null, $timeout = 30, $blocking = true) {
      return  NinukisApi::get_instance()->postJSONToBusAPI($url, $parameters, $timeout, $blocking);
  }

}
