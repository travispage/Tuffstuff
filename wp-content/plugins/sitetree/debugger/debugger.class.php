<?php
/**
 * @package SiteTree
 * @subpackage Debugger
 * @author Luigi Cavalieri
 * @version 1.0
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */


/**
 * @since 1.0
 */
class Debugger {
	/**
	 * Singleton instance
	 *
	 * @since 1.0
	 * @var object
	 */
	protected static $debugger;
	
	/**
	 * Array of all the Debugger instances
	 *
	 * @since 1.0
	 * @var array
	 */
	protected static $instances = array();
	
	/**
	 * @since 1.0
	 * @var string
	 */
	protected static $className;
	
	/**
	 * Instance of the SiteTreeDebugBar class
	 *
	 * @since 1.0
	 * @var object
	 */
	protected $debugBarPanel;
	
	/**
	 * @since 1.0
	 * @var string
	 */
	protected $panelTitle;
	
	/**
	 * Path used to truncate the file path stored into the property $callerInfo.
	 *
	 * @since 1.0 
	 * @see traceCallerInfo()
	 *
	 * @var string
	 */
	protected $pathBase;
	
	/**
	 * @since 1.0
	 * @var array
	 */
	protected $trace;
	
	/**
	 * @since 1.0
	 * @var array
	 */
	protected $callerInfo;
	
	/**
	 * Collection of formatted strings and string representations of arrays/objects.
	 *
	 * @since 1.0
	 * @var string
	 */
	protected $formattedData = '';
	
	/**
	 * Factory method: returns a new instance.
	 *
	 * @since 1.0
	 *
	 * @param string $panel_title
	 * @param string $path_base
	 */
	public static function init( $panel_title = '', $path_base = '' ) {
		if (! $panel_title )
			$panelTitle = static::className();
			
		if (! $path_base )
			$pathBase = WP_CONTENT_DIR;
			
		$instance = new static( $panel_title, $path_base );
		self::$instances[] = $instance;
	
		if (! self::$debugger ) {
			self::$debugger = $instance;
			
			register_shutdown_function( array( static::className(), 'renderFormattedData' ) );
		}
		
		if ( class_exists( 'Debug_Bar' ) )
			add_filter( 'debug_bar_panels', array( $instance, 'loadDebugBarPanel' ) );
			
		return $instance;
	}
	
	/**
	 * Returns the class instance defined in the class scope
	 *
	 * @since 1.0
	 * @return object
	 */
	public static function invoke() {
		if (! self::$debugger )
			self::$debugger = static::init();
		
		return self::$debugger;
	}
	
	/**
	 * Constructor method
	 *
	 * @since 1.0
	 *
	 * @param string $panel_title
	 * @param string $path_base
	 */
	private function __construct( $panel_title, $path_base ) {
		$this->pathBase   = rtrim( $path_base, '/' ) . '/';
		$this->panelTitle = $panel_title;
	}
	
	/**
	 * @since 1.0
	 *
	 * @param string $name
	 * @param mixed $args
	 */
	public function __call( $name, $args ) {
		$overloaded_method = '_' . $name;
	
		if ( method_exists( $this, $overloaded_method ) ) {
			$this->traceCallerInfo();
				
			return call_user_func_array( array( $this, $overloaded_method ), $args );
		}
		
		if ( method_exists( $this, $name ) )
			return call_user_func_array( array( $this, $name ), $args );
		
		trigger_error( 'Call to undefined method ' . static::className() . '::' . $name . '()', E_USER_ERROR );
	}
	
	/**
	 * @since 1.0
	 *
	 * @param string $name
	 * @param mixed $args
	 */
	public static function __callStatic( $name, $args ) {
		$class = static::className();
		$overloaded_method = '_' . $name;
		
		if ( method_exists( $class, $overloaded_method ) ) {
			$debugger = static::invoke();
			$debugger->traceCallerInfo();
			
			return call_user_func_array( array( $debugger, $overloaded_method ), $args );
		}
		
		if ( method_exists( $class, $name ) )
			return call_user_func_array( array( $class, $name ), $args );
			
		trigger_error( 'Call to undefined method ' . $class . '::' . $name . '()', E_USER_ERROR );
	}
	
	/**
	 * @since 1.0
	 * @return string
	 */
	protected static function className() {
		if (! self::$className )
			self::$className = get_called_class();
			
		return self::$className;
	}
	
	/**
	 * @since 1.0
	 * @return bool|void False if there are no data to render, void otherwise
	 */
	public static function renderFormattedData() {
		// Is the loading page an HTML document?
		$encode	 = false;
		$headers = headers_list();
		
		foreach ( $headers as $header ) {
			if ( strpos( $header, 'text/html' ) !== false ) {
				$encode = true;
				break;
			}
		}
		
		// Print formatted data
		$output  = '';
		
		foreach ( self::$instances as $instance )
			$output .= $instance->formattedData();
		
		if (! $output )
			return false;
			
		if ( $encode )
			echo '<pre>' . esc_attr( $output ) . '</pre>';
		else
			echo "\n\n" . str_repeat( '-', 100 ) . "\n" . $output;
	}
	
	/**
	 * @since 1.0
	 *
	 * @param array $panels
	 * @return array
	 */
	public function &loadDebugBarPanel( $panels ) {
		include_once( 'debugger-panel.class.php' );
		
		$this->debugBarPanel = DebuggerPanel::newPanel( $this->panelTitle );
		
		array_unshift( $panels, $this->debugBarPanel );
		
		return $panels;
	}
	
	/**
	 *
	 * @since 1.0
	 * @return string
	 */
	protected function formattedData() {
		if ( $this->debugBarPanel && $this->debugBarPanel->rendered() )
			return '';
		
		return $this->formattedData;
	}
	
	/**
	 * Dumps any data or set of data passed as arguments.
	 *
	 * @since 1.0
	 *
	 * @param mixed $data,... Unlimited optional number of data to debug
	 * @return bool
	 */
	protected function _debug() {
		if (! ( $args = func_get_args() ) )
			return $this->output( $this->callerInfo['name'] );
			
		$arg_pos = 0;
		
		foreach ( $args as &$data ) {
			switch ( $data_type = gettype($data) ) {
				case 'boolean':
					$data_type = 'bool';
					$data =  $data ? 'true' : 'false';
					break;
					
				case 'array':
				case 'object':
					$data = var_export( $data, true );
					break;
					
				case 'NULL':
					$data_type = 'null';
					$data = $this->callerInfo['name'];
					break;
					
				case 'integer':
					$data_type = 'int';
					
				case 'double':
					if ( $data >= 946684800 ) // 2000-01-01
						$data .= '  =>  ' . gmdate( 'Y-m-d H:i:s', $data );
					break;
			}
			
			// Index of the argument that is being processed
			$index = ( $arg_pos++ > 0 ) ? ( ':' . $arg_pos ) : '';
				
			if (! $this->output( $data, false, $index . ' (' . $data_type . ')' ) )
				return false;
		}
		
		return true;
	}
	
	/**
	 * Prints out a message.
	 *
	 * @since 1.0
	 *
	 * @param string $msg Optional string to print.
	 * @return bool
	 */
	protected function _log( $msg = '' ) {
		return $this->output( $msg, true,  ' ' . $this->callerInfo['name'] );
	}
	
	/**
	 * Prints out the backtrace of the function where is called.
	 *
	 * @since 1.0
	 * @return bool
	 */
	protected function _backtrace() {
		$data		= '';
		$num_calls  = count( $this->trace );
		
		// We use the info contained in the $callerInfo array as a starting point for the backtracing
		for ( $i = ++$this->callerInfo['trace_index'], $index = 1; $i < $num_calls; $i++, $index++ ) {
			$data .= "\n\n" . $index . '. ';
		
			if ( isset( $this->trace[$i]['class'] ) )
				$data .= $this->trace[$i]['class'] . $this->trace[$i]['type'];
			
			$data .= $this->trace[$i]['function'] . "()\n   Line: ";
			
			if ( isset( $this->trace[$i]['line'] ) )
				$data .= $this->trace[$i]['line'];
				
			$data .= "\tFile: ";
			
			if ( isset( $this->trace[$i]['file'] ) )
				$data .= str_replace( array( ABSPATH, $this->pathBase ), '~/', $this->trace[$i]['file'] );
		}
		
		return $this->output( $data, false, ': ' . $this->callerInfo['name'] . ' Backtrace' );
	}
		
	/**
	 * Populates the array $callerInfo with a set of information about the function that 
	 * called one of the two debugger methods.
	 *
	 * @since 1.0
	 */
	private function traceCallerInfo() {
		$index = 3;
		$this->trace = &debug_backtrace();
		
		// We check if the debugger method has been called by a function with the same name and
		// we set $index (of the array element from which to extract the function name) accordingly
		$num_calls  = count( $this->trace );
		
		for ( $i = $index; $i < $num_calls; $i++ ) {
			if ( $this->trace[$i]['function'] ==  $this->trace[2]['function'] ) {
				$index = $i + 1;
				break;
			}
		}
		
		// The caller info array must be reset every time a debugger function is called.
		$this->callerInfo = array( 'name' => '', 'trace_index' => $index );
		
		if ( isset( $this->trace[$index]['class'] ) )
			$this->callerInfo['name'] = $this->trace[$index]['class'] . $this->trace[$index]['type'];
		
		$this->callerInfo['name'] .= $this->trace[$index]['function'] . '()';
		
		// We have to decrement $index by one to retrieve the line number and the file path
		$this->callerInfo['line'] = $this->trace[--$index]['line'];
		$this->callerInfo['file'] = str_replace( $this->pathBase, '~/', $this->trace[$index]['file'] );
	}
	
	/**
	 * @since 1.0
	 *
	 * @param string $data
	 * @param bool $inline Controls how the data must be rendered into the DebugBar panel
	 * @param string $add_msg
	 * @return bool
	 */
	protected function output( $data, $inline = true, $add_msg = '' ) {
		$msg = '#' . $this->callerInfo['line'] . $add_msg;
		
		if ( $this->debugBarPanel )
			$this->debugBarPanel->enqueueLog( $msg, $data, $this->callerInfo['file'], $inline );
		
		$msg .= ' | ';
		
		if ( preg_match( '/\n|\r|\t/', $data ) ) {
			$msg .= '-';
			$this->formattedData .= $data . "\n\n\n";
		}
		else { $msg .= $data; }
			
		return error_log( $msg );
	}
}
?>