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

if (!class_exists('NinukisCDN')) {

    class NinukisCDN {

        /**
         * Holds the singleton instance of this class
         *
         * @since 1.0.10
         * @var NinukisCDN
         */
        private static $instance = false;
        
        /**
         * Holds the value of the cdn domain name
         * 
         * @var string as the cdn domain name 
         */
        public $cdnDomain = NULL;
        
        
        /**
         * Holds the value of the source domain that needs to be replaced
         * @var string as the source domain
         */
        public $sourceDomain = NULL;
        
        /**
         * Singleton
         *
         * @since 1.0.10
         * @static
         * @return NinukisCDN
         */
        public static function get_instance() {
            if (!self::$instance) {
                self::$instance = new NinukisCDN;
            }

            return self::$instance;
        }

        /**
         * Constructor for singleton
         *
         * @since 1.0.10
         * @return NinukisCDN
         */
        private function __construct() {
            /* if CDN is enabled, then enable CDN rewrite rules
             */
            if (NinukisCDN::isCDNEnabled()) {
                $this->cdnDomain = NinukisCDN::getCDNDomain();
                /* compute the source domain that needs to be rewritten */
                if ( defined('NINUKIS_CDN_USE_HOME_DOMAIN') && NINUKIS_CDN_USE_HOME_DOMAIN ) {
                    $this->sourceDomain = parse_url(home_url(), PHP_URL_HOST);
                } else {
                    $this->sourceDomain = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_SPECIAL_CHARS);
                }
                add_filter('ninukis_filter_output', array($this, 'do_filter_output'), 910, 1); # add the filter 'last' in the chain
            }
        }

        /**
         * Returns true if the CDN is enabled for this site
         */
        public static function isCDNEnabled($blogId = NULL) {
            # check if CDN is globaly disabled
            if (defined('WP_NINUKIS_CDN_DISABLED') && WP_NINUKIS_CDN_DISABLED)
                return false;
            $option = Ninukis_Plugin::getWPOption($blogId, 'ninukis-cdn-enabled', 'disabled');
            if ($option == "enabled") {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Returns true, if the global network CDN status is enabled. The global
         * 
         * @return boolean
         */
        public static function isCDNNetworkEnabled() {
            # check if CDN is globaly disabled
            if (defined('WP_NINUKIS_CDN_DISABLED') && WP_NINUKIS_CDN_DISABLED)
                return false;
            return Ninukis_Plugin::getWPSiteOption('ninukis-network-cdn-enabled', FALSE);
        }

        /**
         * Update the CDN status
         * @param type $enabled
         */
        public static function updateCDNStatus($enabled = FALSE, $blogId = NULL) {
            $status = $enabled ? "enabled" : "disabled";
            Ninukis_Plugin::setWPOption($blogId, 'ninukis-cdn-enabled', $status);
        }

        /**
         * Configures CDN for the site (or blog ID)
         * @param type $isCDNCapable
         * @param type $publicCDNDomain
         * @param type $blogId
         * @return boolean
         */
        public static function configureCDN($isCDNCapable, $publicCDNDomain, $blogId = NULL) {
            if (is_multisite()) {
                /* this is a multisite installation, blogId is signifficat */
                Ninukis_Plugin::log_me("CDN update request received for blog '$blogId' with CDN rewrite '$publicCDNDomain' with status '$isCDNCapable'");
                Ninukis_Plugin::setWPOption($blogId, 'ninukis-cdn-enabled', $isCDNCapable);
                Ninukis_Plugin::setWPOption($blogId, 'ninukis-cdn-domain', $publicCDNDomain);
                /* PWNP-21 - handle the global network CDN flag */
                if ('enabled' === $isCDNCapable) {
                    /* CDN has been enabled for one blog, so the global CDN flag
                     * can be set to True  */
                    Ninukis_Plugin::setWPSiteOption('ninukis-network-cdn-enabled', TRUE);
                } else {
                    /* CDN has been just disabled for one blog in the network, so
                     * check if the global CDN flag should still be true
                     */
                    $globalCDNFlag = FALSE; // let's start by assuming FALSE

                    /* we will iterate only for the first 100 sites. If the MS is
                     * bigger than that, then we will address this with a support 
                     * ticket
                     */
                    $args = array(
                        'limit' => 100,
                        'offset' => 0,
                    );

                    $blogs = wp_get_sites($args);
                    foreach ($blogs as $blogid => $blog) {
                        $globalCDNFlag = 'enabled' === Ninukis_Plugin::getWPOption($blog['blog_id'], 'ninukis-cdn-enabled', 'disabled');
                        if (true === $globalCDNFlag)
                            break; // we found a blog with CDN enabled, exit the loop
                    }
                    Ninukis_Plugin::setWPSiteOption('ninukis-network-cdn-enabled', $globalCDNFlag);
                }
            } else {
                Ninukis_Plugin::log_me("CDN update request received with CDN rewrite '$publicCDNDomain' with status '$isCDNCapable'");
                Ninukis_Plugin::setWPSiteOption('ninukis-cdn-enabled', $isCDNCapable);
                Ninukis_Plugin::setWPSiteOption('ninukis-cdn-domain', $publicCDNDomain);
            }

            return true;
        }

        /*
         * Determine the CDN domain we should use for this site !
         */

        public static function getCDNDomain( $blogId = NULL) {
            $domain = Ninukis_Plugin::getWPOption($blogId, 'ninukis-cdn-domain', null);
            if ($domain) {
                return $domain;
            }
            # ok, this is not good, go with the default domain
            if (defined('WP_NINUKIS_WP_NAME') && WP_NINUKIS_WP_NAME) {
                return WP_NINUKIS_WP_NAME . ".onpressidium.com";
            } else
                return null;
        }

        /**
         * Filters the HTML output replacing URL for CDN support
         * @param type $html
         * @return type
         */
        public function do_filter_output($html) {
            $is_admin = is_admin();
            if (NinukisCDN::isCDNEnabled() && $this->cdnDomain && !$is_admin) {
                                
                $httpVerb = strtoupper($_SERVER['REQUEST_METHOD']);

                /**
                 * Set the rules on which we won't do any processing at all
                 */
                $doNotProcessRules = array(
                    '$httpVerb!=="GET" && $httpVerb!=="POST"',
                    'Ninukis_Plugin::isStagingEnv()',
                    'strlen($html)<120',
                    '$this->hasValidContentType()===FALSE',
                );

                foreach ($doNotProcessRules as $noProcessRule) {
                    //Ninukis_Plugin::log_me("Evaluating : " . $noProcessRule);
                    $evalExpressionResult = eval('return ' . $noProcessRule . ';');
                    if ($evalExpressionResult) {
                        #Ninukis_Plugin::log_me("Found 'doNotProcessRules' matching: $noProcessRule");
                        return $html;
                    }
                }

                /**
                 * %\b(?:(?:src|href|value|data)\s*=|\burl\s*)[\('\"\s]+((?:https?:)?/?/)([^'\"\)]+)([^'\"\)]*)%i
                 * Extract all possible URLs
                 * 1st Match: (?:(?:src|href|value|data)\s*=|\burl\s*) Non-capturing group
                 *      1st alt: (?:src|href|value|data)
                 *              Alternatives src|href|value|data THEN match 0 to unlimited white space chars (greedy) THEN match = character
                 *      2nd alt: \burl\s*
                 *              \b anchor THEN match url THEN match 0 to unlimited white space chars (greedy)
                 * 2nd Match: [\('\"\s]+
                 *      literal match of characters ( ' "
                 *      from 1 to unlimited, match any white space character
                 *
                 * 1st capturing: ((?:https?:)?/?/)     Capturing group
                 *      (?:https?:)?    1st non-capturing
                 *      literal match of characters / /
                 *
                 * 2nd capturing group: ([^/'\"\)]+)
                 *      from 1 to unlimited, match a single character not present in the list of characters /' " )
                 *
                 * 3rd capturing group: ([^'\"\)]*)     Capturing group
                 *      from 0 to unlimited, matches a single character not in present in the list
                 *
                 */
                //$extractUrlRegex = "%\\b(?:(?:src|href|value|data)\s*=|\burl\s*)[\('\\" . '\"' . "\s]+((?:https?:)?/?/)([^/'\"\)]+)([^'\"\)]*)%i";
                //improved by PWNP-57 & PWNP-115
                $extractUrlRegex = "%\\b(?:(?:content|src|srcset|href|value|data[a-z-]+)\s*=|\burl\s*)[\('\\" . '\"' . "\s]+((?:https?:)?/?/|wp-content/)([^/'\"\)]+)([^'\"\)]*)%i";                
                //Extract and process urls
                $html = preg_replace_callback($extractUrlRegex, array($this, 'processMatchForCDNReplacement'), $html);
                //Ninukis_Plugin::log_me($html);
            }

            return $html;
        }

        /**
         * The preg_replace_callback() callback implementation of the actual
         * domain replacement for CDN supported installs
         * @return string as the replaced string if a CDN rule matched OR the original if no CDN rule matched
         */
        protected function processMatchForCDNReplacement($match) {
            $currentDomain = $this->sourceDomain;       
            //extract $match in local variables
            $s = $match[1]; //scheme
            $d = $match[2]; //domain
            $p = $match[3]; //URI
            
//            Ninukis_Plugin::log_me("CDN REWRITE: match[1]=$s and match[2]=$d and match[3]=$p :");

            //check if this is a match of a naked domain (i.e. src="/wp-content/uploads/lala.jpg")
            //scheme can be "http://" or "https://" or "//" anything else is a relative path in our filesystem
            if (strpos($s, "http") !== 0 && $s!=="//") {
//                Ninukis_Plugin::log_me("NAKED CDN REPLACE ORIGINAL: " . $match[0]);
                if ($s !== "/") { //relative link starting without / char
                    //fix $p and $d values for later use
                    $p = "/" . $match[1] . $d . $p;
                    $d = $currentDomain;
                    $match[0] = str_replace('="' . $match[1], '="//' . $currentDomain . '/' . $match[1], $match[0]);
                    $match[0] = str_replace("='" . $match[1], "='//" . $currentDomain . "/" . $match[1], $match[0]);
//                    Ninukis_Plugin::log_me("NAKED CDN REPLACED P=$p");
                } else { //relative link starting with / char
                    //fix $p and $d values for later use
                    $p = "/" . $d . $p;
                    $d = $currentDomain;
                    $match[0] = str_replace('="/', '="//' . $currentDomain . '/', $match[0]);
                    $match[0] = str_replace("='/", "='//" . $currentDomain . "/", $match[0]);
//                    Ninukis_Plugin::log_me("NAKED CDN REPLACED P=$p");
                }
//                Ninukis_Plugin::log_me("NAKED CDN REPLACED: " . $match[0]);
            }
            //check deny replacement uris
            global $prsdm_deny_cdn_uris;
            if (!empty($prsdm_deny_cdn_uris)) {
                foreach ($prsdm_deny_cdn_uris as $denyURI) {
                    if (preg_match('#' . $denyURI . '#', $match[3])) {
                        //ninukis_Plugin::log_me("has CDN deny URI for " . $match[3]); //debug
                        return $match[0];
                    }
                }
            }
            //get the CDN matching rules and iterate them
            $rules = $this->getCDNreplacementRules();
            //http://snipplr.com/view/758/ @todo replace strcasecmp with === comparison
            if (strcasecmp($currentDomain, $d) == 0) {
                //iterate and apply each CDN replacement rule
                foreach ($rules as $k => $rule) {
                    if (preg_match($rule, $p)) {
                        //Ninukis_Plugin::log_me("Found Matching CDN Replacement Rule #$k: Replacing '$currentDomain' with '".$this->cdnDomain."' for URI='$p'"); //debug
                        return str_replace($currentDomain, $this->cdnDomain, $match[0]);
                    } else {
                        //Ninukis_Plugin::log_me("Didn't find a Match for CDN Replacement Rule #$k and URI='$p'"); //debug
                        return $match[0];
                    }
                }
            } else {
                //Ninukis_Plugin::log_me("Didn't find matching DOMAIN, so I DON'T do anything for '$s$d$p'\n");
                return $match[0];
            }
        }

        /**
         * This method iterates through http headers until it finds
         * the Content-Type header on which we validate that this is
         * a plain html response
         * @return type
         */
        protected function hasValidContentType() {
            $textContentType = false;
            foreach (headers_list() as $header)
                if (preg_match("#^content-type:\\s*text/#i", $header)) {
                    $textContentType = true;
                    break;
                }
            return $textContentType;
        }

        protected function getCDNreplacementRules() {
            return array(
                /**
                 * rule #1
                 * better be on the safe side and replace cdn domain only in known directories
                 * for known filetypes
                 */
                '%/(?:wp-content/(?:themes|plugins|uploads|files|wptouch-data|gallery)|wp-includes|wp-admin|files|images|img|css|js|fancybox|assets)/.+\\.(?:jpe?g|gif|png|css|js|ico|zip|7z|tgz|gz|rar|bz2|do[ct][mx]?|xl[ast][bmx]?|exe|pdf|p[op][ast][mx]?|sld[xm]?|thmx?|txt|tar|midi?|wav|bmp|rtf|avi|mp\\d|mpg|iso|mov|djvu|dmg|flac|r70|mdf|chm|sisx|sis|flv|thm|bin|swf|cert|otf|ttf|eot|svgx?|woff|jar|class|log|web[ma])%i'
                    //rule #2 ...TBD
            );
        }

    }

}

