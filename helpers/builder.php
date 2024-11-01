<?php
/**
 * @package     VikMailSMTP
 * @subpackage  helpers.builder
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2018 E4J s.r.l. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
* Builder Class for Vik Mail SMTP
*/
final class VikMailSMTPBuilder
{
	/**
	 * @var  params pool
	 */
	private static $vars = array();

	/**
	 * Prints the content of the sub-menu page
	 * 
	 * @uses 	skipWizard()
	 * @uses 	paramsEmpty()
	 * @uses 	registerVar()
	 * @uses 	loadLayout()
	 */
	public static function addOptionPage()
	{
		if (!current_user_can('manage_options')) {
			wp_die(
				'<h1>' . __('Forbidden', 'vikmailsmtp') . '</h1>' .
				'<p>' . __('You are not authorized to access this resource.', 'vikmailsmtp') . '</p>',
				403
			);
		}

		// calculate and register the page to display
		$page = self::getCurrentPage();
		self::registerVar('page', $page);

		// load menu and page layout files
		self::loadLayout('menu');
		self::loadLayout($page);
	}

	/**
	 * Returns the name of the layout page to display
	 * 
	 * @return 	string
	 */
	public static function getCurrentPage()
	{
		$page = 'settings';
		if (isset($_REQUEST['p'])) {
			$page = basename($_REQUEST['p']);
		} elseif (!self::skipWizard() && self::paramsEmpty()) {
			$page = 'wizard';
		}

		return $page;
	}

	/**
	 * Displays a system message
	 * 
	 * @param 	string 		$message 	the message identifier to display
	 * @param 	string 		$type 		either success, warning, error, info
	 * 
	 * @uses 	registerVar()
	 * @uses 	loadLayout()
	 */
	public static function displayMessage($message, $type = 'success')
	{
		// make sure the type is lowercased
		$type = strtolower($type);

		// register variables
		self::registerVar('message', $message);
		self::registerVar('type', $type);

		// render the message
		self::loadLayout('message');
	}

	/**
	 * Gets the site administrator email address
	 * 
	 * @return 	string
	 */
	public static function getAdminEmail()
	{
		return get_option('admin_email', '');
	}

	/**
	 * Gets the current user email address
	 * 
	 * @return 	string
	 */
	public static function getUserEmail()
	{
		$current_user = wp_get_current_user();

		if (!$current_user instanceof WP_User)
		{
			return '';
		}

		return $current_user->user_email;
	}

	/**
	 * Sends a test email with the current configuration
	 * 
	 * @return 	void
	 */
	public static function sendTestEmail()
	{
		// get the test email message
		if (!headers_sent()) {
			ob_start();
			self::loadLayout('testemail');
			$body = ob_get_contents();
			ob_end_clean();
		} else {
			$body = __("This is a test email message to verify the current SMTP settings.\nThe message was sent from ".site_url(), 'vikmailsmtp');
		}

		// send the message to the current user email address
		$to = self::getUserEmail();
		$sitename = get_option('blogname', 'Vik Mail SMTP');
		$sender = self::getAdminEmail();
		$subject = sprintf(__('Test Email for %s', 'vikmailsmtp'), $sitename);
		$headers = array(
			'Content-Type: text/plain; charset=UTF-8',
			"From: {$sitename} <{$sender}>",
		);
		$result = wp_mail($to, $subject, $body, $headers);

		// parse result
		if ($result) {
			$message = __('Test email sent successfully!', 'vikmailsmtp');
			$type = 'success';
		} else {
			$message = __('Could not send the test email.', 'vikmailsmtp');
			$type = 'error';
		}

		// register variables
		self::registerVar('message', $message);
		self::registerVar('type', $type);

		// render the message
		self::loadLayout('message');
	}

	/**
	 * Loads the necessary CSS files
	 * 
	 * @return 	void
	 */
	public static function loadAssets()
	{
		static $loaded = 0;

		if ($loaded) {
			return;
		}

		if (!isset($_REQUEST['page']) || $_REQUEST['page'] != 'vikmailsmtp') {
			return;
		}

		// we do not need to check if headers were sent as the plugin runs within the wp-admin section
		$url = plugin_dir_url(VIKMAILSMTP_BASEFILE) . 'assets/css/vikmailsmtp.css';
		$id = md5($url);
		wp_register_style($id, $url);
		wp_enqueue_style($id);

		$loaded = 1;
	}

	/**
	 * Checks whether the params are empty and never saved
	 * 
	 * @return 	boolean
	 */
	public static function paramsEmpty()
	{
		return is_null(get_option('vikmailsmtp_host', null));
	}

	/**
	 * Checks whether the wizard should be skipped
	 * 
	 * @return 	boolean
	 */
	public static function skipWizard()
	{
		return ((int)get_option('vikmailsmtp_skipwizard', 0) > 0);
	}

	/**
	 * Loads a layout file
	 * 
	 * @param 	string 		$name 		the name of the layout file
	 */
	private static function loadLayout($name)
	{
		if (is_file(VIKMAILSMTP_LAYOUTS . DIRECTORY_SEPARATOR . $name . '.php')) {
			include VIKMAILSMTP_LAYOUTS . DIRECTORY_SEPARATOR . $name . '.php';
		}
	}

	/**
	 * Registers variables for the layout files
	 * 
	 * @param 	string 		$key 	the name of the variable
	 * @param 	mixed 		$value 	the value of the variable
	 */
	private static function registerVar($key, $value)
	{
		self::$vars[$key] = $value;
	}

	/**
	 * Gets a value from the variables array
	 * 
	 * @param 	string 		$key 	the name of the variable
	 *
	 * @return  mixed 		the value of the variable requested
	 */
	private static function getVar($key)
	{
		return self::$vars[$key];
	}
}
