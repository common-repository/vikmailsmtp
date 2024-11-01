=== Vik Mail SMTP - Wizard and Logs ===
Contributors: e4jvikwp
Tags: vik, mail, smtp, mail logs, phpmailer, hotmail, windows live, gmail, yahoo
Requires at least: 4.7
Tested up to: 6.4
Stable tag: trunk
Requires PHP: 5.4.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Power up the email sending functions of your website with Vik Mail SMTP. Custom SMTP connections and headers (From Name, From Address) with Logs.

== Description ==

= Powerfully Easy Mailer Plugin =

Setting up an SMTP service for your website will improve all your mail delivery functions.

Whether you use a third party email address like Gmail, Hotmail, Windows Live, Yahoo etc.. or your own domain address, Vik Mail SMTP will help you set up the SMTP service for all the outgoing email messages.

Detailed **logs** will help you check if any errors occur while sending an email message.

Vik Mail SMTP is completely **free**, and it's the perfect assitant of all the other **VikWP Plugins**.

Visit [VikWP.com](https://vikwp.com/) for more details.

= Some of its features =

* Custom configuration of SMTP or PHP Mail service
* Override the default "From Name" and "From Address" for all messages
* Configuration Wizard for Gmail, Windows Live, Hotmail, Yahoo
* Configuration Helper for private SMTP services (like your domain)
* Mail Logs to keep track of successful and erroneous email messages

== Installation ==

= Installation through the WordPress Plugins Browser =

* Search for "Vik Mail SMTP" in the Add Plugin section of your website back-end
* Install the Plugin by clicking on the apposite button
* Activate the Plugin by using the apposite activation button
* A link to access and configure Vik Mail SMTP will be visible in the WP Settings menu

== Frequently Asked Questions ==

= Is this plugin compatible with other plugins that send email? =

Yes of course. Vik Mail SMTP overrides the WordPress native email sending function through the PHPMailer Class. Therefore, any native plugin that sends email messages through the WordPress functions will be affected, theorically every plugin.

= Can I configure the SMTP service of my own domain? =

We actually suggest to use your own SMTP service. For example, if you already have an email address like *example@yourwebsite.com* then you definitely have an SMTP service for the outgoing messages that you can use also for your website.

= Why is this plugin different from the others? =

There are probably many other plugins for WP that can do similar things to Vik Mail SMTP, but this plugin is compatible with the **VikWP Framework** used by all our other plugins. For those who like to hear technical stuff, the VikWP Framework allows to add system messages to the session queue (and much much more), like a success or failure notice that could be displayed right after sending an email message with a redirection to a different page. Other similar plugins that manipulate the SMTP and email sending functions could never support such functions. In short, this plugin can use the libraries of other VikWP plugins if they are installed.

= Does it support XOAUTH2 for Gmail? =

No, sorry. We decided not to include the support for XOAUTH2 with Gmail due to its many limitations. First of all, the SMTP of Gmail can still be used via TLS by so called *less secure apps* like PHPMailer of WordPress, you just need to allow *less secure apps* to access the service from your Gmail account. PHPMailer is actually not a *less secure app*, but because the code is open source, Google will never consider it as *secure*. The protocol XOAUTH2 is only supported by certain versions of PHPMailer and WordPress, and it requires specific configuration settings at server level. Most hosting companies disable such functions for other security reasons, so the chances you have to be able to use XOAUTH2 on your domain are very low. If you need to use the SMTP service of Gmail, you should probably just grant the permissions for PHPMailer from your Gmail account.

== Screenshots ==

1. SMTP Settings and Mail Headers: define a custom SMTP connection or custom headers for the email From Name and From Address information.
2. Configuration Wizard: let the assistant guess your SMTP configuration.
3. Mail Logs: keep track of the email sending functions and easily identify a success or a failure.

== Changelog ==

= 1.0.2 =
* Logs implementation

= 1.0.1 =
* Added the Configuration Wizard

= 1.0 =
* First stable release of the Plugin
