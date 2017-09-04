<?php
/**
 * @package SiteTree
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */
 

/**
 * @since 1.5
 */
final class SiteTreePingController {
	/**
	 * @since 1.5
	 * @var object
	 */
	private $plugin;
	
	/**
	 * @since 1.5
	 * @var object
	 */
	private $db;
	
	/**
	 * Persistent object. Instance of SiteTreePingState
	 *
	 * @since 1.5
	 * @var object
	 */
	private $pingState;

	/**
	 * @since 1.5
	 * @var array
	 */
	private $targets = array(
		'google' => 'http://www.google.com/webmasters/tools/ping?sitemap=',
		'bing'	 => 'http://www.bing.com/webmaster/ping.aspx?sitemap='
	);
	
	/**
	 * Array of information about the current state of the @see $pingState object.
	 *
	 * The first element is a code (not necessarily the same as the status code set 
	 * in the $pingState object) that relates to the general state on the $pingState object.
	 *
	 * The second element is an array of stati, each one containing a status message, that can be
	 * displayed to the user, and a boolean flag that indicates whether or not the ping whose it represents 
	 * the state, actually is a scheduled event that can be cancelled.
	 *
	 * @see getPingInfo()
	 * @since 1.5
	 *
	 * @var array
	 */
	private $pingInfo = array( 'code' => '', 'stati' => array() );
	
	/**
	 * @since 1.5
	 * @param object $plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin	 = $plugin;
		$this->db		 = $plugin->db();
		$this->pingState = $this->db->getOption( 'pingState' );
		
		// Checks if the $pingState object has been initialised or it's corrupted.
		if (! is_a( $this->pingState, 'SiteTreePingState' ) )
			$this->pingState = new SiteTreePingState();
	}
	
	/**
	 * Sends the pings, rescedules the ones that fail and updates the @see $pingState object.
	 *
	 * This method can be invoked only by a WordPress Cron event.
	 *
	 * @since 1.5
	 * @return bool|int True if one of the pings succeed, -1 if the $pingState object is in an unknown state, false otherwise.
	 */
	public function ping() {
		if ( !( defined( 'DOING_CRON' ) && $this->pingState->isScheduled() ) )
			return false;

		$permalink = urlencode( $this->plugin->googleSitemapPermalink( 'raw' ) );
		
		$this->targets['google'] .= $permalink;
		$this->targets['bing']	 .= $permalink;
		
		// We use the current status code in the state object to determine 
		// if this is a new ping or the nth attempt of a scheduled one.
		switch ( $this->pingState->code() ) {
			case 'scheduled':
			case 'failed':
				$now		   = time();
				$bing_pinged   = $this->sendPing( 'bing' );
				$google_pinged = $this->sendPing( 'google' );
				
				if ( $google_pinged && $bing_pinged ) {
					$this->pingState->setCode( 'success' );
					$this->pingState->setTime( $now );
					$this->pingState->reset();
					
					$this->saveState();
					
					return true;
				}
				
				if ( $google_pinged ) {
					$this->pingState->setCode( 'no_bing' );
					$this->pingState->setTime( $now, 'google');
				}
				elseif ( $bing_pinged ) {
					$this->pingState->setCode( 'no_google' );
					$this->pingState->setTime( $now, 'bing' );
				}
				else
					$this->pingState->setCode( 'failed' );
				break;
			case 'no_google':
				if (! $this->sendPing( 'google' ) ) break;
				
				$this->pingState->setCode( 'success' );
				$this->pingState->setTime( time(), 'google' );
				$this->pingState->reset();
				
				$this->saveState();
				return true;
			case 'no_bing':
				if (! $this->sendPing( 'bing' ) ) break;
					
				$this->pingState->setCode( 'success' );
				$this->pingState->setTime( time(), 'bing' );
				$this->pingState->reset();
				
				$this->saveState();
				return true;
			default:
				return -1;
		}
		
		// If one or both pings fail, we try to reschedule a new one.
		$five_min_from_now = time() + 300;
		
		if ( $this->pingState->setScheduledTime( $five_min_from_now ) )
			wp_schedule_single_event( $five_min_from_now, 'sitetree_ping' );
		
		$this->saveState();
		
		return false;
	}
	
	/**
	 * Helper method: actually sends a ping.
	 *
	 * @see ping()
	 * @since 1.5
	 *
	 * @param string $target
	 * @return bool True on success, false otherwise.
	 */
	private function sendPing( $target ) {
		$response = wp_remote_get( $this->targets[$target] );
		
		return is_array( $response ) && isset( $response['response']['code'] ) && ( $response['response']['code'] === 200 );
	
	}
	
	/**
	 * Conditionally schedules a new ping and updates the @see $pingState object.
	 *
	 * @since 1.5
	 * @return bool|int True on success, -1 if the $pingState object is in an unknown state, false otherwise.
	 */
	public function schedulePing() {
		if ( !$this->db->getOption( 'ping', true ) || ( ( $current_status_code = $this->pingState->code() ) == 'scheduled' ) )
			return false;
	
		$now			= time();
		$last_ping_time = 0;
		
		// Max. one ping every 2 hours +/- 10 min
		$ping_freq_limit = 7200 + rand( -600, 600 );
		
		// If a ping has already been scheduled, we determine whether or not it must be delayed
		// (so, both google and bing can be pinged at the same time — just to not overcomplicate things unnecessarily) 
		// by checking the status code of the state object.
		if ( $this->pingState->isScheduled() ) {
			switch ( $current_status_code ) {
				case 'no_google':
					$last_ping_time = $this->pingState->time( 'bing' );
					break;
				case 'no_bing':
					$last_ping_time = $this->pingState->time( 'google' );
					break;
				case 'failed':
					$this->pingState->setCode( 'scheduled' );
					$this->pingState->reset( false );
					
					$this->saveState();
					return true;
				default:
					return -1;
			}
			
			if ( ( $now - $last_ping_time ) < $ping_freq_limit ) {
				$rescheduled_ping_time = $last_ping_time + $ping_freq_limit;
				
				$this->pingState->reset( $rescheduled_ping_time );
				
				wp_clear_scheduled_hook( 'sitetree_ping' );
				wp_schedule_single_event( $rescheduled_ping_time, 'sitetree_ping' );
			}
			else { $this->pingState->reset( false ); }
			
			$this->pingState->setCode( 'scheduled' );
			
			$this->saveState();
			
			return true;
		}
		
		// If there are no scheduled pings, we compute the date of the next one.
		switch ( $current_status_code ) {
			case 'success':
			case 'reverted':
			case 'failed':
				$last_ping_time = $this->pingState->maxTime();
				break;	
			case 'no_pings_yet':
				// To not repeat the lines of code at the end of this method, we leave $last_ping_time equal to 0, 
				// so the very first event is forced to be scheduled for the next ten minutes.
				break;
			case 'no_google':
				$last_ping_time = $this->pingState->time( 'bing' );
				break;
			case 'no_bing':
				$last_ping_time = $this->pingState->time( 'google' );
				break;
			default:
				return -1;
		}
		
		$next_ping_time = ( ( $now - $last_ping_time ) >= $ping_freq_limit ) ? ( $now + 600 ) : ( $last_ping_time + $ping_freq_limit );
		
		$this->pingState->setCode( 'scheduled' );
		$this->pingState->reset( $next_ping_time );
		
		$this->saveState();
		
		wp_schedule_single_event( $next_ping_time, 'sitetree_ping' );
		
		return true;
	}
	
	/**
	 * @since 1.5
	 * @return bool False if there aren't scheduled pings, true otherwise.
	 */
	public function unschedulePing() {
		if (! $this->pingState->isScheduled() )
			return false;
		
		// If the current status code is 'scheduled', it should be reset to the value it had 
		// before the 'schedulePing()' method set it to 'scheduled'.
		// However, by setting it to a new value ('reverted'), there's more than one advantage: 
		// this method remains short and straightforward, the structure of the pingState object 
		// doesn't get more complicated and the 'getPingInfo()' method works faster.
		//
		// Any other value of the status code is kept. This means that the $pingState object goes in
		// a 'cancelled' state = the state in which a resceduled ping is cancelled. 
		if ( $this->pingState->code() == 'scheduled' )
			$this->pingState->setCode( 'reverted' );
		
		$this->pingState->reset();
		$this->saveState();
		
		wp_clear_scheduled_hook( 'sitetree_ping' );
		
		return true;
	}
	
	/**
	 * Helper method: stores the @see $pingState object into the database.
	 *
	 * @since 1.5
	 */
	private function saveState() { $this->db->setOption( 'pingState', $this->pingState ); }
	
	/**
	 * Returns a set of information about the current state of the ping(s) that will be processed afterwards
	 * by the Dashboard Controller to update the UI elements related to the ping feature.
	 *
	 * @see $pingInfo
	 * @since 1.5
	 *
	 * @return array
	 */
	public function getPingInfo() {
		switch ( $this->pingInfo['code'] = $this->pingState->code() ) {
			case 'success':
				$this->enqueueStatus( __( 'The last pings were on %s', 'sitetree' ), $this->pingState->time() );
				break;
			case 'scheduled':
				$this->enqueueStatus( __( 'New pings have been scheduled for %s', 'sitetree' ) , $this->pingState->scheduledTime(), true );
				break;
			case 'reverted':
				$this->setupPingInfoOnRevertedOrCancelled();
				break;
			case 'no_bing':
				$info_set = $this->initPingInfoOnError(array(
					'limit_reached' => __( 'All attempts to ping Bing and Yahoo have failed. There could be a temporary connection problem '
										 . "or something &mdash; better luck next time!", 'sitetree' ),
					'nth_attempt'	=> __( 'The %s attempt to ping Bing and Yahoo has failed.', 'sitetree' )
				));
			
				if ( $info_set ) {
					$this->pingInfo['code'] = 'error';
					
					$this->enqueueStatus( __( 'Google was last pinged on %s', 'sitetree' ), $this->pingState->time( 'google' ) );
					
					// Swap the elements in the stati array to have always on top the status 
					// element related to the ping to google.
					// That is done for consistency and usability: the user will see the information 
					// about the ping to google always at the same place.
					$temp						= $this->pingInfo['stati'][0];
					$this->pingInfo['stati'][0] = $this->pingInfo['stati'][1];
					$this->pingInfo['stati'][1] = $temp;
				}
				break;
			case 'no_google':
				$info_set = $this->initPingInfoOnError(array(
					'limit_reached' => __( 'All attempts to ping Google have failed. There could be a temporary connection problem '
										 . "or something &mdash; better luck next time!", 'sitetree' ),
					'nth_attempt'	=> __( 'The %s attempt to ping Google has failed.', 'sitetree' )
				));
			
				if ( $info_set ) {
					$this->pingInfo['code'] = 'error';
					
					$this->enqueueStatus( __( 'Bing and Yahoo were last pinged on %s', 'sitetree' ), $this->pingState->time( 'bing' ) );
				}
				break;
			case 'failed':
				$this->initPingInfoOnError(array(
					'limit_reached' => __( 'All ping attempts have failed &mdash; maybe your server is a bit lazy today.<br>'
										 . "Don't worry too much. Even computers need a break every now and then.", 'sitetree' ),
					'nth_attempt'	=> __( 'The %s ping attempt has failed.', 'sitetree' )
				));
				break;
			case 'no_pings_yet':
				$this->pingInfo['code'] = '';
				$this->enqueueStatus( __( "No pings sent, yet &mdash; it's time to be great! The world is awaiting your next essay.", 'sitetree' ) );
				break;
			default:
				$this->pingInfo['code'] = 'error';
				$this->enqueueStatus(
					__( 'Could not retrieve any information. Please, try to deactivate and then reactivate the ping functionality.', 'sitetree' )
				);
				break;
		}
		
		return $this->pingInfo;
	}
	
	/**
	 * Helper method: sets up the @see $pingInfo array in case the status code is 'reverted'
	 * (the state in which the $pingState object is placed when scheduled pings are cancelled)
	 * or the state of the $pingState object is 'cancelled' (a resceduled ping has been cancelled).
	 *
	 * @see getPingInfo()
	 * @since 1.5
	 */
	private function setupPingInfoOnRevertedOrCancelled() {
		$last_ping_time = $this->pingState->time();
				
		if ( $last_ping_time == 0 ) {
			$this->pingInfo['code'] = '';
			$this->enqueueStatus( __( "No pings sent, yet &mdash; it's time to be great! The world is awaiting your next essay.", 'sitetree' ) );
		}	
		elseif ( $last_ping_time == 1 ) {
			$this->pingInfo['code'] = 'success';
			
			$last_ping_time = $this->pingState->time( 'google' );
			
			if ( $last_ping_time == 0 )
				$this->enqueueStatus( __( "Google hasn't been pinged, yet. Relax! There is no hurry.", 'sitetree' ) );
			else
				$this->enqueueStatus( __( 'Google was last pinged on %s', 'sitetree' ), $last_ping_time );
				
			$last_ping_time = $this->pingState->time( 'bing' );
			
			if ( $last_ping_time == 0 )
				$this->enqueueStatus( __( "Bing and Yahoo have not been pinged, yet. Relax! There is no hurry.", 'sitetree' ) );
			else
				$this->enqueueStatus( __( 'Bing and Yahoo were last pinged on %s', 'sitetree' ), $last_ping_time );
		}
		else {
			$this->pingInfo['code'] = 'success';
			$this->enqueueStatus( __( 'The last pings were on %s', 'sitetree' ), $last_ping_time );
		}
	}
	
	/**
	 * Helper method: initialises the @see $pingInfo array in case the status code is 'no_google'
	 * or 'no_bing' or 'failed' and a currently scheduled event hasn't been cancelled by the user.
	 * The setup of $pingInfo is eventually finalised by @see getPingInfo().
	 *
	 * @see getPingInfo()
	 * @since 1.5
	 *
	 * @param array $messages
	 * @return bool True if the $pingInfo array has been initialised, false if has been completelly set up.
	 */
	private function initPingInfoOnError( $messages ) {
		// If the $scheduledTime is exactly equal to 0, it does mean that the $pingState object has
		// been reset by the @see unschedulePing method = the user has cancelled a scheduled event.
		if ( $this->pingState->scheduledTime() === 0 ) {
			$this->setupPingInfoOnRevertedOrCancelled();
			
			return false;
		}
		
		if ( $this->pingState->limitReached() )
			$this->enqueueStatus( $messages['limit_reached'] );
		else {
			$minutes_from_now = ceil( ( $this->pingState->scheduledTime() - time() ) / 60 );
			$message		  = sprintf( $messages['nth_attempt'], SiteTreeUtilities::numberToOrdinal( $this->pingState->numOfAttempts() ) );
			$message		 .= ' <strong>';
			
			if ( $minutes_from_now > 1 )
				$message .= sprintf( __( 'I will try again in about %d minutes.', 'sitetree' ), $minutes_from_now );
			else
				$message .= __( "I'm going to try again.", 'sitetree' );
			
			$message .= '</strong>';
			
			$this->enqueueStatus( $message, null, true ); 
		}
		
		return true;
	}
	
	/**
	 * Helper method: it pushes a new status element into the @see $pingInfo['stati'] array.
	 *
	 * @see getPingInfo()
	 * @since 1.5
	 *
	 * @param string $message
	 * @param int $timestamp
	 * @param bool $can_be_cancelled
	 */
	private function enqueueStatus( $message, $timestamp = null, $can_be_cancelled = false ) {
		// Checking $timestamp against 'null' forces the date-formatting block
		// to be executed even if $timestamp is zero. So, if an error occurs, no %s is displayed.
		if ( $timestamp !== null ) {
			if ( $can_be_cancelled )
				$message = sprintf( $message, '<strong>' . SiteTreeUtilities::localDate( $timestamp ) . '</strong>' );
			else
				$message = sprintf( $message, '<em>' .SiteTreeUtilities::localDate( $timestamp ) . '</em>' );
		}
		
		$this->pingInfo['stati'][] = array( 'can_be_cancelled' => $can_be_cancelled, 'message' => $message );
	}
}
?>