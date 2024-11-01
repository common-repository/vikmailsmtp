<?php
/**
 * @package     VikMailSMTP
 * @subpackage  helpers.translate
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2018 E4J s.r.l. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Translator Class for Vik Mail SMTP.
 * Long sentences are placed here for comfort.
 */
final class VikMailSMTPTranslate
{
	/**
	 * Returns the translated string given the key.
	 *
	 * @param 	string 	$key 	the key identifier of the translation
	 * 
	 * @return 	string
	 */
	public static function get($key)
	{
		$value = '';

		switch ($key) {
			case 'help-settings':
				$value = __('In order to be able to send email messages through your preferred <em>Simple Mail Transfer Protocol</em>, you should make sure to have defined the correct connection settings. If you are trying to configure the SMTP of your personal email address, then your hosting company may help you get to know the right authentication settings. Otherwise, if your email service is offered by a third party company like Google, Yahoo, Windows Live etc.. you can look up on the Internet which settings are required to use their SMTP service. If you already read your messages through a Mail Client like Mail for Mac, Thunderbird or Outlook, then you have already configured your SMTP service in your computer. You can check the account details directly from your Mail Client to see what you need to set up in this Plugin for your website.', 'vikmailsmtp');
				break;
			case 'help-logs':
				$value = __('The Logs function can be turned on and off from the page Settings. When it\'s enabled and the SMTP settings are set up, every time an email message is sent from your website, no matter fow who, the Plugin will register a log with some details of the message and the result. This will help you keep track of the email messages that get sent from your website, and it will be easy to identify any kind of issue.', 'vikmailsmtp');
				break;
			case 'help-gmail':
				$value = __('Gmail is probably one of the most popular email services. If you are already using it for your personal email address, then it\'s probably good to use it for sending messages from your website to your visitors. However, this service is probably not suited for sending a large amount of messages per day, or they may block your SMTP service.<br /><b>Important Notice:</b> Google recently changed their preferred method of authenticating for Gmail SMTP. In fact, SSL and TLS were deprecated for &quot;non-secure applications&quot; by opting for <em>XOAUTH2</em>. You should know that your WordPress website sends every email message through the PHP &quot;application&quot; called PHPMailer. This is considered as a non-secure application by Google. PHPMailer is actually secure, but Google cannot whitelist a PHP application where everyone could alter its source code.<br />We decided to not include the support for the <em>XOAUTH2</em> protocol as it requires a specific version of PHP, WordPress and PHPMailer. Also, most web-servers do not support this protocol and so the configuration of this service could be unusable for many users. <b>The solution</b> is to configure this Plugin by using the suggested settings, and then you should <b>allow less secure apps</b> to authenticate directly from your Gmail account. Just send a test email after configuring the Gmail SMTP service in the Plugin and check your mailbox. You will find a message from Google with the instructions for allowing less secure applications to authenticate. The TLS protocol is just as a secure as XOAUTH2, but Google simply opted for that only protocol. However, if you grant permissions to your application (website) to authenticate, the problem will be resolved.', 'vikmailsmtp');
				break;
		}

		return $value;
	}
}
