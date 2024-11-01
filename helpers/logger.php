<?php
/**
 * @package     VikMailSMTP
 * @subpackage  helpers.logger
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2018 E4J s.r.l. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
* Logger Class for Vik Mail SMTP
*/
final class VikMailSMTPLogger
{
	/**
	 * @var  int 	logging identifier
	 */
	private static $id = 0;

	/**
	 * @var  int 	process identifier
	 */
	private static $process = 0;

	/**
	 * @var  array 	current logging pool
	 */
	private static $pool = array();

	/**
	 * Starts a logging identifier. Called every time
	 * the action 'phpmailer_init' takes place.
	 *
	 * @return 	void
	 */
	public static function init()
	{
		self::$id++;
		self::$process = uniqid();
	}

	/**
	 * Checks whether the logging is enabled
	 * 
	 * @return 	boolean
	 */
	public static function enabled()
	{
		// cache the get_option call
		static $enabled = null;

		if (!is_null($enabled)) {
			return ($enabled > 0);
		}

		$enabled = (int)get_option('vikmailsmtp_logging', 0);

		return ($enabled > 0);
	}

	/**
	 * Stores a log for the email sending result
	 *
	 * @param 	string 	$type 		the type of log to store (error, info)
	 * @param 	mixed 	$content 	the content of the log, either an array of
	 *								information or a string with the message
	 * 
	 * @return 	boolean
	 */
	public static function store($type, $content)
	{
		if (!self::enabled()) {
			return false;
		}

		// get the current log key identifier
		$key_id = self::$process . self::$id;

		// prepare log data
		$data = new stdClass;
		$data->id = $key_id;
		$data->date = date('Y-m-d H:i:s');
		$data->log = array();
		$log = new stdClass;
		$log->type = $type;
		$log->content = $content;
		array_push($data->log, $log);

		if (isset(self::$pool[$key_id])) {
			// append to existing log
			return self::appendLog($key_id, $data);
		}

		// prepend new log
		return self::addLog($key_id, $data);
	}

	/**
	 * Returns the current list of logs.
	 * 
	 * @return 	array
	 */
	public static function getLogsList()
	{
		$list = @json_decode(get_option('vikmailsmtp_logs', null));

		return is_array($list) ? $list : array();
	}

	/**
	 * Flushes all the logs by making them empty.
	 * 
	 * @return 	void
	 */
	public static function flushLogs()
	{
		update_option('vikmailsmtp_logs', '');
	}

	/**
	 * Prepends a log to the current log array.
	 *
	 * @param 	object 	$log 	the object with the log data
	 * 
	 * @return 	boolean
	 */
	private static function addLog($key_id, $log)
	{
		// get the current logs list
		$current = self::getLogsList();

		// prepend log
		array_unshift($current, $log);

		// add log to buffer
		self::$pool[$key_id] = 1;

		// check if number of logs exceeds the limit
		if (count($current) > VIKMAILSMTP_MAXLOGS) {
			$current = array_slice($current, 0, VIKMAILSMTP_MAXLOGS);
		}

		// update logs in DB
		update_option('vikmailsmtp_logs', json_encode($current));

		return true;
	}

	/**
	 * Appends a log to an existing one in the current log array.
	 *
	 * @param 	int 	$key 	the key of the previously log stored
	 * @param 	object 	$log 	the object with the log data
	 * 
	 * @return 	boolean
	 */
	private static function appendLog($key, $log)
	{
		// get the current logs list
		$current = self::getLogsList();

		// seek the parent log
		$parent_found = false;
		foreach ($current as $k => $v) {
			if ($v->id == $key) {
				$parent_found = true;
				array_push($current[$k]->log, $log->log[0]);
				break;
			}
		}

		// if no parent, prepend a new log
		if (!$parent_found) {
			// prepend log
			array_unshift($current, $log);
		}

		// update logs in DB
		update_option('vikmailsmtp_logs', json_encode($current));

		return true;
	}
}
