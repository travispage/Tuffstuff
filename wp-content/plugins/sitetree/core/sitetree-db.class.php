<?php
/**
 * @package SiteTree
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */


/**
 * @since 1.2
 */
final class SiteTreeDB extends SiteTreeDBGatekeeper {
	/**
	 * @since 1.4
	 * @var string
	 */
	protected $namespace = 'sitetree';
	
	/**
	 * @since 1.4
	 * @var int
	 */
	private $cacheLifetime =  2592000; // 30 days;
	
	/**
	 * 
	 *
	 * @since 1.4
	 */
	public function __clone() { wp_die( __('Cheatin&#8217; uh?') ); }

	/**
	 * 
	 *
	 * @since 1.4
	 */
	public function __wakeup() { wp_die( __('Cheatin&#8217; uh?') ); }
	
	/**
	 *
	 * @since 1.4
	 *
	 * @param mixed $data
	 * @param string $key
	 * @return bool
	 */
	public function setCache( $key, $data ) {
		$this->setOption( $key, true, 'cache' );
		
		return $this->setTransient( $key , $data, $this->cacheLifetime );
	}
	
	/**
	 *
	 * @since 1.4
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getCache( $key ) {
		if (! $this->isCacheAlive( $key ) )
			return false;
			
		return $this->getTransient( $key );
	}
	
	/**
	 *
	 * @since 1.4
	 *
	 */
	public function invalidateCache( $key ) {
		return $this->setOption( $key, false, 'cache' );
	}
	
	/**
	 *
	 * @since 1.4
	 *
	 */
	public function isCacheAlive( $key ) {
		return (bool) $this->getOption( $key, false, 'cache' );
	}
	
	/**
	 *
	 * @since 1.4
	 *
	 * @return bool
	 */
	public function xmlEnabled() {
		return (bool) $this->getOption( 'enable_xml' );
	}
	
	/**
	 *
	 * @since 1.4
	 *
	 * @return bool
	 */
	public function html5Enabled() {
		return (bool) $this->getOption( 'page_for_sitemap' );
	}
}


/**
 * Abstraction layer that manages the interaction with the WordPress DB APIs.
 *
 * @since 1.5
 */
class SiteTreeDBGatekeeper {
	/**
	 *
	 *
	 * @since 1.5
	 * @var string
	 */
	protected $namespace;
	
	/**
	 * Options array.
	 *
	 * @since 1.5
	 * @var array
	 */
	protected $options;
	
	/**
	 * Post metadata retrieved from the database.
	 *
	 * @since 1.5
	 * @var array
	 */
	protected $metadata;
	
	/**
	 *
	 *
	 * @since 1.5
	 * @var bool
	 */
	protected $consolidate = false;
	
	/**
	 * Contructor method.
	 *
	 * @since 1.5
	 * @param string $namespace
	 */
	public function __construct( $namespace = '' ) {
		// The value of the $namespace property can be hardcoded in a parent class.
		if (! $this->namespace )
			$this->namespace = $namespace;
			
		if (! is_array( $this->options = get_option( $this->namespace ) ) )
			$this->options = array();
	}
	
	/**
	 * Updates the plugin options. 
	 *
	 * @since 1.5
	 * @param array $options
	 * @return bool
	 */
	public function setOptions( $options, $recoursive = false ) {
		if ( $recoursive ) {
			$success = false;
			
			foreach ( $options as $key => &$option ) {
				if ( $this->setOption( $key, $option ) )
					$success = true;
			}
			
			return $success;
		}
	
		if ( $this->options === $options )
			return false;
			
		$this->options = $options;
		
		return ( $this->consolidate = true );
	}
	
	/**
	 * Returns a copy of the whole @see $options array.
	 *
	 * @since 1.5
	 * @return array
	 */
	public function getOptions() { return $this->options; }
	
	/**
	 * Stores a value into the multidimensional @see $options array.
	 *
	 * @since 1.5
	 * @param string $key
	 * @param mixed $value
	 * @param string $group
	 * @param string $context
	 * @return bool
	 */
	public function setOption( $key, $value, $group = '', $context = '' ) {
		$old_value = $this->getOption( $key, null, $group, $context );
		
		if ( $value === $old_value  )
			return false;
			
		if ( $value === null )
			$value = false;
		elseif ( is_object( $value ) )
			$value = clone $value;
		
		if (! $group )
			$this->options[$key] = $value;
		elseif (! $context )
			$this->options[$group][$key] = $value;
		else
			$this->options[$context][$group][$key] = $value;
			
		return ( $this->consolidate = true );
	}
	
	/**
	 * Retrieves an option value stored up to the third level into the 
	 * multidimensional @see $options array
	 *
	 * @since 1.5
	 *
	 * @param string $key
	 * @param mixed $default
	 * @param string $group
	 * @param string $context
	 * @return mixed
	 */
	public function getOption( $key, $default = false, $group = '', $context = '' ) {
		$option = null;
		
		if (! $group ) {
			if ( isset( $this->options[$key] ) ) {
				if ( is_object( $this->options[$key] ) )
					return clone $this->options[$key];
				else
					return $this->options[$key];
			}
		}
		elseif (! $context ) {
			if ( isset( $this->options[$group][$key] ) ) {
				if ( is_object( $this->options[$group][$key] ) )
					return clone $this->options[$group][$key];
				else
					return $this->options[$group][$key];
			}
		}
		elseif ( isset( $this->options[$context][$group][$key] ) ) {
			if ( is_object( $this->options[$context][$group][$key] ) )
				return clone $this->options[$context][$group][$key];
			else
				return $this->options[$context][$group][$key];
		}
		
		return $default;
	}
	
	/**
	 *
	 * @since 1.5
	 *
	 * @param string $key
	 * @param string $group
	 * @param string $context
	 * @return bool
	 */
	public function deleteOption( $key, $group = '', $context = '' ) {
		if ( $this->getOption( $key, null, $group, $context ) === null )
			return false;
		
		if (! $group )
			unset( $this->options[$key] );
		elseif (! $context )
			unset( $this->options[$group][$key] );
		else
			unset( $this->options[$context][$group][$key] );
		
		return ( $this->consolidate = true );
	}
	
	/**
	 * Checks whether or not the @see $options array is empty.
	 *
	 * @since 1.5
	 * @return bool
	 */
	public function optionsEmpty() { return !(bool) $this->options; }
	
	/**
	 *
	 * @since 1.5
	 *
	 * @param string $name
	 * @param mixed $value
	 * @param int $expiration
	 * @return bool
	 */
	public function setTransient( $name, $value, $expiration = 30 ) {
		$transient_name = $this->namespace . '_' . $name;
	
		return set_transient( $transient_name , $value, $expiration );
	}
	
	/**
	 *
	 * @since 1.5
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function getTransient( $name ) {
		return get_transient( $this->namespace . '_' . $name );
	}
	
	/**
	 *
	 * @since 1.5
	 *
	 * @param string $name
	 * @return bool
	 */
	public function deleteTransient( $name ) {
		return delete_transient( $this->namespace . '_' . $name );
	}
	
	/**
	 *
	 * @since 1.5
	 *
	 * @param int $post_id
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
	public function setPostMeta( $post_id, $key, $value ) {
		$meta_key = '_' . $this->namespace . '_' . $key;
	
		return update_metadata( 'post', $post_id, $meta_key, $value );
	}
	
	/**
	 *
	 * @since 1.5
	 *
	 * @param int $post_id
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getPostMeta( $post_id, $key, $default = false ) {
		if ( strpos( $key, '_' ) !== 0 )
			$key = '_' . $this->namespace . '_' . $key;
		
		if (! isset( $this->metadata[$post_id] ) )
			$this->metadata[$post_id] = get_metadata( 'post', $post_id );
		
		if ( isset( $this->metadata[$post_id][$key][0] ) )
			return maybe_unserialize( $this->metadata[$post_id][$key][0] );
		
		return $default;
	}
	
	/**
	 *
	 *
	 * @since 1.5
	 *
	 * @param int $post_id
	 * @param string $key
	 * @return bool
	 */
	public function deletePostMeta( $post_id, $key ) {
		$meta_key = '_' . $this->namespace . '_' . $key;
		
		unset( $this->metadata[$post_id][$meta_key] );
		
		return delete_metadata( 'post', $post_id, $meta_key );
	}
	
	/**
	 *
	 * @since 1.5
	 *
	 * @return bool|int
	 */
	public function consolidate() {
		if ( $this->consolidate )
			return update_option( $this->namespace, $this->options );
		
		return -1;
	}
}
?>