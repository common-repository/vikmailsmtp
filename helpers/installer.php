<?php
/**
 * @package     VikMailSMTP
 * @subpackage  helpers.installer
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2018 E4J s.r.l. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
* Installer Class for Vik Mail SMTP
*/
final class VikMailSMTPInstaller
{
	/**
	 * Removes the options upon disinstallation
	 * 
	 * @return 	void
	 */
	public static function uninstall()
	{
		delete_option('vikmailsmtp_host');
		delete_option('vikmailsmtp_port');
		delete_option('vikmailsmtp_security');
		delete_option('vikmailsmtp_auth');
		delete_option('vikmailsmtp_sender');
		delete_option('vikmailsmtp_user');
		delete_option('vikmailsmtp_pass');
		delete_option('vikmailsmtp_skipwizard');
		delete_option('vikmailsmtp_logging');
		delete_option('vikmailsmtp_logs');
	}

	/**
	 * Resets all the plugin settings
	 * 
	 * @return 	void
	 */
	public static function reset()
	{
		update_option('vikmailsmtp_host', null);
		update_option('vikmailsmtp_port', null);
		update_option('vikmailsmtp_security', null);
		update_option('vikmailsmtp_auth', null);
		update_option('vikmailsmtp_sender', null);
		update_option('vikmailsmtp_user', null);
		update_option('vikmailsmtp_pass', null);
		update_option('vikmailsmtp_skipwizard', null);
		// do not reset the logs or logging here as there is an apposite button to do it
	}
}
