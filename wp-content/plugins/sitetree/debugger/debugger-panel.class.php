<?php
/**
 * @package SiteTree
 * @subpackage Debugger
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */


class DebuggerPanel extends Debug_Bar_Panel {
	/**
	 * @since 1.0
	 * @var array
	 */
	private $log = array();
	
	/**
	 * @since 1.0
	 * @var bool
	 */
	private $rendered = false;
	
	/**
	 * @since 1.0
	 * @var bool
	 */
	private static $stylesEnqueued = false;

	/**
	 * Factory method.
	 *
	 * @since 1.0
	 *
	 * @param string $title
	 * @return object
	 */
	public static function newPanel( $title = '' ) {
		if (! self::$stylesEnqueued ) {
			self::$stylesEnqueued = true;
			
			wp_enqueue_style( 'debugger-panel', plugins_url( 'debugger-panel.css', __FILE__ ), null, '1.0' );
		}
		
		return new self( $title );
	}
	
	/**
	 * @since 1.0
	 * @param string $title
	 */
	private function __construct( $title ) { $this->title( $title ); }
	
	/**
	 * @since 1.0
	 */
	public function render() {
		$this->rendered = (bool) $this->log;
		
		$output = '<ol id="debugger-panel">';
		
		foreach( $this->log as $entry ) {
			$output .= '<li><a href="#">' . $entry['msg'] . ' <span>' . $entry['info'] . '</span></a> ';
			$output .= $entry['inline'] ? $entry['data'] : ( '<pre>' . esc_attr( $entry['data'] ) . '</pre>' );
			$output .= '</li>';
		}
		
		echo $output . '</ol>';
	}
	
	/**
	 * @since 1.0
	 */
	public function rendered() { return $this->rendered; }
	
	/**
	 * @since 1.0
	 *
	 * @param string $msg 
	 * @param string $data
	 * @param string $info
	 * @param bool $inline
	 */
	public function enqueueLog( $msg, $data, $info, $inline ) {
		$this->log[] = array( 'msg'  => $msg, 'data' => $data, 'info' => $info, 'inline' => $inline );
	}
}
?>