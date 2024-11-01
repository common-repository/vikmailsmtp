<?php
/**
 * @package     VikMailSMTP
 * @subpackage  helpers.settings
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2018 E4J s.r.l. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
* Settings Calculator Class for Vik Mail SMTP
*/
final class VikMailSMTPSettings
{
	/**
	 * Calculates the settings from the Wizard selection and stops
	 * the wizard from being displayed again in case of success.
	 *
	 * @param 	string 		$provider 	the name/type of the SMTP provider
	 * @param 	string 		$email 		the email address specified
	 * @param 	string 		$pwd 		the password for the email address
	 * @param 	boolean 	$raise_err 	whether to display an error in case of failure and redirect
	 * 
	 * @return 	boolean
	 *
	 * @uses 	VikMailSMTPBuilder::displayMessage()
	 * @uses 	VikMailSMTPCypher::encode()
	 */
	public static function calculate($provider, $email, $pwd, $raise_err = false)
	{
		// make sure the values are not empty
		if (empty($provider) || empty($email) || empty($pwd)) {
			if ($raise_err) {
				VikMailSMTPBuilder::displayMessage('missing_data_calc', 'error');
			}
			return false;
		}

		// check the syntax of the email
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			if ($raise_err) {
				VikMailSMTPBuilder::displayMessage('invalid_email', 'error');
			}
			return false;
		}

		// work on the plain email before encoding it
		$mail_parts = explode('@', $email);
		$sender = self::guessFrom($email);

		// encode data
		$email = VikMailSMTPCypher::encode($email);
		$pwd = VikMailSMTPCypher::encode($pwd);

		// attempt to calculate the settings from the provider
		switch ($provider) {
			case 'gmail':
				// update the plugin settings
				update_option('vikmailsmtp_host', 'smtp.gmail.com');
				update_option('vikmailsmtp_port', '587');
				update_option('vikmailsmtp_security', 'tls');
				update_option('vikmailsmtp_auth', 1);
				update_option('vikmailsmtp_user', $email);
				update_option('vikmailsmtp_pass', $pwd);
				update_option('vikmailsmtp_sender', $sender);
				break;
			case 'yahoo':
				// update the plugin settings
				update_option('vikmailsmtp_host', 'smtp.mail.'.$mail_parts[1]);
				update_option('vikmailsmtp_port', '465');
				update_option('vikmailsmtp_security', 'ssl');
				update_option('vikmailsmtp_auth', 1);
				update_option('vikmailsmtp_user', $email);
				update_option('vikmailsmtp_pass', $pwd);
				update_option('vikmailsmtp_sender', $sender);
				break;
			case 'hotmail':
				// update the plugin settings
				update_option('vikmailsmtp_host', 'smtp.live.com');
				update_option('vikmailsmtp_port', '587');
				update_option('vikmailsmtp_security', 'tls');
				update_option('vikmailsmtp_auth', 1);
				update_option('vikmailsmtp_user', $email);
				update_option('vikmailsmtp_pass', $pwd);
				update_option('vikmailsmtp_sender', $sender);
				break;
			default:
				// try guessing the hostname from the email address
				$guess_host = 'mail.'.$mail_parts[1];
				// update the plugin settings
				update_option('vikmailsmtp_host', $guess_host);
				update_option('vikmailsmtp_port', '25');
				update_option('vikmailsmtp_security', 'none');
				update_option('vikmailsmtp_auth', 1);
				update_option('vikmailsmtp_user', $email);
				update_option('vikmailsmtp_pass', $pwd);
				update_option('vikmailsmtp_sender', '');
				break;
		}

		// enable logging by default when using the wizard
		update_option('vikmailsmtp_logging', 1);

		// stop the wizard from being displayed again
		update_option('vikmailsmtp_skipwizard', 1);
		
		return true;
	}

	/**
	 * Attempts to guess a valid sender information
	 * from the email address passed for authentication.
	 * This method does not check that the passed email address
	 * is syntactically valid, so this should be done before.
	 *
	 * @param 	string 		$email 		the email address to authenticate
	 * 
	 * @return 	string 		the sender string in the format: Your Name <your@email.com> or an empty string.
	 */
	public static function guessFrom($email)
	{
		if (empty($email)) {
			return '';
		}

		$mail_parts = explode('@', (string)$email);
		if (empty($mail_parts[0]) || count($mail_parts) != 2) {
			return '';
		}

		if (strpos($mail_parts[0], '.') === false) {
			// hard to guess the name, return just the email as sender 'From' address
			return $email;
		}

		// compose the sender name
		$name_parts = explode('.', $mail_parts[0]);
		// remove everything but letters from the array
		$name_parts = preg_replace('/[^a-z]/i', '', $name_parts);
		// upper case first letter of each word
		$name_parts = array_map('ucfirst', $name_parts);

		return implode(' ', $name_parts).' &lt;'.$email.'&gt;';
	}

	/**
	 * Gets the sender From Name and From Address values
	 * from the current settings or passed value.
	 *
	 * @param 	[string] 	$sender 	the string of the forced sender in the format:
	 * 									Your Name <your@email.com>
	 * 
	 * @return 	array 		2-value array where the 0th is the From Address and the 1st is the From Name.
	 * 						May return an empty array or 1-value with just the email address for From Address.
	 */
	public static function getSender($sender = null)
	{
		$forced = array();

		if (is_null($sender)) {
			$sender = get_option('vikmailsmtp_sender', '');
		}
		
		if (empty($sender)) {
			// let WP or whoever calls wp_mail set the sender information without overriding
			return $forced;
		}

		// use regex to get email address inside the string
		preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", (string)$sender, $matches);
		if (count($matches[0])) {
			// push From Address ('From') by taking only the first match
			array_push($forced, $matches[0][0]);

			// clean string to get the From Name
			$sender = str_replace(array($matches[0][0], '<', '>', '&lt;', '&gt;', '  '), array('', '', '', '', '', ' '), $sender);
			$sender = trim($sender);

			// push From Name ('FromName') if not empty
			if (strlen($sender) > 1) {
				array_push($forced, $sender);
			}
		}

		return $forced;
	}
}
