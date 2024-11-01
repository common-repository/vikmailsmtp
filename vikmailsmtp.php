<?php
/*
Plugin Name:  VikMailSMTP
Plugin URI:   https://wordpress.org/plugins/vikmailsmtp/
Description:  Let your WP website send email messages through your preferred SMTP service to ensure 100% delivery
Version:      1.0.2
Author:       E4J s.r.l.
Author URI:   https://vikwp.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  vikmailsmtp
Domain Path:  /languages
*/

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';

// handle uninstall
register_uninstall_hook(__FILE__, array('VikMailSMTPInstaller', 'uninstall'));

// register init for assets in the head
add_action('init', array('VikMailSMTPBuilder', 'loadAssets'));

// register the sub-menu page
add_action('admin_menu', function() {
	
	// get hook and add options page
	global $vikmailsmtp_hook;
	$vikmailsmtp_hook = add_options_page( 
		'Vik Mail SMTP',
		'SMTP Mailer',
		'manage_options',
		'vikmailsmtp',
		array('VikMailSMTPBuilder', 'addOptionPage')
	);

	// register Help Tab native button
	add_action('load-'.$vikmailsmtp_hook, function() {
		// get current page
		$page = VikMailSMTPBuilder::getCurrentPage();

		get_current_screen()->add_help_tab(array(
			'id' => 'settings',
			'title' => __('SMTP Settings', 'vikmailsmtp'),
			'content' => '<p>'.VikMailSMTPTranslate::get('help-settings').'</p>',
		));
		get_current_screen()->add_help_tab(array(
			'id' => 'logs',
			'title' => __('Mail Logs', 'vikmailsmtp'),
			'content' => '<p>'.VikMailSMTPTranslate::get('help-logs').'.</p>',
		));
		get_current_screen()->add_help_tab(array(
			'id' => 'gmail',
			'title' => __('Gmail SMTP', 'vikmailsmtp'),
			'content' => '<p>'.VikMailSMTPTranslate::get('help-gmail').'.</p>',
		));
		get_current_screen()->set_help_sidebar(
			'<p><b>'.__('For more information:', 'vikmailsmtp').'</b></p>' . 
			'<a href="https://vikwp.com" target="_blank">https://vikwp.com</a>'
		);
	});

});

// hook to the PHPMailer init action
add_action('phpmailer_init', function(&$phpmailer) {
	
	if (!$phpmailer instanceof PHPMailer) {
		// safety check
		return $phpmailer;
	}

	// start the loggins class
	VikMailSMTPLogger::init();

	// gather the plugin parameters
	$params = array(
		'host' 		=> get_option('vikmailsmtp_host', null),
		'port' 		=> get_option('vikmailsmtp_port', null),
		'security' 	=> get_option('vikmailsmtp_security', null),
		'auth' 		=> get_option('vikmailsmtp_auth', null),
		'sender' 	=> get_option('vikmailsmtp_sender', ''),
		'user' 		=> VikMailSMTPCypher::decode(get_option('vikmailsmtp_user', null)),
		'pass' 		=> VikMailSMTPCypher::decode(get_option('vikmailsmtp_pass', null))
	);

	foreach ($params as $v) {
		if (is_null($v)) {
			// do not interfere if the plugin parameters are null
			return $phpmailer;
		}
	}

	// the plugin may be used to just override the From Name and From Address
	$use_smtp = !empty($params['host']);

	if ($use_smtp) {
		// Tell PHPMailer to use SMTP
		$phpmailer->isSMTP();

		// Set the hostname of the mail server
		$phpmailer->Host = $params['host'];

		// Set the SMTP port number
		$phpmailer->Port = (int)$params['port'];

		// Whether to use SMTP authentication
		$phpmailer->SMTPAuth = ((int)$params['auth'] > 0);

		if ($phpmailer->SMTPAuth) {
			// Username to use for SMTP authentication
			$phpmailer->Username = $params['user'];

			//Password to use for SMTP authentication
			$phpmailer->Password = $params['pass'];
		}

		if ($params['security'] != 'none') {
			$phpmailer->SMTPSecure = $params['security'];
		} else {
			$phpmailer->SMTPAutoTLS = false;
		}
	}

	// Whether to force the sender address
	if (!empty($params['sender'])) {
		$sender = VikMailSMTPSettings::getSender($params['sender']);
		if (count($sender)) {
			// force the From Address value
			$phpmailer->From = $sender[0];
			
			// force the From Name value if different than email address
			if (isset($sender[1]) && $sender[1] != $sender[0]) {
				$phpmailer->FromName = $sender[1];
			}
		}
	}

	// invoke the loggins class
	VikMailSMTPLogger::store('info', array(
		'subject' 	=> $phpmailer->Subject,
		'recipient' => implode(', ', array_keys($phpmailer->getAllRecipientAddresses())),
		'sender' 	=> $phpmailer->FromName . ' &lt;' . $phpmailer->From . '&gt;'
	));

	return $phpmailer;
});

/**
 * Handler to attach PHPMailer errors to the queue of system messages.
 */
add_action('wp_mail_failed', function($error) {
	
	// invoke the loggins class
	VikMailSMTPLogger::store('error', $error->get_error_message());

	if ($_POST && isset($_POST['testmail'])) {
		// during the test email function, we let displayMessage() render the error
		VikMailSMTPBuilder::displayMessage($error->get_error_message(), 'error');
		return;
	}

	if (class_exists('JFactory')) {
		// if any of the VikWP Plugins is installed, we let enqueueMessage() render the error
		JFactory::getApplication()->enqueueMessage($error->get_error_message(), 'error');
	} else {
		// in case of redirects after the email sending function, this message will not be displayed
		VikMailSMTPBuilder::displayMessage($error->get_error_message(), 'error');
	}

});

/**
 * Debug the arguments passed to the wp_mail function (useful for checking the headers)
 */
/**
add_filter('wp_mail', function() {
	if (class_exists('JFactory')) {
		JFactory::getApplication()->enqueueMessage('<pre>'.print_r(func_get_args(), true).'</pre><br/>');
	}
});
 */
