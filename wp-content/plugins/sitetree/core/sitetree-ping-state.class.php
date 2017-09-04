<?php
/**
 * @package SiteTree
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */
 
 
/**
 * The only instance of this class is used as a persistent object that incapsulates
 * all the relevant information about the current (or latest) state of one or more ping
 * events.
 *
 * @since 1.5
 */
final class SiteTreePingState {
	/**
	 * Status code that identifies the overall state of the instance but not necessarily
	 * that of the current (or latest) event.
	 *
	 * The value of this property doesn't affect the internal behaviour of the instance
	 * but it's a key element to the @see SiteTreePingController and its workflow.
	 * 
	 * Because of the purpose of the class, the instance doesn't have any knowledge about
	 * the possibile values that this property can assume. 
	 *
	 * @since 1.5
	 * @var string
	 */
	private $code = 'no_pings_yet';
	
	/**
	 * Array of timestamps related to when each ping was sent.
	 * They are equal if all the pings succeed during the same scheduled event, 
	 * different otherwise.
	 *
	 * @since 1.5
	 * @var array
	 */
	private $time = array( 'google' => 0, 'bing' => 0 );
	
	/**
	 * Timestamp of when the next scheduled (or rescheduled) ping(s) will occur.
	 *
	 * @since 1.5
	 * @var int
	 */
	private $scheduledTime = 0;
	
	/**
	 * Internal counter: number of times a failed ping (or failed pings) has been rescheduled.
	 * Used to interrupt the rescheduling if a ping keeps failing repeatedly.
	 *
	 * @since 1.5
	 * @var int
	 */
	private $attempts = 0;
	
	/**
	 * @since 1.5
	 * @return string
	 */
	public function code() { return $this->code; }
	
	/**
	 * @since 1.5
	 * @param string $code
	 */
	public function setCode( $code ) { $this->code = $code; }
	
	/**
	 * Returns the element in the @see $time array pointed by the optional argument
	 * — if provided —, one of the elements if they're exactly equal, one otherwise.
	 *
	 * @since 1.5
	 *
	 * @param string $key
	 * @return int
	 */
	public function time( $key = '' ) {
		if ( $key )
			return isset( $this->time[$key] ) ? (int) $this->time[$key] : 0;
		
		if ( isset( $this->time['google'], $this->time['bing'] ) && ( $this->time['google'] === $this->time['bing'] ) )
			return (int) $this->time['google'];
		
		return 1;
	}
	
	/**
	 * @since 1.5
	 * return int
	 */
	public function maxTime() { return (int) max( $this->time ); }
	
	/**
	 * If the second argument is provided, the pointed element in the @see $time array
	 * is set to $timestamp. Otherwise, both elements are set to $timestamp.
	 *
	 * @since 1.5
	 *
	 * @param int $timestamp
	 * @param string $key
	 */
	public function setTime( $timestamp, $key = '' ) {
		if ( $key )
			$this->time[$key] = $timestamp;
		else
			$this->time['google'] = $this->time['bing'] = $timestamp;
	}
	
	/**
	 * Increments the @see $attempts counter and set the value of @see $scheduledTime
	 * if the $attempts hasn't reached its max. value.
	 *
	 * @since 1.5
	 *
	 * @param int $timestamp
	 * @return bool Whether or not the scheduled time has been set.
	 */
	public function setScheduledTime( $timestamp ) {
		if ( $time_set =  ( ++$this->attempts < 3 ) )
			$this->scheduledTime = $timestamp;
		
		return $time_set;
	}
	
	/**
	 * @since 1.5
	 * @return int
	 */
	public function scheduledTime() { return (int) $this->scheduledTime; }
	
	/**
	 * Checks if the state of the object is 'scheduled' (there are one or more pings scheduled).
	 *
	 * If this method returns true, it doesn't necessarily mean that the value of @see $code is 'scheduled'.
	 * Because when a failed ping is resceduled, $code keeps its value: failed, no_google or no_bing.
	 *
	 * @since 1.5
	 * return bool
	 */
	public function isScheduled() { return $this->scheduledTime && ( $this->attempts < 3 ); }
	
	/**
	 * @since 1.5
	 * @return int
	 */
	public function numOfAttempts() { return (int) $this->attempts; }
	
	/**
	 * Checks if the @see $attempts property has reached the maximum value.
	 *
	 * @since 1.5
	 * return bool
	 */
	public function limitReached() { return $this->attempts >= 3; }
	
	/**
	 * Resets the @see $attempts counter and the @see $scheduledTime property.
	 *
	 * The optional argument is used to reset $scheduledTime to a value other
	 * than zero or to override its reset by setting the argument to false;
	 *
	 * @since 1.5
	 *
	 * @param int|bool $scheduled_time
	 */
	public function reset( $scheduled_time = 0 ) {
		$this->attempts	= 0;
		
		if ( $scheduled_time !== false )
			$this->scheduledTime = $scheduled_time;
	}
}
?>